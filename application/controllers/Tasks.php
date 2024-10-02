<?php
/**
 * Tasks Controller
 *
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB $db
 * @property Task_model $Task_model
 */
class Tasks extends CI_Controller {

	private $client_id;
    private $client_secret;
    private $refresh_token;
    private $access_token;
	
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Task_model');
		$this->load->model('Task_photo_model');
		$this->load->model('Payment_model');
		$this->load->model('User_model');
        $this->load->library(['form_validation', 'upload']);
        $this->load->helper(['url', 'json_input', 'form']);
        $this->load->helper('security');
		$this->load->helper('token_validate');
		$this->load->library('Pdf');
        $this->output->set_content_type('application/json');

		$this->client_id = '1000.0V519TDSRC8AYUHXMU2SYCAGN9UP3L';
        $this->client_secret = '0f58eea7da1716ec409099063b8f7e42218854e242';
        $this->refresh_token = '1000.a1cbd529bc1bcf8ae030bcf97cbad423.8c806d23c1ba1bb56db72c545e976d8f';
		$this->access_token = '1000.8e68f7b5453b9b0199ae6e7c5d3bcabc.9063dd361e346d18fc708f19ddcbc76b';
    }

    public function create() {
		// Decode JSON input
		$json_input = json_decode($this->input->raw_input_stream, true);
	
		// Set validation rules for required fields
		$this->form_validation->set_rules('zoho_crm_id', 'deal Id', 'required');
		$this->form_validation->set_rules('owner_id', 'Owner Id', 'required');
		$this->form_validation->set_rules('deal_name', 'deal Name', 'required');
		$this->form_validation->set_rules('assigned_to', 'Assigned To', 'required');
		
		// Optional fields (no 'required' rule, just validation if present)
		$this->form_validation->set_rules('remark', 'Remark');
		$this->form_validation->set_rules('remark', 'Remark');
		$this->form_validation->set_rules('service_charge', 'Amount');
		$this->form_validation->set_rules('title', 'Title');
		$this->form_validation->set_rules('status', 'Status');
		$this->form_validation->set_rules('customer_name', 'Customer Name');
		$this->form_validation->set_rules('customer_email', 'Email', 'valid_email');
		$this->form_validation->set_rules('customer_contact', 'Contact');
		$this->form_validation->set_rules('street', 'Street');
		$this->form_validation->set_rules('city', 'City');
		$this->form_validation->set_rules('state', 'State');
		$this->form_validation->set_rules('country', 'Country');
		$this->form_validation->set_rules('zip_code', 'Zip Code');
	
		// Load the input into $_POST to make it compatible with form_validation
		$_POST = $json_input;
	
		if ($this->form_validation->run() == FALSE) {
			$this->output->set_status_header(400);
			echo json_encode(['errors' => $this->form_validation->error_array()]);
		} else {
			// Check if the deal already exists in the database
			$existing_deal = $this->Task_model->get_task_by_zoho_crm_id($json_input['zoho_crm_id']);
	
			if ($existing_deal) {
				// deal exists, update the record
				$update_result = $this->Task_model->update_task($existing_deal['id'], $json_input);
	
				if ($update_result) {
					echo json_encode(['success' => 'deal updated successfully']);
				} else {
					$this->output->set_status_header(500);
					echo json_encode(['error' => 'Failed to update deal']);
				}
			} else {
				// deal does not exist, insert a new record
				$result = $this->Task_model->insert_task($json_input);
	
				if ($result) {
					echo json_encode(['success' => 'deal created successfully']);
				} else {
					$this->output->set_status_header(500);
					echo json_encode(['error' => 'Failed to create deal']);
				}
			}
		}
	}
	
	public function update_lead_status() {
		// Validate API key
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

        if (!$api_key || !validate_api_key($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }

        // Decode JSON input
        $json_input = json_decode($this->input->raw_input_stream, true);

        // Check if JSON input was successfully decoded
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'Invalid JSON format']);
            return;
        }

        // Retrieve the lead_id and status from the decoded JSON input
        $lead_id = $json_input['lead_id'] ?? null;
        $status = $json_input['status'] ?? null;

        // Validate input
        $this->form_validation->set_rules('lead_id', 'Lead ID', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[Attempted to Contact,Contact in Future,Contacted,Junk Lead,Lost Lead,Not Contacted,Pre-Qualified,Not Qualified]');
        
        // Set validation data
        $_POST = [
            'lead_id' => $lead_id,
            'status' => $status,
        ];

        if ($this->form_validation->run() == FALSE) {
            $this->output->set_status_header(400);
            echo json_encode(['errors' => $this->form_validation->error_array()]);
            return;
        }

        // Update lead status in Zoho CRM
        $update_result = $this->update_lead_status_in_zoho($lead_id, $status);

        // Log the response for debugging
        log_message('debug', 'Update Lead Status Response: ' . json_encode($update_result));

        // Check for errors during update
        if (isset($update_result['error'])) {
            $this->output->set_status_header(500);
            echo json_encode($update_result);
            return;
        }

        // Retrieve and check the updated lead details
        $lead_details = $this->get_lead_details($lead_id);

        // Log the response for debugging
        log_message('debug', 'Get Lead Details Response: ' . json_encode($lead_details));

        if ($lead_details && isset($lead_details['data'][0]['Lead_Status']) && $lead_details['data'][0]['Lead_Status'] === $status) {
            echo json_encode(['success' => 'Lead status updated successfully in both DB and Zoho CRM']);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'Failed to verify the lead status update in Zoho CRM']);
        }
    }

	private $zoho_api_url = 'https://www.zohoapis.com/crm/v2/Leads/';
	public function update($id) {
		// Validate API key
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
	
		if (!$api_key || !$user_data = validate_api_key($api_key)) {
			$this->output->set_status_header(401);
			echo json_encode(['error' => 'Unauthorized access']);
			return;
		}
	
		// Detect Content-Type and retrieve data accordingly
		$content_type = $this->input->server('CONTENT_TYPE');
		if (strpos($content_type, 'application/json') !== false) {
			$data = json_decode($this->input->raw_input_stream, true);
		} else {
			$data = $this->input->post();
		}
	
		if ($data === null) {
			$this->output->set_status_header(400);
			echo json_encode(['error' => 'Invalid data format.']);
			return;
		}
	
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('remark', 'Remark');
		$this->form_validation->set_rules('service_charge', 'Service Charge');
	
		if ($this->form_validation->run() === FALSE) {
			$errors = validation_errors();
			$this->output->set_status_header(400);
			echo json_encode(['error' => $errors]);
			return;
		}
	
		// Exclude 'photo' from data for the update query
		$task_data = [
			'remark' => $data['remark'],
			'service_charge' => $data['service_charge']
		];
	
		if ($this->Task_model->update_task($id, $task_data)) {
			$response = ['message' => 'Task updated successfully.'];
	
			// Handle multiple file uploads
			if (isset($_FILES['photos'])) {
				$files = $_FILES;
				$cpt = count($_FILES['photos']['name']);
				$upload_path = './assets/photos/';
				
				if (!is_dir($upload_path)) {
					mkdir($upload_path, 0777, TRUE);
				}
	
				$config['upload_path'] = $upload_path;
				$config['allowed_types'] = 'gif|jpg|jpeg|webp|png';
				$config['max_size'] = 2048;
	
				$this->load->library('upload', $config);
	
				$uploaded_files = [];
	
				for ($i = 0; $i < $cpt; $i++) {
					$_FILES['photo']['name'] = $files['photos']['name'][$i];
					$_FILES['photo']['type'] = $files['photos']['type'][$i];
					$_FILES['photo']['tmp_name'] = $files['photos']['tmp_name'][$i];
					$_FILES['photo']['error'] = $files['photos']['error'][$i];
					$_FILES['photo']['size'] = $files['photos']['size'][$i];
	
					$this->upload->initialize($config);
	
					if (!$this->upload->do_upload('photo')) {
						$this->output->set_status_header(400);
						echo json_encode(['error' => $this->upload->display_errors()]);
						return;
					} else {
						$upload_data = $this->upload->data();
						$photo_data = [
							'task_id' => $id,
							'photo' => $upload_data['file_name']
						];
	
						if (!$this->Task_photo_model->add_photo($photo_data)) {
							$this->output->set_status_header(500);
							echo json_encode(['error' => 'Failed to save photo details.']);
							return;
						}
	
						$uploaded_files[] = $upload_data['full_path']; // Full path to file for uploading to Zoho
					}
				}
	
				// Update photos in Zoho CRM
				$update_results = $this->upload_file_to_zoho($data['zoho_crm_id'], $uploaded_files);
	
				foreach ($update_results as $update_result) {
					if (isset($update_result['error'])) {
						$this->output->set_status_header(500);
						echo json_encode($update_result);
						return;
					}
				}
	
				$response['photos'] = array_map('basename', $uploaded_files); // Return only file names
			}
	
			// Check if the request includes lead status update data
			if (isset($data['zoho_crm_id']) && isset($data['status'])) {
				$lead_id = $data['zoho_crm_id'];
				$status = $data['status'];
	
				// Validate lead_id and status
				$this->form_validation->set_rules('zoho_crm_id', 'Lead ID', 'required');
				$this->form_validation->set_rules('status', 'Status', 'required|in_list[Job Assigned,Job Confirmed,Job Not Confirmed,Big Project,Job Completed]');
	
				if ($this->form_validation->run() == FALSE) {
					$this->output->set_status_header(400);
					echo json_encode(['errors' => $this->form_validation->error_array()]);
					return;
				}
	
				// Update lead status in Zoho CRM
				$update_result = $this->update_lead_status_in_zoho($lead_id, $status);
	
				// Log the response for debugging
				log_message('debug', 'Update Lead Status Response: ' . json_encode($update_result));
	
				// Check for errors during update
				if (isset($update_result['error'])) {
					$this->output->set_status_header(500);
					echo json_encode($update_result);
					return;
				}
	
				// Retrieve and check the updated lead details
				$lead_details = $this->get_lead_details($lead_id);
	
				if ($lead_details && isset($lead_details['data'][0]['Lead_Status']) && $lead_details['data'][0]['Lead_Status'] === $status) {
					$response['lead_status'] = 'Lead status updated successfully in both DB and Zoho CRM';
				} else {
					$this->output->set_status_header(500);
					echo json_encode(['error' => 'Failed to verify the lead status update in Zoho CRM']);
					return;
				}
			}
	
			$this->output->set_status_header(200);
			echo json_encode($response);
		} else {
			$this->output->set_status_header(500);
			echo json_encode(['error' => 'Failed to update task.']);
		}
	}

	public function update_all_leads_and_files() {
		// Define the prefix for file paths
		$file_prefix = './assets/photos/'; // Adjust this to your actual prefix
	
		// Fetch all tasks with Zoho CRM IDs and status updates
		$tasks = $this->Task_model->get_all_tasks_for_update();
	
		$responses = []; // Array to hold the results of the update process
	
		foreach ($tasks as $task) {
			$task_response = [
				'lead_id' => $task['zoho_crm_id'] ?? null,
				'status_update' => null,
				'file_uploads' => []
			];
	
			// Update lead status in Zoho CRM
			if (isset($task['zoho_crm_id']) && isset($task['status'])) {
				$lead_id = $task['zoho_crm_id'];
				$status = $task['status'];
	
				$update_result = $this->update_lead_status_in_zoho($lead_id, $status);
	
				if (isset($update_result['error'])) {
					$task_response['status_update'] = 'Failed to update lead status in Zoho CRM: ' . json_encode($update_result);
				} else {
					$task_response['status_update'] = 'Lead status updated successfully in Zoho CRM: ' . json_encode($update_result);
				}
			}
	
			// Handle file uploads to Zoho CRM
			if (isset($task['photos']) && !empty($task['photos'])) {
				$uploaded_files = []; // Assuming file paths are stored in the 'photos' field
	
				foreach ($task['photos'] as $file_path) {
					$full_file_path = $file_prefix . $file_path;
					if (file_exists($full_file_path)) {
						$uploaded_files[] = $full_file_path;
					} else {
						$task_response['file_uploads'][] = 'File does not exist: ' . $full_file_path;
					}
				}
	
				if (!empty($uploaded_files)) {
					$upload_results = $this->upload_file_to_zoho($task['zoho_crm_id'], $uploaded_files);
	
					foreach ($upload_results as $upload_result) {
						if (isset($upload_result['error'])) {
							$task_response['file_uploads'][] = 'Failed to upload file to Zoho CRM. File: ' . ($upload_result['file_path'] ?? 'Unknown') . '. Error: ' . json_encode($upload_result);
						} else {
							$task_response['file_uploads'][] = 'File uploaded successfully to Zoho CRM. File: ' . ($upload_result['file_path'] ?? 'Unknown') . '. Response: ' . json_encode($upload_result);
						}
					}
				}
			}
	
			$responses[] = $task_response;
		}
	
		// Return the responses as a JSON object
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'status' => 'success',
				'message' => 'Lead status and file updates processed.',
				'data' => $responses
			]));
	}				
	
	private function upload_file_to_zoho($lead_id, $file_paths) {
		$url = "https://www.zohoapis.com/crm/v2/Leads/{$lead_id}/Attachments";
		$headers = array(
			"Authorization: Zoho-oauthtoken " . $this->access_token
		);
	
		$responses = [];
	
		foreach ($file_paths as $file_path) {
			// Ensure $file_path is a string representing the file path
			if (!is_string($file_path) || !file_exists($file_path)) {
				$responses[] = array('error' => 'Invalid file path: ' . $file_path);
				continue;
			}
	
			$mime_type = mime_content_type($file_path); // Get MIME type of the file
	
			// Prepare the file for upload
			$post_fields = [
				'file' => new CURLFile($file_path, $mime_type)
			];
	
			$response = $this->execute_curl_request($url, $headers, $post_fields);
	
			if ($response['http_code'] == 401) {  // Unauthorized, access token may have expired
				if ($this->refresh_access_token()) {
					$headers = array(
						"Authorization: Zoho-oauthtoken " . $this->access_token
					);
	
					$response = $this->execute_curl_request($url, $headers, $post_fields);
				}
			}
	
			if ($response['http_code'] == 200) {
				$response_data = json_decode($response['body'], true);
				$file_response = [
					'file_path' => $file_path, // Add the file path to the response
					'response' => $response_data
				];
				$responses[] = $file_response;
			} else {
				$responses[] = array(
					'file_path' => $file_path, // Include the file path in the error response
					'error' => 'Failed to upload file',
					'response' => $response['body'],
					'http_code' => $response['http_code']
				);
			}
		}
	
		return $responses;
	}		
						
    private function update_lead_status_in_zoho($lead_id, $status) {
		$url = $this->zoho_api_url . $lead_id;
		$data = array(
			"data" => array(
				array(
					"Lead_Status" => $status
				)
			)
		);
	
		$headers = array(
			"Authorization: Zoho-oauthtoken " . $this->access_token,
			"Content-Type: application/json"
		);
	
		$response = $this->execute_curl_request($url, $headers, json_encode($data), "PATCH");
	
		if ($response['http_code'] == 401) {  // Unauthorized, access token may have expired
			if ($this->refresh_access_token()) {
				$headers = array(
					"Authorization: Zoho-oauthtoken " . $this->access_token,
					"Content-Type: application/json"
				);
	
				$response = $this->execute_curl_request($url, $headers, json_encode($data), "PATCH");
			}
		}
	
		if ($response['http_code'] == 200) {
			return json_decode($response['body'], true);
		} else {
			// Improved error handling
			$error_message = 'Failed to update lead status';
			$response_data = json_decode($response['body'], true);
			
			if (isset($response_data['data'][0]['message'])) {
				$error_message .= ': ' . $response_data['data'][0]['message'];
			}
	
			return array('error' => $error_message, 'response' => $response['body'], 'http_code' => $response['http_code']);
		}
	}
	
	private function execute_curl_request($url, $headers, $post_fields = null, $method = 'POST') {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
		if ($method === 'PATCH') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		} else {
			curl_setopt($ch, CURLOPT_POST, true);
		}
	
		if ($post_fields) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		}
	
		$response_body = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		return ['body' => $response_body, 'http_code' => $http_code];
	}

	private function refresh_access_token() {
		$url = "https://accounts.zoho.com/oauth/v2/token";
	
		// Replace with your stored credentials
		$data = array(
			'refresh_token' => $this->refresh_token,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type' => 'refresh_token'
		);
	
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
		$response = curl_exec($ch);
		curl_close($ch);
	
		$response_data = json_decode($response, true);
	
		if (isset($response_data['access_token'])) {
			// Save the new access token (you might want to store it in the database or cache)
			$this->access_token = $response_data['access_token'];
			// Optionally, save it in the session or a config file
			// $this->save_access_token($this->access_token);
	
			return $this->access_token;
		} else {
			// Log or handle the error
			log_message('error', 'Failed to refresh access token: ' . $response);
			return false;
		}
	}		

    private function get_lead_details($lead_id) {
        $url = $this->zoho_api_url . $lead_id;
        
        $headers = array(
            "Authorization: Zoho-oauthtoken " . $this->access_token,
            "Content-Type: application/json"
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
	
	public function list_tasks_per_user($id) {
		// Validate API key
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
	
		if (!$api_key || !$user_data = validate_api_key($api_key)) {
			$this->output->set_status_header(401);
			echo json_encode(['error' => 'Unauthorized access']);
			return;
		}
	
		// Get the user_id from the validated user data
		$header_user_id = $user_data['id'];
	
		// Fetch the user_id based on the id passed in the URL
		$user_id = $this->User_model->get_user_id_by_id($id);
	
		if (!$user_id) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'User not found for this id.']);
			return;
		}
	
		// Check if the user_id fetched matches the one from the token
		if ($header_user_id != $id) {
			$this->output->set_status_header(403); // Forbidden
			echo json_encode(['error' => 'Forbidden: User ID mismatch']);
			return;
		}
	
		// Fetch tasks where assigned_to equals the user_id
		$tasks = $this->User_model->get_tasks_by_assigned_user($user_id);
	
		if (empty($tasks)) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'No tasks found for this user.']);
		} else {
			foreach ($tasks as &$task) {
				$task['photos'] = $this->Task_photo_model->get_photos_by_task($task['id']);
			}
			$this->output->set_status_header(200);
			echo json_encode($tasks);
		}
	}

	public function get_payments_by_user($id) {
        // Validate API key
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
	
		if (!$api_key || !$user_data = validate_api_key($api_key)) {
			$this->output->set_status_header(401);
			echo json_encode(['error' => 'Unauthorized access']);
			return;
		}

		// Get the user_id from the validated user data
		$header_user_id = $user_data['id'];

		// Check if the user_id fetched matches the one from the token
		if ($header_user_id != $id) {
			$this->output->set_status_header(403); // Forbidden
			echo json_encode(['error' => 'Forbidden: User ID mismatch']);
			return;
		}

        // Fetch the user_id based on the id passed in the URL
        $user_id = $this->User_model->get_user_id_by_id($id);

        if (!$user_id) {
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'User ID not found.']);
            return;
        }

        // Fetch payments based on user_id
        $payments = $this->Payment_model->get_payments_by_user($user_id);

        if ($payments) {
            $this->output->set_status_header(200);
            echo json_encode(['payments' => $payments]);
        } else {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'No payments found for the given user ID.']);
        }
    }

	private function validate_api_key() {
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

		return $api_key && validate_api_key($api_key);
	}
	
	public function get_big_projects($id) {

		// Validate API key
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
	
		if (!$api_key || !$user_data = validate_api_key($api_key)) {
			$this->output->set_status_header(401);
			echo json_encode(['error' => 'Unauthorized access']);
			return;
		}

		// Get the user_id from the validated user data
		$header_user_id = $user_data['id'];

		// Check if the user_id fetched matches the one from the token
		if ($header_user_id != $id) {
			$this->output->set_status_header(403); // Forbidden
			echo json_encode(['error' => 'Forbidden: User ID mismatch']);
			return;
		}

		// Fetch the user_id based on the id passed in the URL
        $user_id = $this->User_model->get_user_id_by_id($id);

		$projects = $this->Task_model->get_big_projects($user_id);

		if (empty($projects)) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'No projects found.']);
		}

		// Set response headers
		$this->output->set_status_header(200);
		echo json_encode($projects);
	}

	public function view($id) {
        // Validate API key
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
        if (!$api_key || !$user_data = validate_api_key($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }
        
        $task = $this->Task_model->get_task($id);

        if (empty($task)) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'Task not found.']);
        } else {
            $photos = $this->Task_photo_model->get_photos_by_task($id);
            $task['photos'] = $photos;
            
            // Fetch the quotes related to the task
            // $quotes = $this->Task_quote_model->get_quotes_by_task($id);
            // $task['quotes'] = $quotes;
            
            $this->output->set_status_header(200);
            echo json_encode($task);
        }
    }
    
    public function deal_pdf($id) {
        // Validate API key
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
        if (!$api_key || !$user_data = validate_api_key($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }
    
        // Get task data
        $task = $this->Task_model->get_task($id);
        if (empty($task)) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'Task not found.']);
            return;
        }
    
        // Load PDF library
        $pdf = new Pdf();
        $pdf->SetMargins(5, 0, 5);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage('', array(200, 400));
        
        // $pdf->Image(FCPATH . 'assets/photos/logo/vx_lg1.jpg', 10, 10, 50, '', '', '', false);
        // $pdf->Image(FCPATH . 'assets/photos/logo/seal.jpg', 150, 10, 50, '', '', '', false);
    
        // Load view and pass the task data to it
        $html = $this->load->view('deal_pdf_view', ['task' => $task], true);
    
        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
    
        // Close and output PDF document
        $pdf->Output('task_details.pdf', 'I');  // Output to browser
    }
	  
}

