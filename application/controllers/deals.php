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

	private function send_request($url, $headers, $payload) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        log_message('error', "Zoho API Request - Status Code: $status_code, URL: $url, Response: $response");
        return ['status_code' => $status_code, 'response' => json_decode($response, true)];
    }

    public function create_lead_in_zoho() {
        // Detect Content-Type and retrieve data
        $content_type = $this->input->server('CONTENT_TYPE');
        $data = strpos($content_type, 'application/json') !== false ? json_decode($this->input->raw_input_stream, true) : $this->input->post();

        if ($data === null) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
            return;
        }

        // Validate required fields
		$this->form_validation->set_data($data); // Pass data for validation
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('VZ_app_user_id', 'VZ App user id', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->output->set_status_header(400);
            echo json_encode(['errors' => $this->form_validation->error_array()]);
            return;
        }

        $zoho_data = [
            'First_Name' =>  $data['first_name'],
            'Last_Name' => $data['last_name'],
            'Account_Name' => $data['account_name'] ?? null,
            'Description' => $data['complaint_info'] ?? null,
            'Stage' => 'Qualification',
            'Customer_Name' => $data['customer_name'] ?? null,
            'Email' => $data['customer_email'] ?? null,
            'Phone' => $data['phone'] ?? null,
            'Mobile' => $data['mobile'] ?? null,
            'Owner' => '5653678000000401001',
            'Company' => $data['company_name'] ?? 'CompanyName',
            "VZ_app_user" => ["id" => $data['VZ_app_user_id']],
        ];

        $access_token = $this->get_access_token();
        $headers = ["Authorization: Bearer $access_token", "Content-Type: application/json"];
        $payload = json_encode(['data' => [$zoho_data]]);
        $url = "https://www.zohoapis.com/crm/v2/Leads";

        $response = $this->send_request($url, $headers, $payload);

        if ($response['status_code'] === 201 && isset($response['response']['data'][0]['details']['id'])) {
            $zoho_lead_id = $response['response']['data'][0]['details']['id'];
            $conversion_result = $this->convert_lead_to_deal($zoho_lead_id, $data);
            echo json_encode($conversion_result);
        } elseif ($response['status_code'] === 401) {
            $access_token = $this->refresh_access_token();
            if ($access_token) $this->create_lead_in_zoho();
            else echo json_encode(['error' => 'Failed to retrieve or refresh access token']);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'Failed to create lead in Zoho CRM', 'details' => $response['response']]);
        }
    }

    public function convert_lead_to_deal($lead_id, $data) {
        $access_token = $this->get_access_token();
        $headers = ["Authorization: Bearer $access_token", "Content-Type: application/json"];
        $url = "https://www.zohoapis.com/crm/v2/Leads/{$lead_id}/actions/convert";
        $payload_data = [
            [
                "Deals" => [
                    "Deal_Name" => $data['first_name'] . " " . $data['last_name'],
                    "Stage" => "Qualification",
                    "Assign_Department1" => "VOLTRONIX CONTRACTING LLC",
                    "VZ_app_user" => ["id" => $data['VZ_app_user_id']],
                    "VZ_App_Status" => "Pending",
                    'Description' => $data['complaint_info'] ?? null,
                    'Email' => $data['customer_email'] ?? null,
                    'Phone' => $data['phone'] ?? null,
                    'Mobile' => $data['mobile'] ?? null,
                    'Owner' => '5653678000000401001',
                    'Company' => $data['company_name'] ?? 'CompanyName',
                ],
                "overwrite" => true
            ]
        ];
        $payload = json_encode(['data' => $payload_data]);

        $response = $this->send_request($url, $headers, $payload);
		log_message('debug', 'Conversion Response: ' . print_r($response, true));
        if (in_array($response['status_code'], [200, 201])) {
            $zoho_crm_id = $response['response']['data'][0]['Deals'];
            return $this->handle_post_conversion_tasks($zoho_crm_id, $data);
        } elseif (isset($response['response']['data'][0]['code']) && $response['response']['data'][0]['code'] === 'DUPLICATE_DATA') {
            $contact_id = $response['response']['data'][0]['details']['id'];
            return $this->create_deal_for_existing_contact($contact_id, $data);
        } elseif ($response['status_code'] === 401) {
			// Attempt to refresh the token
            if ($this->refresh_access_token()) {
                // Update the token in the headers after refreshing
                $this->access_token = $this->get_access_token();
                $headers = [
                    'Content-Type: application/json',
                    "Authorization: Zoho-oauthtoken " . $this->access_token
                ];
        
                // Retry the contact creation request with the refreshed token
                $contact_response = $this->execute_curl_request(
                    "https://www.zohoapis.com/crm/v2/Leads/{$lead_id}/actions/convert",
                    $headers,
                    $data,
                    'POST'
                );
        
                // Decode the response again after retry
                $contact_data_response = json_decode($contact_response['body'], true);
            } else {
                // If the token refresh fails, log an error and return
                log_message('error', 'Failed to refresh access token for contact creation.');
                return ['error' => 'Failed to refresh access token.'];
            }
        } else {
            return ['success' => false, 'error' => 'Failed to create deal in Zoho CRM', 'details' => $response['response']];
        }
    }

    private function handle_post_conversion_tasks($zoho_crm_id, $data) {
        if (isset($_FILES['photos'])) {
            $upload_results = $this->process_file_uploads($zoho_crm_id, $_FILES['photos'], $data['VZ_app_user_id']);

            if (isset($upload_results['error'])) {
                $this->output->set_status_header(400);
                return ['success' => false, 'error' => $upload_results['error']];
            }

            $remark = $data['remark'] ?? null;
            $remark_update_result = $this->update_remark_in_zoho($zoho_crm_id, $remark, $upload_results['photos'] ?? []);

            if (isset($remark_update_result['error'])) {
                $this->output->set_status_header(500);
                return ['success' => false, 'error' => $remark_update_result['error']];
            }

            return ['success' => true, 'message' => 'Deal created and remark/files updated successfully in Zoho CRM'];
        }
        return ['success' => true, 'message' => 'Deal created successfully in Zoho CRM', 'deal_id' => $zoho_crm_id];
    }

    public function create_deal_for_existing_contact($contact_id, $data) {
        $access_token = $this->get_access_token();
        $url = "https://www.zohoapis.com/crm/v2/Deals";
        $headers = ["Authorization: Bearer $access_token", "Content-Type: application/json"];
        
        $payload_data = [
            [
                "Contact_Name" => ["id" => $contact_id],
                "Deal_Name" => $data['first_name'] . " " . $data['last_name'],
                "Stage" => "Qualification",
                "Assign_Department1" => "VOLTRONIX CONTRACTING LLC",
                "VZ_app_user" => ["id" => $data['VZ_app_user_id']],
                "VZ_App_Status" => "Pending",
                'Description' => $data['complaint_info'] ?? null,
                'Email' => $data['customer_email'] ?? null,
                'Phone' => $data['phone'] ?? null,
                'Mobile' => $data['mobile'] ?? null,
                'Owner' => '5653678000000401001',
                'Company' => $data['company_name'] ?? 'CompanyName',
            ]
        ];

        $payload = json_encode(['data' => $payload_data]);
        $response = $this->send_request($url, $headers, $payload);

        return $this->handle_post_conversion_tasks($response['response']['data'][0]['details']['id'] ?? null, $data);
    }

	// public function create_deal_in_zoho() {
    //     // Detect Content-Type and retrieve data accordingly
    //     $content_type = $this->input->server('CONTENT_TYPE');
    //     $data = strpos($content_type, 'application/json') !== false ? json_decode($this->input->raw_input_stream, true) : $this->input->post();
    
    //     if ($data === null) {
    //         $this->output->set_status_header(400);
    //         echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
    //         return;
    //     }
    
    //     // Set validation rules for required fields
    //     $this->form_validation->set_rules('owner_id', 'Owner ID', 'required');
    //     $this->form_validation->set_rules('deal_name', 'Deal Name', 'required');
    //     $this->form_validation->set_rules('VZ_app_user_id', 'VZ App user id', 'required');
    
    //     // Optional fields (no 'required' rule, just validation if present)
    //     $this->form_validation->set_rules('remark', 'Remark');
    //     $this->form_validation->set_rules('service_charge', 'Amount');
    //     $this->form_validation->set_rules('complaint_info', 'Description');
    //     $this->form_validation->set_rules('account_name', 'Account Name');
    //     $this->form_validation->set_rules('email', 'Email');
    //     $this->form_validation->set_rules('status', 'Status');
    //     $this->form_validation->set_rules('customer_name', 'Customer Name');
    //     $this->form_validation->set_rules('customer_email', 'Email');
    //     $this->form_validation->set_rules('phone', 'Phone');
    //     $this->form_validation->set_rules('mobile', 'Mobile');
    
    //     if ($this->form_validation->run() == FALSE) {
    //         $this->output->set_status_header(400);
    //         echo json_encode(['errors' => $this->form_validation->error_array()]);
    //     } else {
    //         // Prepare the data to be sent to Zoho CRM
    //         $zoho_data = [
    //             'Deal_Name' => $data['deal_name'],
    //             'Owner' => $data['owner_id'],
    //             'Account_Name' => $data['account_name'] ?? null,
    //             'Description' => $data['complaint_info'] ?? null,
    //             'Stage' => 'Qualification',
    //             'Customer_Name' => $data['customer_name'] ?? null,
    //             'Email' => $data['customer_email'] ?? null,
    //             'Phone' => $data['phone'] ?? null,
    //             'Mobile' => $data['mobile'] ?? null,
    //             'Address' => [
    //                 'street' => $data['street'] ?? null,
    //                 'city' => $data['city'] ?? null,
    //                 'state' => $data['state'] ?? null,
    //                 'country' => $data['country'] ?? null,
    //                 'zip_code' => $data['zip_code'] ?? null
    //             ],
    //             "VZ_app_user" => [
    //                 "id" => $data['VZ_app_user_id']
    //             ],
    //             "VZ_App_Status" => "Pending"
    //         ];
    
    //         // Get or refresh access token
    //         $access_token = $this->get_access_token();
    
    //         if (!$access_token) {
    //             $this->output->set_status_header(500);
    //             echo json_encode(['error' => 'Failed to retrieve access token']);
    //             return;
    //         }
    
    //         // Send the data to Zoho CRM
    //         $url = "https://www.zohoapis.com/crm/v2/Deals";
    //         $headers = [
    //             "Authorization: Bearer $access_token",
    //             "Content-Type: application/json"
    //         ];
    //         $payload = json_encode(['data' => [$zoho_data]]);
    //         $ch = curl_init($url);
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //         curl_setopt($ch, CURLOPT_POST, true);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    //         $response = curl_exec($ch);
    //         $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         curl_close($ch);
    
    //         // Check if the request was successful
    //         if ($status_code === 201) {
    //             // print_r($response);
    //             // die();
    //             $zoho_crm_id = json_decode($response, true)['data'][0]['details']['id'];
    
    //             // Handle file uploads if remark is present or files are uploaded
    //             if (isset($_FILES['photos'])) {
    //                 $upload_results = isset($_FILES['photos']) ? $this->process_file_uploads($zoho_crm_id, $_FILES['photos'], $data['VZ_app_user_id']) : [];
    
    //                 if (isset($upload_results['error'])) {
    //                     $this->output->set_status_header(400);
    //                     echo json_encode(['success' => false, 'error' => $upload_results['error']]);
    //                     return;
    //                 }
    
    //                 // Update remark in Zoho
    //                 $remark_update_result = $this->update_remark_in_zoho($zoho_crm_id, $data['remark'] ?? null, $upload_results['photos'] ?? []);
    
    //                 if (isset($remark_update_result['error'])) {
    //                     $this->output->set_status_header(500);
    //                     echo json_encode(['success' => false, 'error' => $remark_update_result['error']]);
    //                     return;
    //                 }
    
    //                 echo json_encode(['success' => 'Deal created and remark/files updated successfully in Zoho CRM']);
    //             } else {
    //                 echo json_encode(['success' => 'Deal created successfully in Zoho CRM']);
    //             }
    //         } elseif ($status_code === 401) {
    //             // Handle expired access token and retry
    //             $this->retry_create_deal($data, $payload, $url);
    //         } else {
    //             $this->output->set_status_header(500);
    //             echo json_encode(['error' => 'Failed to create deal in Zoho CRM', 'details' => json_decode($response, true)]);
    //         }
    //     }
    // }

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
			$this->form_validation->set_rules('subject', 'Subject', 'required');
			$this->form_validation->set_rules('kind_attention', 'Kind attention', 'required');
			$this->form_validation->set_rules('terms_of_payment', 'Terms of Payment', 'required');
			$this->form_validation->set_rules('product_id', 'product Id', 'required');
			$this->form_validation->set_rules('product_name', 'product Name', 'required');
			$this->form_validation->set_rules('uom', 'U.O.M', 'required');
			$this->form_validation->set_rules('quantity', 'Quantity', 'required|numeric');
			$this->form_validation->set_rules('general_exclusion', 'general exclusion');
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
					'uom' => $data['uom'] ?? null,
					'kind_attention' => $data['kind_attention'] ?? null,
					'subject' => $data['subject'] ?? 'Default Subject',
					'project' => $data['project_name'] ?? 'na',                
					'terms_of_payment' => $data['terms_of_payment'] ?? 'na', 
					'specification' => 'na',    
					'general_exclusion' => $data['general_exclusion'] ?? 'na', 
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
    
        // Step 1: Check if contact already exists by querying with last name
        $last_name = $proposal_data['kind_attention'] ?? 'Default Last Name';
        $existing_contact_response = $this->execute_curl_request(
            "https://www.zohoapis.com/crm/v2.1/Contacts/search?criteria=(Last_Name:equals:$last_name)",
            [
                'Content-Type: application/json',
                "Authorization: Zoho-oauthtoken " . $this->access_token
            ],
            null,
            'GET'
        );
    
        $existing_contact_data = json_decode($existing_contact_response['body'], true);
        // print_r($existing_contact_data);
        // die();
        if (!empty($existing_contact_data['data'][0]['Last_Name'])) {
            // Contact exists, use existing contact ID
            $contact_id = $existing_contact_data['data'][0]['id'];
            $contact_name = $existing_contact_data['data'][0]['Last_Name'];
        } else {
            // Contact does not exist, proceed to create a new contact
            $contact_data = json_encode([
                'data' => [
                    [
                        'Last_Name' => $proposal_data['kind_attention'] ?? 'Default Last Name',
                        // 'Email' => '',
                        // 'Phone' => $proposal_data['phone'] ?? '0000000000'
                    ]
                ]
            ]);
    
            $contact_response = $this->execute_curl_request(
                "https://www.zohoapis.com/crm/v2.1/Contacts",
                [
                    'Content-Type: application/json',
                    "Authorization: Zoho-oauthtoken " . $this->access_token
                ],
                $contact_data,
                'POST'
            );
    
            $contact_response_body = json_decode($contact_response['body'], true);
    
            if (isset($contact_response_body['data'][0]['details']['id'])) {
                $contact_id = $contact_response_body['data'][0]['details']['id'];
                $contact_name = $proposal_data['kind_attention'];
            } else {
                log_message('error', 'Failed to create contact in Zoho CRM: ' . print_r($contact_response_body, true));
                return ['error' => 'Failed to create contact.'];
            }
        }
    
        // Step 2: Fetch product details if available
        $product_description = '';
        if (!empty($proposal_data['product_id'])) {
            $product_response = $this->execute_curl_request(
                "https://www.zohoapis.com/crm/v2.1/Products/{$proposal_data['product_id']}",
                [
                    'Content-Type: application/json',
                    "Authorization: Zoho-oauthtoken " . $this->access_token
                ],
                null,
                'GET'
            );
    
            $product_data = json_decode($product_response['body'], true);
    
            if (isset($product_data['data'][0])) {
                $product_description = $product_data['data']['0']['Description'] ?? '';
            } else {
                log_message('error', 'Product not found or missing description for product ID: ' . $proposal_data['product_id']);
            }
        }
    
        // Prepare data for creating a quote
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
                    'Valid_Till' => $valid_until_date,
                    'Contact_Name' => [
                        'name' => $contact_name,
                        'id' => $contact_id,
                    ],
                    'Quoted_Items' => [
                        [
                            'Product_Name' => $proposal_data['product_id'],  
                            'Quantity' => (float) $proposal_data['quantity'], 
                            'List_Price' => (float) $proposal_data['unit_price'], 
                            'U_O_M' => $proposal_data['uom'],
                            'Description' => $product_description 
                        ]
                    ]
                ]
            ]
        ]);
    
        // Make API request to create a quote
        $headers = [
            'Content-Type: application/json',
            "Authorization: Zoho-oauthtoken " . $this->access_token
        ];
    
        $response = $this->execute_curl_request(
            "https://www.zohoapis.com/crm/v2.1/Quotes",
            $headers,
            $data,
            'POST'
        );
    
        $response_body = json_decode($response['body'], true);
    
        if (isset($response_body['data'][0]['details']['id'])) {
            $quote_id = $response_body['data'][0]['details']['id'];
    
            // Fetch Quote_No from the created quote
            $quote_details_response = $this->execute_curl_request(
                "https://www.zohoapis.com/crm/v2/Quotes/$quote_id",
                $headers,
                null,
                'GET'
            );
    
            $quote_details = json_decode($quote_details_response['body'], true);
            $quote_number = $quote_details['data'][0]['Quote_No'] ?? null;
            $modified_time = $quote_details['data'][0]['Modified_Time'] ?? null;
    
            if (!$quote_number) {
                log_message('error', 'Quote number missing from Zoho CRM response: ' . print_r($quote_details, true));
                return ['error' => 'Quote number not found in Zoho CRM response.'];
            }
    
            // Save proposal data to database
            $data = [
                'quote_id' => $quote_id,
                'quote_number' => $quote_number,
                'subject' => $proposal_data['subject'],
                'project_name' => $proposal_data['project'],
                'kind_attention' => $proposal_data['kind_attention'],
                'terms_of_payment' => $proposal_data['terms_of_payment'],
                'product_id' => $proposal_data['product_id'],
                'product_name' => $proposal_data['product_name'],
                'product_description' => $product_description,
                'uom' => $proposal_data['uom'],
                'quantity' => $proposal_data['quantity'],
                'valid_until' => $proposal_data['valid_until'],
                'general_exclusion' => $proposal_data['general_exclusion'],
                'updated_at' => $modified_time,
            ];
    
            if (!$this->Task_model->save_proposal_data($data, $id)) {
                return ['error' => 'Failed to update Task_quote_model.'];
            }
    
            return ['success' => true, 'message' => 'Task quote created successfully.'];
        } else {
            log_message('error', 'Quote ID not found in Zoho CRM response: ' . print_r($response_body, true));
            return ['error' => 'Quote ID not found in Zoho CRM response.'];
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
					'description' => $product['Description'],
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
