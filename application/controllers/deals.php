<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tasks Controller
 *
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB $db
 * @property User_model $User_model
 */
class deals extends CI_Controller {
	private $client_id;
    private $client_secret;
    private $refresh_token;
    private $access_token;
	
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Task_model');
		$this->load->model('Task_photo_model');
		$this->load->model('Access_token_model');
		$this->load->model('Payment_model');
		$this->load->model('Task_quote_model');
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['url', 'json_input', 'form']);
        $this->load->helper('security');
		$this->load->helper('token_validate');
        $this->output->set_content_type('application/json');

		$this->client_id = '1000.0V519TDSRC8AYUHXMU2SYCAGN9UP3L';
        $this->client_secret = '0f58eea7da1716ec409099063b8f7e42218854e242';
        $this->refresh_token = '1000.4348d34c1a96e813abe7ff21bfc4a04b.0fd6bc7aeb9d83178d3b5f7f893744c8';
    }

	// private $zoho_api_url = 'https://www.zohoapis.com/crm/v2/Deals/';

	private function get_access_token() {
        $this->access_token = $this->Access_token_model->get_access_token();
        return $this->access_token;
    }

	// Method to validate API key
	private function validate_api_key() {
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

		return $api_key && validate_api_key($api_key);
	}

	public function update($id) {
        // Validate API key
        if (!$this->validate_api_key()) {
            $this->output->set_status_header(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
            return;
        }
    
        // Detect Content-Type and retrieve data accordingly
        $content_type = $this->input->server('CONTENT_TYPE');
        $data = strpos($content_type, 'application/json') !== false ? json_decode($this->input->raw_input_stream, true) : $this->input->post();
    
        if ($data === null) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
            return;
        }
    
        // Validation rules
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('remark', 'Remark');
        $this->form_validation->set_rules('service_charge', 'Service Charge');
        $this->form_validation->set_rules('status', 'Status', 'in_list[Pending,Site Visit,Close to Lost,Proposal,Omitted,Close to Won,Proposal-Omitted,Big Project]');
    
        // Add validation for item details only when the status is "Proposal"
		if (isset($data['status']) && $data['status'] === 'Proposal') {
			$this->form_validation->set_rules('project_name', 'Project Name', 'required');
			$this->form_validation->set_rules('subject', 'Subject');
			$this->form_validation->set_rules('terms_of_payment', 'Terms of Payment', 'required');
			$this->form_validation->set_rules('product_id', 'product Id', 'required');
			$this->form_validation->set_rules('product_name', 'product Name', 'required');
			// $this->form_validation->set_rules('uom', 'U.O.M', 'required');
			$this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric');
		}
    
        if ($this->form_validation->run() === FALSE) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'error' => validation_errors()]);
            return;
        }
    
        // Fetch the zoho_crm_id using the task ID
        $zoho_crm_id = $this->Task_model->get_zoho_crm_id($id);
    
        if (!$zoho_crm_id) {
            $this->output->set_status_header(404); // Not Found
            echo json_encode(['success' => false, 'error' => 'Task ID not found.']);
            return;
        }
        
    
        $task_data = [
            'remark' => $data['remark'] ?? null,
            'service_charge' => $data['service_charge'] ?? null,
            'zoho_crm_id' => $zoho_crm_id,
            'status' => $data['status'] ?? null
        ];
    
        // Begin transaction
        $this->db->trans_start();
    
        if ($this->Task_model->update_task($id, $task_data)) {
            $response = ['success' => true, 'message' => 'Task updated successfully'];
    
            // Handle remark and photo updates
            if (!empty($data['remark']) || isset($_FILES['photos'])) {
                $upload_results = isset($_FILES['photos']) ? $this->process_file_uploads($id, $_FILES['photos'], $zoho_crm_id) : [];
    
                if (isset($upload_results['error'])) {
                    $this->db->trans_rollback();
                    $this->output->set_status_header(400);
                    echo json_encode(['success' => false, 'error' => $upload_results['error']]);
                    return;
                }
    
                $remark_update_result = $this->update_remark_in_zoho($zoho_crm_id, $data['remark'] ?? null, $upload_results['photos'] ?? []);
    
                if (isset($remark_update_result['error'])) {
                    $this->db->trans_rollback();
                    $this->output->set_status_header(500);
                    echo json_encode(['success' => false, 'error' => $remark_update_result['error']]);
                    return;
                }
    
                $response['remark_update'] = 'Remark and files updated successfully in Zoho CRM';
                $response['photos'] = $upload_results['photos'] ?? null;
            }
    
            // Handle status and service charge updates
            if (isset($data['status']) || isset($data['service_charge'])) {
                $update_result = $this->update_deal_in_zoho($zoho_crm_id, $data['status'] ?? null, $data['service_charge'] ?? null);
    
                if (isset($update_result['error'])) {
                    $this->db->trans_rollback();
                    $this->output->set_status_header(500);
                    echo json_encode(['success' => false, 'error' => $update_result['error']]);
                    return;
                }
    
                if (isset($data['status'])) {
                    $response['deal_status'] = 'Deal status updated successfully';
                }
    
                if (isset($data['service_charge'])) {
                    $response['service_charge'] = 'Service charge updated successfully';
                }
    
                // Add service charge to payments if status is 'Close to Won'
                if ($data['status'] === 'Close to Won' && isset($data['service_charge'])) {
                    $assigned_to_id = $this->Task_model->get_assigned_to_id($zoho_crm_id);
    
                    $payment_data = [
                        'user_id' => $assigned_to_id ?? null,
                        'amount' => $data['service_charge'] ?? null,
                        'task_id' => $id ?? null
                    ];
    
                    // Check if a payment with the same task_id already exists
                    if ($this->Payment_model->payment_exists($id)) {
                        // Update the existing payment
                        if (!$this->Payment_model->update_payment_by_task_id($id, $payment_data)) {
                            $this->db->trans_rollback();
                            $this->output->set_status_header(500);
                            echo json_encode(['success' => false, 'error' => 'Failed to update payment.']);
                            return;
                        }
                    } else {
                        // Insert a new payment record
                        if (!$this->Payment_model->add_payment($payment_data)) {
                            $this->db->trans_rollback();
                            $this->output->set_status_header(500);
                            echo json_encode(['success' => false, 'error' => 'Failed to add payment.']);
                            return;
                        }
                    }
    
                    $response['payment'] = 'Payment added successfully.';
                }
            }
            
            // Add proposal details to Zoho CRM when status is "Proposal"
			if (isset($data['status']) && $data['status'] === 'Proposal') {
				$proposal_data = [
					'item_name' => $data['item_name'] ?? null,
					'uom' => $data['uom'] ?? null,
					'quantity' => $data['quantity'] ?? null,
					'unit_price' => $data['service_charge'] ?? null,
					'product_id' => $data['product_id'] ?? null,
					'product_name' => $data['product_name'] ?? null,
					'subject' => $data['project_name'] ?? 'Default Subject',
					'project' => $data['project_name'] ?? 'na',                
					'terms_of_payment' => $data['terms_of_payment'] ?? 'na', 
					'specification' => 'na',    
					'general_exclusion' => 'na', 
					'brand' => 'na',      
					'warranty' => 'na',
					'delivery' => 'na',
					'valid_until' => $data['valid_until'] ?? null, // Optional Valid Until
				];
			
				// Call function to add proposal items to Zoho CRM
				$proposal_update_result = $this->add_proposal_to_zoho($zoho_crm_id, $proposal_data, $id);
			
				if (isset($proposal_update_result['error'])) {
					$this->db->trans_rollback();
					$this->output->set_status_header(500);
					echo json_encode(['success' => false, 'error' => $proposal_update_result['error']]);
					return;
				}
			
				$response['proposal'] = 'Proposal details added successfully to Zoho CRM';

                if (isset($update_result['error'])) {
                    $this->db->trans_rollback();
                    $this->output->set_status_header(500);
                    echo json_encode(['success' => false, 'error' => $update_result['error']]);
                    return;
                }
			}
    
            // Commit transaction
            $this->db->trans_complete();
    
            if ($this->db->trans_status() === FALSE) {
                $this->output->set_status_header(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update task.']);
                return;
            }
    
            $this->output->set_status_header(200);
            echo json_encode($response);
        } else {
            $this->db->trans_rollback();
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'error' => 'Failed to update task.']);
        }
    }

    private function add_proposal_to_zoho($deal_id, $proposal_data, $id) {
		// Initialize valid_until_date
		$valid_until_date = null;
	
		// Check if valid_until exists and is a valid date
		if (!empty($proposal_data['valid_until'])) {
			try {
				$date = new DateTime($proposal_data['valid_until']);
				$valid_until_date = $date->format('Y-m-d');  // Format the date as 'YYYY-MM-DD'
			} catch (Exception $e) {
				log_message('error', 'Invalid date format for valid_until: ' . $proposal_data['valid_until']);
				return ['error' => 'Invalid date format for valid_until.'];
			}
		}
	
		$data = json_encode([
			'data' => [
				[
					'Deal_Name' => $deal_id,
					'Subject' => $proposal_data['subject'] ?? 'Default Subject',
					'Project' => $proposal_data['project'] ?? 'na',
					'Terms_of_Payment' => $proposal_data['terms_of_payment'] ?? 'na',
					'Specification' => $proposal_data['specification'] ?? 'na',
					'General_Exclusion' => $proposal_data['general_exclusion'] ?? 'na',
					'Brand' => $proposal_data['brand'] ?? 'na',
					'Warranty' => $proposal_data['warranty'] ?? 'na',
					'Delivery' => $proposal_data['delivery'] ?? 'na',
					'Valid_Till' => $valid_until_date ?? null,
					'Product_Details' => [
						[
							'product' => $proposal_data['product_id'],  // Use the Product_Id from Zoho CRM
							'quantity' => (float) $proposal_data['quantity'],  // Ensure this is passed as a number
							'list_price' => (float) $proposal_data['unit_price'],  // Use list_price instead of Unit_Price
							'UOM' => $proposal_data['uom']  // Ensure UOM is a valid UOM option in Zoho CRM
						]
					]
				]
			]
		]);        
	
		// Fetch the access token from the model
		$this->access_token = $this->get_access_token();
	
		if (!$this->access_token) {
			return ['error' => 'Access token not found.'];
		}
	
		// Set headers
		$headers = [
			'Content-Type: application/json',
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
	
		// Debugging
		log_message('debug', 'Headers: ' . print_r($headers, true));
		log_message('debug', 'Post Data: ' . $data);
	
		// Make API request to Zoho CRM
		$response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2/Quotes",
			$headers,
			$data,
			'POST'
		);
	
		// Decode the response body
		$response_body = json_decode($response['body'], true);
	
		// Debugging: Log the full response for inspection
		log_message('debug', 'Zoho CRM proposal Response: ' . print_r($response_body, true));
		log_message('debug', 'HTTP Code: ' . $response['http_code']);
	
		// Handle 401 Unauthorized error by refreshing the token
		if ($response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				// Update the token in the headers after refreshing
				$this->access_token = $this->get_access_token();
				$headers[1] = "Authorization: Zoho-oauthtoken " . $this->access_token;
				$response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2/Quotes",
					$headers,
					$data,
					'POST'
				);
	
				// Decode and log the response again after the retry
				$response_body = json_decode($response['body'], true);
				log_message('debug', 'Zoho CRM proposal Response after retry: ' . print_r($response_body, true));
			}
		}
	
		// Now handle the insertion of task quote
		if (isset($response_body['data'][0]['details']['id'])) {
			$quote_id = $response_body['data'][0]['details']['id'];
			
	        // Step 1: Fetch the created quote to get its Quote_No
			$quote_details_response = $this->execute_curl_request(
				"https://www.zohoapis.com/crm/v2/Quotes/$quote_id",
				$headers,
				null,
				'GET'
			);
			
			// Step 2: Extract Quote_No from the response
			$quote_details = json_decode($quote_details_response['body'], true);
			$quote_number = $quote_details['data'][0]['Quote_No'] ?? null;

			if (!$quote_number) {
				log_message('debug', 'Quote number missing from Zoho CRM response: ' . print_r($quote_details, true));
				$this->output->set_status_header(500);
				echo json_encode(['success' => false, 'error' => 'Quote number not found in Zoho CRM response.']);
				return;
			}
			
			$data = [
				// 'id' => $id,
				'quote_id' => $quote_id,
				'quote_number' => $quote_number,
				'project_name' => $proposal_data['project'],
				'terms_of_payment' => $proposal_data['terms_of_payment'],
				'product_id' => $proposal_data['product_id'],
				'product_name' => $proposal_data['product_name'],
				'quantity' => $proposal_data['quantity'],
				'valid_until' => $proposal_data['valid_until'],
			];
	
			// Assuming you want to insert into 'task_quotes' table
			if (!$this->Task_model->save_proposal_data($data, $id)) {
				$this->db->trans_rollback();
				$this->output->set_status_header(500);
				echo json_encode(['success' => false, 'error' => 'Failed to update Task_quote_model.']);
				return;
			}
	
			$response_body['task_quote'] = 'Task quote created successfully.';
			$this->output->set_status_header(200);
			// echo json_encode($response_body);
		} else {
			// Log additional debug information to understand why the id might be missing
			log_message('debug', 'Full response for error handling: ' . print_r($response_body, true));
	
			$this->output->set_status_header(500);
			echo json_encode(['success' => false, 'error' => 'Quote ID not found in Zoho CRM response.']);
		}
	}
	
	public function fetch_all_products() {
		// Fetch the access token
		$this->access_token = $this->get_access_token();
	
		if (!$this->access_token) {
			return $this->output
				->set_status_header(500)
				->set_output(json_encode(['success' => false, 'error' => 'Access token not found.']));
		}
	
		// Set the API URL for fetching all products
		$url = "https://www.zohoapis.com/crm/v2/Products";
	
		// Set the headers (removed Content-Type for GET)
		$headers = [
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
	
		// Log the request details
		log_message('debug', 'Requesting Zoho CRM Products with headers: ' . print_r($headers, true));
	
		// Make the request to Zoho CRM (no body for GET request)
		$response = $this->execute_curl_request($url, $headers, null, 'GET');
		$response_body = json_decode($response['body'], true);
	
		// Log response for debugging
		log_message('debug', 'Zoho CRM Products Response: ' . print_r($response_body, true));
	
		// Handle 401 Unauthorized error by refreshing the token
		if ($response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				// Update the token in the headers after refreshing
				$this->access_token = $this->get_access_token();
				$headers[0] = "Authorization: Zoho-oauthtoken " . $this->access_token;
	
				// Retry the request after refreshing the token
				$response = $this->execute_curl_request($url, $headers, null, 'GET');
				$response_body = json_decode($response['body'], true);
	
				// Log response after retry
				log_message('debug', 'Zoho CRM Products Response after retry: ' . print_r($response_body, true));
			} else {
				return $this->output
					->set_status_header(500)
					->set_output(json_encode(['success' => false, 'error' => 'Failed to refresh access token.']));
			}
		}
	
		// Check if we got products data
		if (isset($response_body['data'])) {
			$products = [];
	
			// Extract specific product details
			foreach ($response_body['data'] as $product) {
				$products[] = [
					'id' => $product['id'],
					'name' => $product['Product_Name'],
					'qty_in_stock' => $product['Qty_in_Stock'],
					'owner' => $product['Owner']['name'],
					'modified_time' => $product['Modified_Time']
				];
			}
	
			// Return the formatted product data
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => true, 'products' => $products]));
		} else {
			// Handle error
			return $this->output
				->set_status_header(500)
				->set_output(json_encode(['success' => false, 'error' => 'Failed to fetch products from Zoho CRM.']));
		}
	}
	
	private function update_deal_in_zoho($deal_id, $status = null, $service_charge = null) {
        $url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}";
        $data = [
            "data" => [
                []
            ]
        ];
    
        if ($status !== null) {
            $data['data'][0]['VZ_App_Status'] = $status;
    
            // Set the stage based on the VZ_App_Status
            switch ($status) {
                case 'Close to Won':
                    $data['data'][0]['Stage'] = 'Closed Won';
                    break;
                case 'Proposal-Omitted':
                case 'Omitted':
                    $data['data'][0]['Stage'] = 'Closed Lost';
                    break;
                case 'Proposal':
                    $data['data'][0]['Stage'] = 'Proposal/Price Quote';
                    break;
                case 'Site Visit':
					$data['data'][0]['Stage'] = 'Site Visit';
					break;
				case 'Pending':
					$data['data'][0]['Stage'] = 'Qualification';
					break;
            }
        }
    
        if ($service_charge !== null) {
            $data['data'][0]['Amount'] = $service_charge;
        }
    
        // Remove the empty data array if no fields are provided
        if (empty($data['data'][0])) {
            return ['error' => 'No fields to update.'];
        }
    
        // Fetch the access token from the model
        $this->access_token = $this->get_access_token();
    
        if (!$this->access_token) {
            return ['error' => 'Access token not found.'];
        }
    
        $headers = [
            "Authorization: Zoho-oauthtoken " . $this->access_token,
            "Content-Type: application/json"
        ];
    
        $response = $this->execute_curl_request($url, $headers, json_encode($data), 'PUT');
    
        if ($response['http_code'] == 401) {
            if ($this->refresh_access_token()) {
                // Update the token in the headers after refreshing
                $this->access_token = $this->get_access_token();
                $headers[0] = "Authorization: Zoho-oauthtoken " . $this->access_token;
                $response = $this->execute_curl_request($url, $headers, json_encode($data), 'PUT');
            }
        }
    
        // Log the full response for debugging
        log_message('debug', 'Zoho CRM Response: ' . print_r($response, true));
    
        if ($response['http_code'] == 200 || $response['http_code'] == 201) {
            return ['success' => true];
        }
    
        return ['error' => 'Failed to update deal in Zoho CRM. Response: ' . print_r($response, true)];
    }
			
	
	private function process_file_uploads($task_id, $files, $zoho_crm_id) {
		$upload_path = './assets/photos/';
		
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, TRUE);
		}
	
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'gif|jpg|jpeg|webp|png';
		$config['max_size'] = 5120;
	
		$this->load->library('upload', $config);
	
		$uploaded_files = [];
		$existing_files = [];
	
		// Fetch existing file names from the database
		$existing_photos = $this->Task_photo_model->get_photos_by_task_id($task_id);
		foreach ($existing_photos as $photo) {
			$existing_files[] = $photo['photo'];
		}
	
		log_message('debug', 'Existing files in DB: ' . print_r($existing_files, true));
	
		for ($i = 0; $i < count($files['name']); $i++) {
			$_FILES['photo'] = [
				'name' => $files['name'][$i],
				'type' => $files['type'][$i],
				'tmp_name' => $files['tmp_name'][$i],
				'error' => $files['error'][$i],
				'size' => $files['size'][$i],
			];
	
			$this->upload->initialize($config);
	
			if (!$this->upload->do_upload('photo')) {
				log_message('error', 'Upload failed: ' . $this->upload->display_errors());
				return ['error' => $this->upload->display_errors()];
			} else {
				$upload_data = $this->upload->data();
				$file_name = $upload_data['file_name'];
	
				log_message('debug', 'Checking file: ' . $file_name . ' against existing files.');
	
				if (in_array($file_name, $existing_files)) {
					log_message('debug', 'File already exists and is skipped: ' . $file_name);
					continue;
				}
	
				$photo_data = [
					'task_id' => $task_id,
					'photo' => $file_name
				];
	
				if (!$this->Task_photo_model->add_photo($photo_data)) {
					log_message('error', 'Failed to save photo details.');
					return ['error' => 'Failed to save photo details.'];
				}
	
				$uploaded_files[] = $file_name; // Store just the file name
			}
		}
	
		return ['photos' => $uploaded_files];
	}
	
	private function update_remark_in_zoho($deal_id, $remark, $file_paths = []) {
        $url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Notes";
        
        $prefix = 'https://app.zenerom.com/voltronix/assets/photos/'; // Prefix URL
        
        $notes = [];
        
        // Add remark note
        if (!empty($remark)) {
            $notes[] = [
                "Note_Title" => "Remark Update",
                "Note_Content" => $remark
            ];
        }
        
        // Add file paths note if provided
        if (!empty($file_paths)) {
            // Add prefix to each file path
            $file_paths_with_prefix = array_map(function($file_path) use ($prefix) {
                return $prefix . $file_path;
            }, $file_paths);
            
            $file_paths_content = "Files:\n" . implode("\n", $file_paths_with_prefix);
            $notes[] = [
                "Note_Title" => "Files Uploaded",
                "Note_Content" => $file_paths_content
            ];
        }
        
        // Proceed with the API call only if we have notes to send
        if (empty($notes)) {
            return ['error' => 'No notes to update.'];
        }
        
        $data = ["data" => $notes];
    
        // Fetch the access token from the model
        $this->access_token = $this->get_access_token();
    
        if (!$this->access_token) {
            return ['error' => 'Access token not found.'];
        }
        
        $headers = [
            "Authorization: Zoho-oauthtoken " . $this->access_token,
            "Content-Type: application/json"
        ];
        
        // Send the API request
        $response = $this->execute_curl_request($url, $headers, json_encode($data), 'POST');
        
        // Handle token expiration and retry the request
        if ($response['http_code'] == 401) {
            if ($this->refresh_access_token()) {
                $this->access_token = $this->get_access_token(); 
                $headers[0] = "Authorization: Zoho-oauthtoken " . $this->access_token;
                $response = $this->execute_curl_request($url, $headers, json_encode($data), 'POST');
            }
        }
        
        // Check for success
        if ($response['http_code'] == 200 || $response['http_code'] == 201) {
            return ['success' => true];
        }
        
        return ['error' => 'Failed to update notes in Zoho CRM. Response: ' . print_r($response, true)];
    }
	

	private function execute_curl_request($url, $headers, $data = null, $method = 'POST') {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
		// Set method type
		if ($method === 'PUT') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			if ($data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
		} elseif ($method === 'GET') {
			// No need to set CURLOPT_POST or CURLOPT_POSTFIELDS for GET
			curl_setopt($ch, CURLOPT_HTTPGET, true);
		} else {
			curl_setopt($ch, CURLOPT_POST, true);
			if ($data) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
		}
	
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
		if (curl_errno($ch)) {
			log_message('error', 'Curl error: ' . curl_error($ch));
		}
	
		curl_close($ch);
	
		return [
			'body' => $response,
			'http_code' => $http_code
		];
	}			

	private function refresh_access_token() {
        $url = "https://accounts.zoho.com/oauth/v2/token";

        // Replace with your stored credentials
        $data = [
            'refresh_token' => $this->refresh_token,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (isset($response_data['access_token'])) {
            // Save the new access token in the database
            $this->Access_token_model->save_access_token($response_data['access_token']);
            $this->access_token = $response_data['access_token'];
            return $this->access_token;
        } else {
            // Log or handle the error
            log_message('error', 'Failed to refresh access token: ' . $response);
            return false;
        }
    }
						
	private function upload_file_to_zoho($deal_id, $file_paths) {
		$url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Attachments";
		$headers = [
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
	
		$responses = [];
	
		foreach ($file_paths as $file_path) {
			if (!is_string($file_path) || !file_exists($file_path)) {
				$responses[] = ['error' => 'Invalid file path: ' . $file_path];
				continue;
			}
	
			$mime_type = mime_content_type($file_path);
	
			$post_fields = [
				'file' => new CURLFile($file_path, $mime_type)
			];
	
			$response = $this->execute_curl_request($url, $headers, $post_fields, 'POST');
	
			if ($response['http_code'] == 401) {
				if ($this->refresh_access_token()) {
					$headers[0] = "Authorization: Zoho-oauthtoken " . $this->access_token;
					$response = $this->execute_curl_request($url, $headers, $post_fields, 'POST');
				}
			}
	
			if ($response['http_code'] == 200) {
				$response_data = json_decode($response['body'], true);
				$responses[] = [
					'file_path' => $file_path,
					'response' => $response_data
				];
			} else {
				$responses[] = [
					'file_path' => $file_path,
					'error' => 'Failed to upload file',
					'response' => $response['body'],
					'http_code' => $response['http_code']
				];
			}
		}
	
		return $responses;
	}	

	public function fetch_templates() {
		$access_token = '1000.30acdd5cbe34f76a4f3e4ff26da69b1f.40672d410c3ceb371c0ecb6cf553b634'; 

		$url = "https://www.zohoapis.com/crm/v2/settings/templates?type=inventory";

		// Initialize cURL
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Set up authorization headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			'Authorization: Zoho-oauthtoken ' . $access_token
		]);

		// Execute the cURL request
		$response = curl_exec($ch);

		// Check for errors
		if ($response === false) {
			$error = curl_error($ch);
			echo 'cURL error: ' . $error;
		} else {
			$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($http_code === 200) {
				// Successfully fetched the templates
				echo 'Response: ' . $response;
			} else {
				// Handle non-200 responses
				echo 'HTTP Status Code: ' . $http_code;
				echo 'Response: ' . $response;
			}
		}

		// Close the cURL session
		curl_close($ch);
	}
	
	
}

?>