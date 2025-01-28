<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	private $client_id;
    private $client_secret;
	private $refresh_token;
    private $access_token;

	public function __construct() {
		parent::__construct();
		$this->load->library(['form_validation', 'session']);
		$this->load->model('Task_model');
		$this->load->model('User_model');
		$this->load->model('Task_photo_model');
		$this->load->model('Access_token_model');
		$this->load->helper(['url', 'json_input', 'form']);
		$this->output->set_content_type('application/json');

		$this->client_id = '1000.0V519TDSRC8AYUHXMU2SYCAGN9UP3L';
        $this->client_secret = '0f58eea7da1716ec409099063b8f7e42218854e242';
		$this->refresh_token = '1000.4348d34c1a96e813abe7ff21bfc4a04b.0fd6bc7aeb9d83178d3b5f7f893744c8';
	}

	public function check_logged_in() {
		if (!$this->session->userdata('logged_in')) {
			// If not logged in, redirect to login page
			redirect('web/login');
		}
	}
	
	private function get_access_token() {
        $this->access_token = $this->Access_token_model->get_access_token();
        return $this->access_token;
    }

	public function add_proposal() {
		// Check if the user is logged in
		$this->check_logged_in();

		$content_type = $this->input->server('CONTENT_TYPE') ?? '';
		$data = strpos($content_type, 'application/json') !== false ? json_decode($this->input->raw_input_stream, true) : $this->input->post();
	
		if ($data === null) {
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
			return;
		}
	
		// Log parsed data for debugging
		log_message('debug', 'Parsed data: ' . json_encode($data));
	
		// Set validation rules for required fields
		$this->form_validation->set_rules('dealNumber', 'Deal Number', 'required');
		$this->form_validation->set_rules('subject', 'subject', 'required');
		// $this->form_validation->set_rules('accountName', 'Account Name', 'required');
		$this->form_validation->set_rules('itemName', 'Item Name', 'required');
		$this->form_validation->set_rules('quantity', 'Quantity', 'required');
		$this->form_validation->set_rules('unitPrice', 'Unit Price', 'required');
		$this->form_validation->set_rules('total', 'Total', 'required');
		$this->form_validation->set_rules('project', 'Project Name', 'required');
		$this->form_validation->set_rules('termsOfPayment', 'Terms of Payment', 'required');
		$this->form_validation->set_rules('specification', 'Specification', 'required');
		$this->form_validation->set_rules('generalExclusion', 'General Exclusion', 'required');
		$this->form_validation->set_rules('brand', 'Brand', 'required');
		$this->form_validation->set_rules('warranty', 'Warranty', 'required');
		$this->form_validation->set_rules('delivery', 'Delivery', 'required');
		$this->form_validation->set_rules('validUntil', 'Valid Until', 'required');
	
		$_POST = $data;
	
		if ($this->form_validation->run() == FALSE) {
			// Log the validation errors
			log_message('error', 'Validation errors: ' . json_encode($this->form_validation->error_array()));
		
			// Set response status to 400 for validation errors
			$this->output->set_status_header(400);
		
			// Return the response with a single error message
			$errors = $this->form_validation->error_array();
			$errorMessage = reset($errors);  // Retrieve the first error message
		
			echo json_encode(['error' => $errorMessage ?: 'Validation failed.']);
			return;
		}
		
		// Ensure `general_exclusion` is properly formatted before sending to Zoho
        $data['general_exclusion'] = $this->input->post('generalExclusion', true);
	
		// Add proposal to Zoho
		$result = $this->add_proposal_to_zoho($data['dealNumber'], $data);
	
		if (isset($result['error'])) {
			log_message('error', 'Failed to add proposal to Zoho: ' . $result['error']);
			return $this->response($result, 500);
		}
	
		// Log successful addition
		log_message('info', 'Proposal successfully added to Zoho for Deal ID: ' . $data['dealNumber']);
		// return $this->response(['success' => true, 'message' => 'Proposal added successfully.'], 201);
	}        
	
	private function add_proposal_to_zoho($deal_number, $proposal_data) {
		ini_set('display_errors', 0); // Hide errors from being directly output to the client
		ini_set('log_errors', 1); // Enable error logging
	
		// Step 1: Create a new contact in Zoho CRM
		$contact_data = json_encode([
			'data' => [
				[
					'Last_Name' => $proposal_data['kind_attention'] ?? 'Default Last Name',
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
		
		$contact_data_response = json_decode($contact_response['body'], true);
		
		if ($contact_response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				$this->access_token = $this->get_access_token();
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];
	
				$contact_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2.1/Contacts",
					$headers,
					$contact_data,
					'POST'
				);
	
				$contact_data_response = json_decode($contact_response['body'], true);
			} else {
				log_message('error', 'Failed to refresh access token for contact creation.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
	
		$contact_response_body = json_decode($contact_response['body'], true);
		if (isset($contact_response_body['data'][0]['details']['id'])) {
			$contact_id = $contact_response_body['data'][0]['details']['id'];
			// $contact_name = $proposal_data['kind_attention'];
		} else {
			log_message('error', 'Failed to create contact in Zoho CRM: ' . print_r($contact_response_body, true));
			return ['error' => 'Failed to create contact.'];
		}
		
		// Fetch product details from Zoho CRM using product_id
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
		
			if ($product_response['http_code'] == 401) {
				if ($this->refresh_access_token()) {
					$this->access_token = $this->get_access_token();
					$headers = [
						'Content-Type: application/json',
						"Authorization: Zoho-oauthtoken " . $this->access_token
					];
	
					$product_response = $this->execute_curl_request(
						"https://www.zohoapis.com/crm/v2.1/Products/{$proposal_data['product_id']}",
						$headers,
						null,
						'GET'
					);
	
					$product_data = json_decode($product_response['body'], true);
				} else {
					log_message('error', 'Failed to refresh access token for product details.');
					return ['error' => 'Failed to refresh access token.'];
				}
			}
		
			$product_description = $product_data['data']['0']['Description'] ?? '';
		} else {
			$product_description = '';
		}

		// Step 1: Get the Deal ID using DealNumber
		$deal_response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Deals/search?criteria=(DealNumber:equals:$deal_number)",
			[
				'Content-Type: application/json',
				"Authorization: Zoho-oauthtoken " . $this->access_token
			],
			null,
			'GET'
		);
	
		$deal_data = json_decode($deal_response['body'], true);
	
		if ($deal_response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				$this->access_token = $this->get_access_token();
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];
	
				$deal_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2.1/Deals/search?criteria=(DealNumber:equals:$deal_number)",
					$headers,
					null,
					'GET'
				);
	
				$deal_data = json_decode($deal_response['body'], true);
			} else {
				log_message('error', 'Failed to refresh access token while searching for DealNumber.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
	
		// Check if Deal ID is found
		if (!isset($deal_data['data'][0]['id'])) {
			log_message('error', 'Deal not found for DealNumber: ' . $deal_number);
			return ['error' => 'Deal not found.'];
		}
	
		$deal_id = $deal_data['data'][0]['id'];
		
		// Fetch task data by Deal ID
        $task = $this->Task_model->get_task_by_deal_id($deal_id);
        if (!$task) {
            log_message('error', 'No task found for Deal ID: ' . $deal_id);
            return ['error' => 'No task found for the provided Deal ID.'];
        }
    
        $id = $task->id;
        $account_name = $task->account_name;

		$data = json_encode([
			'data' => [
				[
					'Deal_Name' => $deal_id,
					'Account_Name' => $account_name ?? null,
					'Subject' => $proposal_data['subject'] ?? 'Default Subject',
					'Project' => $proposal_data['project'] ?? 'na',
					'Terms_of_Payment' => $proposal_data['termsOfPayment'] ?? 'na',
					'Specification' => $proposal_data['specification'] ?? 'na',
					'General_Exclusion' => $proposal_data['generalExclusion'] ?? 'na',
					'Brand' => $proposal_data['brand'] ?? 'na',
					'Warranty' => $proposal_data['warranty'] ?? 'na',
					'Delivery' => $proposal_data['delivery'] ?? 'na',
					'Valid_Till' => $proposal_data['validUntil'] ?? null,
					'Sub_Total' => (float) ($proposal_data['subTotal'] ?? 0),
					'Discount' => (float) ($proposal_data['discount'] ?? 0),
					'line_tax' => [
						[
							'percentage' => 5, // VAT Percentage
							'name' => 'Vat',
							'id' => '5653678000000021003', // You can use the Zoho ID for the VAT line tax
							'value' => (float) (((float) $proposal_data['subTotal'] - (float) $proposal_data['discount']) * 5 / 100), // Ensure numeric
						]
					],
					'Adjustment' => (float) ($proposal_data['adjustment'] ?? 0),
					'Grand_Total' => (float) ($proposal_data['grandTotal'] ?? 0),
					'Contact_Name' => [
						'name' => $contact_name,
						'id' => $contact_id,
					],
					'Quoted_Items' => [
						[
							'Product_Name' => $proposal_data['itemName'],
							'Quantity' => (float) $proposal_data['quantity'],
							'List_Price' => (float) $proposal_data['unitPrice'],
							'U_O_M' => $proposal_data['uom'],
							'Description' => $proposal_data['itemDescription'],
						]
					]
				]
			]
		]);			   
	
		$this->access_token = $this->get_access_token();
	
		if (!$this->access_token) {
			return ['error' => 'Access token not found.'];
		}
	
		$headers = [
			'Content-Type: application/json',
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
	
		// log_message('debug', 'Headers: ' . print_r($headers, true));
		// log_message('debug', 'Post Data: ' . $data);
	
		$response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Quotes",
			$headers,
			$data,
			'POST'
		);
	
		$response_body = json_decode($response['body'], true);

		// Now handle the insertion of task quote
		if (isset($response_body['data'][0]['details']['id'])) {
			$quote_id = $response_body['data'][0]['details']['id'];
		
			log_message('debug', 'Quote ID: ' . $quote_id);
			
			// Fetch the created quote to get its Quote_No
			$quote_details_response = $this->execute_curl_request(
				"https://www.zohoapis.com/crm/v2/Quotes/$quote_id",
				$headers,
				null,
				'GET'
			);
			
			$quote_details = json_decode($quote_details_response['body'], true);
			$quote_number = $quote_details['data'][0]['Quote_No'] ?? null;
		
			if (!$quote_number) {
				log_message('debug', 'Quote number missing from Zoho CRM response: ' . print_r($quote_details, true));
				$this->output->set_status_header(500);
				return json_encode(['success' => false, 'error' => 'Quote number not found in Zoho CRM response.']);
			}
			
			// Now update the deal status to 'Proposal'
			$update_status_response = $this->update_deal_in_zoho($deal_id, 'Proposal/Price Quote', $proposal_data['unitPrice']);
		
			// $id = $this->Task_model->get_id_by_deal_id($deal_id);
		
			log_message('debug', 'Task ID: ' . $id);
		
			if (!$id) {
				log_message('debug', 'No task found for the provided deal_id: ' . $deal_id);
				$this->output->set_status_header(404);
				return json_encode(['error' => 'No task found for the provided deal_id']);
			}
			
			$quote_id = $quote_details['data'][0]['id'];
    
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
			
			$data = [
				// 'id' => $id,
				'zoho_crm_id' => $deal_id,
				'quote_id' => $quote_id,
				'quote_number' => $quote_number,
				'subject' => $proposal_data['subject'],
				'project_name' => $proposal_data['project'],
				'terms_of_payment' => $proposal_data['termsOfPayment'],
				'product_id' => $proposal_data['product_id'],
				'product_name' => $proposal_data['product_name'],
				'product_description' => $proposal_data['itemDescription'],
				'uom' => $proposal_data['uom'],
				'service_charge' => $proposal_data['unitPrice'],
				'kind_attention' => $proposal_data['kind_attention'],
				'specification' => $proposal_data['specification'],
				'brand' => $proposal_data['brand'],
				'warranty' => $proposal_data['warranty'],
				'delivery' => $proposal_data['delivery'],
				'quantity' => $proposal_data['quantity'],
				'valid_until' => $proposal_data['validUntil'],
				'general_exclusion' => $proposal_data['generalExclusion'],
				'updated_at' => $modified_time,
			];
		
			if (!$this->Task_model->save_proposal_data($data, $id)) {
				log_message('error', 'Failed to insert data into Task_model with data: ' . print_r($data, true));
				$this->db->trans_rollback();
				$this->output->set_status_header(500);
				return json_encode(['error' => 'Failed to update Task_quote_model.']);
			}
		
			// return ['success' => true, 'message' => 'Task quote created successfully.'];
			return $this->response(['success' => true, 'message' => 'Proposal added successfully.', 'id' => $id], 201);
		} else {
			log_message('debug', 'Full response for error handling: ' . print_r($response_body, true));
			$this->output->set_status_header(500);
			return json_encode(['error' => 'Quote ID not found in Zoho CRM response.']);
		}
		
	}
	
	// Function to update the deal status to 'Proposal' in Zoho CRM
	public function update_deal_in_zoho($deal_id, $status, $unitPrice)
	{
		// Prepare the request body to update the deal status
		$data = [
			"data" => [
				[
					"id" => $deal_id,
					"Amount" => $unitPrice,
					"Stage" => $status,
					"VZ_App_Status" => 'Proposal',
				]
			]
		];

		// Convert the data to JSON
		$json_data = json_encode($data);

		// Set headers for the Zoho CRM API request
		$headers = [
			'Authorization: Zoho-oauthtoken ' . $this->access_token, // Add your OAuth token here
			'Content-Type: application/json'
		];

		// Send the API request to update the deal
		$response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2/Deals/{$deal_id}",
			$headers,
			$json_data,
			'PUT'
		);

		// Check if the response code is 401 (Unauthorized)
		if ($response['http_code'] == 401) {
			// Attempt to refresh the access token
			if ($this->refresh_access_token()) {
				// Retrieve the new access token
				$this->access_token = $this->get_access_token();

				// Retry the request with the new access token
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];

				$response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2/Deals/{$deal_id}",
					$headers,
					$json_data,
					'PUT'
				);
			} else {
				log_message('error', 'Failed to refresh access token while updating the deal status.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}

		// Decode the response to check if the update was successful
		$response_body = json_decode($response['body'], true);

		// Check for success in the response body
		if (isset($response_body['data'][0]['status']) && $response_body['data'][0]['status'] == 'success') {
			// Successfully updated the deal
			log_message('debug', 'Deal status updated successfully for Deal ID: ' . $deal_id);
			return ['success' => true, 'message' => 'Deal status updated to Proposal.'];
		} else {
			// Log failure and return error
			log_message('error', 'Failed to update deal status for Deal ID: ' . $deal_id . ' - ' . print_r($response_body, true));
			return ['success' => false, 'error' => 'Failed to update deal status.'];
		}
	}
	
	public function edit_proposal() {
		// Check if the user is logged in
		$this->check_logged_in();

		$content_type = $this->input->server('CONTENT_TYPE') ?? '';
		$data = strpos($content_type, 'application/json') !== false ? json_decode($this->input->raw_input_stream, true) : $this->input->post();
	
		if ($data === null) {
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
			return;
		}
	
		// Log parsed data for debugging
		log_message('debug', 'Parsed data: ' . json_encode($data));
	
		// Set validation rules for required fields
		$this->form_validation->set_rules('QuoteNumber', 'Quote Number', 'required');
		$this->form_validation->set_rules('subject', 'subject', 'required');
		// $this->form_validation->set_rules('accountName', 'Account Name', 'required');
		$this->form_validation->set_rules('itemName2', 'Item Name', 'required');
		// $this->form_validation->set_rules('itemDescription', 'Item description', 'required');
		$this->form_validation->set_rules('quantity', 'Quantity', 'required');
		$this->form_validation->set_rules('unitPrice', 'Unit Price', 'required');
		$this->form_validation->set_rules('total', 'Total', 'required');
		$this->form_validation->set_rules('project', 'Project Name', 'required');
		$this->form_validation->set_rules('termsOfPayment', 'Terms of Payment', 'required');
		$this->form_validation->set_rules('specification', 'Specification', 'required');
		$this->form_validation->set_rules('generalExclusion', 'General Exclusion', 'required');
		$this->form_validation->set_rules('brand', 'Brand', 'required');
		$this->form_validation->set_rules('warranty', 'Warranty', 'required');
		$this->form_validation->set_rules('delivery', 'Delivery', 'required');
		$this->form_validation->set_rules('validUntil', 'Valid Until', 'required');
	
		$_POST = $data;
	
		if ($this->form_validation->run() == FALSE) {
			// Log the validation errors
			log_message('error', 'Validation errors: ' . json_encode($this->form_validation->error_array()));
		
			// Set response status to 400 for validation errors
			$this->output->set_status_header(400);
		
			// Return the response with a single error message
			$errors = $this->form_validation->error_array();
			$errorMessage = reset($errors);  // Retrieve the first error message
		
			echo json_encode(['error' => $errorMessage ?: 'Validation failed.']);
			return;
		}
		
		// Ensure `general_exclusion` is properly formatted before sending to Zoho
        $data['general_exclusion'] = $this->input->post('generalExclusion', true);
	
		// Add proposal to Zoho
		$result = $this->update_quote_in_zoho($data['QuoteNumber'], $data);
	
		if (isset($result['error'])) {
			log_message('error', 'Failed to edit proposal in Zoho: ' . $result['error']);
			return $this->response($result, 500);
		}
	
		// Log successful addition
		log_message('info', 'Proposal successfully edited in Zoho for Quote ID: ' . $data['QuoteNumber']);
	
		// return $this->response(['success' => true, 'message' => 'Proposal edited successfully.'], 201);
	}

	private function update_quote_in_zoho($quote_number, $proposal_data) {
		ini_set('display_errors', 0); // Hide errors from being directly output to the client
		ini_set('log_errors', 1); // Enable error logging
		
		// Ensure JSON response
        header('Content-Type: application/json');

		// Step 1: Create a new contact in Zoho CRM
		$contact_data = json_encode([
			'data' => [
				[
					'Last_Name' => $proposal_data['kind_attention'] ?? 'Default Last Name',
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
		
		$contact_data_response = json_decode($contact_response['body'], true);
		
		if ($contact_response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				$this->access_token = $this->get_access_token();
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];
	
				$contact_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2.1/Contacts",
					$headers,
					$contact_data,
					'POST'
				);
	
				$contact_data_response = json_decode($contact_response['body'], true);
			} else {
				log_message('error', 'Failed to refresh access token for contact creation.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
	
		$contact_response_body = json_decode($contact_response['body'], true);
		if (isset($contact_response_body['data'][0]['details']['id'])) {
			$contact_id = $contact_response_body['data'][0]['details']['id'];
			$contact_name = $proposal_data['kind_attention'];
		} else {
			log_message('error', 'Failed to create contact in Zoho CRM: ' . print_r($contact_response_body, true));
			return ['error' => 'Failed to create contact.'];
		}
	
		// Step 1: Fetch Quote ID using the Quote Number
		$quote_response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2/Quotes/search?criteria=(Quote_No:equals:$quote_number)",
			[
				'Content-Type: application/json',
				"Authorization: Zoho-oauthtoken " . $this->access_token
			],
			null,
			'GET'
		);
	
		$quote_data = json_decode($quote_response['body'], true);
	
		if ($quote_response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				$this->access_token = $this->get_access_token();
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];
	
				$quote_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2/Quotes/search?criteria=(Quote_No:equals:$quote_number)",
					$headers,
					null,
					'GET'
				);
	
				$quote_data = json_decode($quote_response['body'], true);
			} else {
				log_message('error', 'Failed to refresh access token while searching for Quote Number.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
	
		if (!isset($quote_data['data'][0]['id'])) {
			log_message('error', 'Quote not found for Quote Number: ' . $quote_number);
			return ['error' => 'Quote not found.'];
		}
	
		$quote_id = $quote_data['data'][0]['id'];

		// Step 1: Get the Deal ID using DealNumber
		$quote_response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Quotes/search?criteria=(Quote_No:equals:$quote_number)",
			[
				'Content-Type: application/json',
				"Authorization: Zoho-oauthtoken " . $this->access_token
			],
			null,
			'GET'
		);
	
		$quote_data = json_decode($quote_response['body'], true);
		log_message('error', 'message while searching for DealNumber.' . print_r($quote_data, true));
	
		if ($quote_response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				$this->access_token = $this->get_access_token();
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];
	
				$quote_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2.1/Quotes/search?criteria=(Quote_No:equals:$quote_number)",
					$headers,
					null,
					'GET'
				);
	
				$quote_data = json_decode($quote_response['body'], true);
				log_message('error', 'Failed to refresh access token while searching for DealNumber.' . print_r($quote_data, true));
			} else {
				log_message('error', 'Failed to refresh access token while searching for DealNumber.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
	
		// Check if Deal ID is found
		if (!isset($quote_data['data'][0]['id'])) {
			log_message('error', 'Deal not found for DealNumber: ' . $quote_number);
			return ['error' => 'Deal not found.'];
		}
	
		$quote_no = $quote_data['data'][0]['id'];
	
		// Step 2: Prepare data for updating the quote
		$data = json_encode([
			'data' => [
				[
					'Subject' => $proposal_data['subject'] ?? 'Default Subject',
					'Project' => $proposal_data['project'] ?? 'na',
					'Terms_of_Payment' => $proposal_data['termsOfPayment'] ?? 'na',
					'Specification' => $proposal_data['specification'] ?? 'na',
					'General_Exclusion' => $proposal_data['generalExclusion'] ?? 'na',
					'Brand' => $proposal_data['brand'] ?? 'na',
					'Warranty' => $proposal_data['warranty'] ?? 'na',
					'Delivery' => $proposal_data['delivery'] ?? 'na',
					'Valid_Till' => $proposal_data['validUntil'] ?? null,
					'Sub_Total' => (float) ($proposal_data['subTotal'] ?? 0),
					'Discount' => (float) ($proposal_data['discount'] ?? 0),
					'line_tax' => [
                        [
                            'percentage' => 5, // VAT Percentage
                            'name' => 'Vat',
                            'id' => '5653678000000021003', // You can use the Zoho ID for the VAT line tax
                            'value' => (float) (((float) $proposal_data['subTotal'] - (float) $proposal_data['discount']) * 5 / 100), // Ensure numeric
                        ]
                    ],
					'Adjustment' => (float) ($proposal_data['adjustment'] ?? 0),
					'Grand_Total' => (float) ($proposal_data['grandTotal'] ?? 0),
					'Contact_Name' => [
						'name' => $contact_name,
						'id' => $contact_id,
					],
					'Quoted_Items' => [
						[
							'Product_Name' => $proposal_data['itemName2'],
							'Quantity' => (float) $proposal_data['quantity'],
							'List_Price' => (float) $proposal_data['unitPrice'],
							'U_O_M' => $proposal_data['uom'],
							'Description' => $proposal_data['itemDescription'] ?? '',
						]
					]
				]
			]
		]);
	
		$headers = [
			'Content-Type: application/json',
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
	
		// Step 3: Update the quote in Zoho CRM
		$response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Quotes/$quote_id",
			$headers,
			$data,
			'PATCH'
		);
	
		$response_body = json_decode($response['body'], true);
	
		if (isset($response_body['data'][0]['code']) && $response_body['data'][0]['code'] === 'SUCCESS') {
				// Now handle the insertion of task quote
				$quote_id = $response_body['data'][0]['details']['id'];
			
				log_message('debug', 'Quote ID: ' . $quote_id);
				
				// Fetch the created quote to get its Quote_No
				$quote_details_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2/Quotes/$quote_id",
					$headers,
					null,
					'GET'
				);
				
				$quote_details = json_decode($quote_details_response['body'], true);
				$quote_number = $quote_details['data'][0]['Quote_No'] ?? null;
			
				if (!$quote_number) {
					log_message('debug', 'Quote number missing from Zoho CRM response: ' . print_r($quote_details, true));
					$this->output->set_status_header(500);
					return json_encode(['success' => false, 'error' => 'Quote number not found in Zoho CRM response.']);
				}
			
				$id = $this->Task_model->get_id_by_quote_id($quote_no);
			
				log_message('debug', 'Task ID: ' . $id);
			
				if (!$id) {
					log_message('debug', 'No task found for the provided deal_id: ' . $quote_no);
					$this->output->set_status_header(404);
					return json_encode(['error' => 'No task found for the provided deal_id']);
				}
				
				$quote_id = $quote_details['data'][0]['id'];
		
				// Fetch Quote_No from the created quote
				$quote_details_response = $this->execute_curl_request(
					"https://www.zohoapis.com/crm/v2/Quotes/$quote_id",
					$headers,
					null,
					'GET'
				);
		
				$quote_details = json_decode($quote_details_response['body'], true);
				// $quote_number = $quote_details['data'][0]['Quote_No'] ?? null;
				$modified_time = $quote_details['data'][0]['Modified_Time'] ?? null;
		
				if (!$quote_number) {
					log_message('error', 'Quote number missing from Zoho CRM response: ' . print_r($quote_details, true));
					return ['error' => 'Quote number not found in Zoho CRM response.'];
				}
				
				$data = [
					// 'id' => $id,
					// 'zoho_crm_id' => $deal_id,
					// 'quote_id' => $quote_id,
					// 'quote_number' => $quote_number,
					'subject' => $proposal_data['subject'],
					'project_name' => $proposal_data['project'],
					'terms_of_payment' => $proposal_data['termsOfPayment'],
					'product_id' => $proposal_data['product_id'],
					'product_name' => $proposal_data['product_name'],
					'product_description' => $proposal_data['itemDescription'],
					'uom' => $proposal_data['uom'],
					'service_charge' => $proposal_data['unitPrice'],
					'kind_attention' => $proposal_data['kind_attention'],
					'specification' => $proposal_data['specification'],
					'brand' => $proposal_data['brand'],
					'warranty' => $proposal_data['warranty'],
					'delivery' => $proposal_data['delivery'],
					'quantity' => $proposal_data['quantity'],
					'valid_until' => $proposal_data['validUntil'],
					'general_exclusion' => $proposal_data['generalExclusion'],
					'updated_at' => $modified_time,
				];
			
				if (!$this->Task_model->save_proposal_data($data, $id)) {
					log_message('error', 'Failed to insert data into Task_model with data: ' . print_r($data, true));
					$this->db->trans_rollback();
					$this->output->set_status_header(500);
					return json_encode(['error' => 'Failed to update Task_quote_model.']);
				}
			
				// Return success with the ID
				echo json_encode(['success' => true, 'message' => 'Proposal edited successfully.', 'id' => $id]);
				return;
		} else {
			log_message('error', 'Failed to update quote: ' . print_r($response_body, true));
			return ['error' => 'Failed to update quote.'];
		}
	}

	public function get_quote_details() {
		// Get the JSON data from the POST request
		$data = json_decode($this->input->raw_input_stream, true);
		
		// Check if QuoteNumber is provided
		if (isset($data['QuoteNumber']) && count($data) === 1) {
			// Fetch quote details from the database
			$quote_number = $data['QuoteNumber'];
			$quote_data = $this->Task_model->get_quote_by_number($quote_number);
	
			// If quote is not found, return error
			if (empty($quote_data)) {
				log_message('error', 'Quote not found for Quote Number: ' . $quote_number);
				$this->output->set_status_header(404);
				echo json_encode(['success' => false, 'error' => 'Quote not found.']);
				return;
			}
	
			// Return the fetched quote data
			echo json_encode(['success' => true, 'data' => $quote_data]);
		} else {
			// Handle the case where QuoteNumber is not provided or invalid
			echo json_encode(['success' => false, 'error' => 'Invalid or missing QuoteNumber.']);
		}
	}			
	
	private function execute_curl_request($url, $headers, $data = null, $method = 'POST') {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
	
		if ($data !== null) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
	
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		return ['http_code' => $http_code, 'body' => $response];
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

	private function response($data, $status = 200) {
		$this->output->set_status_header($status);
		echo json_encode($data);
	}
	
	public function get_deal_number() {
		// Get the raw POST data (since the data is sent as JSON)
		$inputData = json_decode($this->input->raw_input_stream, true);
		$dealId = isset($inputData['deal_id']) ? $inputData['deal_id'] : null;
		log_message('debug', 'Deal ID: ' . $dealId);
		
		if (!$dealId) {
			return $this->output->set_output(json_encode(['success' => false, 'message' => 'Deal ID is required.']));
		}
	
		// Initial attempt to fetch deal details
		$accessToken = $this->get_access_token(); // Retrieve the current access token
		$url = "https://www.zohoapis.com/crm/v2.1/Deals/{$dealId}";
	
		// Function to execute the request with the provided access token
		$dealResponse = $this->executeDealRequest($url, $accessToken);
	
		// Check if the response indicates an expired token
		if ($dealResponse['http_code'] == 401) {
			// Attempt to refresh the access token
			if ($this->refresh_access_token()) {
				// Retry the request with the new token
				$accessToken = $this->get_access_token(); // Get the refreshed token
				$dealResponse = $this->executeDealRequest($url, $accessToken);
	
				// Decode the refreshed response
				$responseData = json_decode($dealResponse['body'], true);
				log_message('debug', 'Fetched deal details: ' . print_r($responseData, true));
	
				if (isset($responseData['data'][0]['DealNumber'])) {
					return $this->output->set_output(json_encode(['success' => true, 'DealNumber' => $responseData['data'][0]['DealNumber']]));
				} else {
					return $this->output->set_output(json_encode(['success' => false, 'message' => 'Deal number not found or failed to fetch after token refresh.']));
				}
			} else {
				log_message('error', 'Failed to refresh access token for fetching deal number.');
				return $this->output->set_output(json_encode(['success' => false, 'message' => 'Failed to refresh access token.']));
			}
		} else {
			// Decode the response if the token was initially valid
			$responseData = json_decode($dealResponse['body'], true);
			log_message('debug', 'Fetched deal details: ' . print_r($responseData, true));
	
			if (isset($responseData['data'][0]['DealNumber'])) {
				return $this->output->set_output(json_encode(['success' => true, 'DealNumber' => $responseData['data'][0]['DealNumber']]));
			} else {
				return $this->output->set_output(json_encode(['success' => false, 'message' => 'Deal number not found or failed to fetch.']));
			}
		}
	}	
	
	// Helper function to execute the cURL request with the current token
	private function executeDealRequest($url, $accessToken) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Zoho-oauthtoken $accessToken"
		]);
	
		$body = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
	
		return ['body' => $body, 'http_code' => $httpCode];
	}
	
	public function fetch_tasks(){
	    // Get the user ID from the session
        $id = $this->session->userdata('id'); 
    
        if (!$id) {
            // If the ID is not found in the session, handle the error
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'User ID not found in session.']);
            // return;
        }
    
        // Retrieve user information by ID
        $user_id = $this->User_model->get_user_id_by_id($id);
    
        if (!$user_id) {
            // If the user ID is not found in the database, handle the error
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'User not found for this ID.']);
            // return;
        }
    
        // Fetch tasks where assigned_to equals the user_id
        $tasks = $this->User_model->get_tasks_by_assigned_user($user_id);
    
        if (empty($tasks)) {
            // If no tasks are found, respond with an appropriate message
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'No tasks found for this user.']);
            // return;
        }
    
        // Pass tasks data to the view
        $data = [
            'tasks' => $tasks
        ];
        
        $this->load->view('auth/myJobs', $data);
	}
			
}
