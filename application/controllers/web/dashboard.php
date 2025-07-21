<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tasks Controller
 *
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB $db
 * @property Task_model $Task_model
 * @property User_model $User_model
 * @property Access_token_model $Access_token_model
 * @property Proposal_model $Proposal_model
 * @property session $session
 */

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
		$this->load->model('Proposal_model');
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
	
		// Parse JSON input
		$data = json_decode($this->input->raw_input_stream, true);
	
		if ($data === null) {
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
			return;
		}
	
		// Check if a proposal already exists for this deal number in the tasks table
		$task = $this->Task_model->get_task_by_deal_number($data['dealNumber']);
		if ($task && !is_null($task->quote_number)) {
			$this->output->set_status_header(409); // Conflict status code
			echo json_encode([
				'success' => false,
				'error' => 'A proposal already exists for this deal number: ' . $data['dealNumber']
			]);
			return;
		}
	
		// Set validation rules for required fields
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('dealNumber', 'Deal Number', 'required');
		$this->form_validation->set_rules('subject', 'Subject', 'required');
		// $this->form_validation->set_rules('kind_attention', 'Kind Attention', 'required');
		// $this->form_validation->set_rules('project', 'Project Name', 'required');
		// $this->form_validation->set_rules('termsOfPayment', 'Terms of Payment', 'required');
		// $this->form_validation->set_rules('specification', 'Specification', 'required');
		// $this->form_validation->set_rules('generalExclusion', 'General Exclusion', 'required');
		// $this->form_validation->set_rules('brand', 'Brand', 'required');
		// $this->form_validation->set_rules('warranty', 'Warranty', 'required');
		// $this->form_validation->set_rules('delivery', 'Delivery', 'required');
		// $this->form_validation->set_rules('validUntil', 'Valid Until', 'required');
	
		// Validate each item in the items array
		if (!empty($data['items'])) {
			foreach ($data['items'] as $index => $item) {
				$this->form_validation->set_rules("items[$index][itemName]", "Item Name for item $index", 'required');
				$this->form_validation->set_rules("items[$index][quantity]", "Quantity for item $index", 'required|numeric');
				$this->form_validation->set_rules("items[$index][unitPrice]", "Unit Price for item $index", 'required|numeric');
				$this->form_validation->set_rules("items[$index][itemDiscount]", "Discount % for item $index", 'required|numeric');
			}
		} else {
			$this->output->set_status_header(400);
			echo json_encode(['error' => 'At least one item is required.']);
			return;
		}
	
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
	
		return $this->response(['success' => true, 'message' => 'Proposal added successfully.', 'id' => $result['id']], 201);
	}        
	
	private function add_proposal_to_zoho($deal_number, $proposal_data) {
		ini_set('display_errors', 0); // Hide errors from being directly output to the client
		ini_set('log_errors', 1); // Enable error logging

		// Step 1: Create a new contact in Zoho CRM
		$contact_name = !empty(trim($proposal_data['kind_attention'] ?? '')) ? trim($proposal_data['kind_attention']) : 'Default Contact';
		if (!preg_match('/^[a-zA-Z\s]+$/', $contact_name)) {
			$contact_name = 'Default Contact'; // Fallback to default if invalid characters
		}
	
		// Step 1: Create a new contact in Zoho CRM
		$contact_data = json_encode([
			'data' => [
				[
					'Last_Name' => $contact_name,
					'First_Name' => '', // Optional, can be set if needed
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
		} else {
			log_message('error', 'Failed to create contact in Zoho CRM: ' . print_r($contact_response_body, true));
			return ['error' => 'Failed to create contact.'];
		}
	
		// Step 2: Get the Deal ID using DealNumber
		$deal_response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Deals/search?criteria=(QualDealNumber:equals:$deal_number)",
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
					"https://www.zohoapis.com/crm/v2.1/Deals/search?criteria=(QualDealNumber:equals:$deal_number)",
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
	
		// Step 3: Prepare data for Zoho Quote creation
		$quoted_items = [];
		$unitPrice = 0; // To calculate total unit price for deal amount
		foreach ($proposal_data['items'] as $item) {
			$item_total = (float)$item['unitPrice'] * (float)$item['quantity'];
			$unitPrice += $item_total; // Accumulate total amount for the deal
			$quoted_items[] = [
				'Product_Name' => $item['itemName'],
				'Quantity' => (float) $item['quantity'],
				'List_Price' => (float) $item['unitPrice'],
				'ItemDiscount' => (float) $item['itemDiscount'],
				'U_O_M' => $item['uom'],
				'Description' => $item['itemDescription'],
			];
		}
	
		$data = json_encode([
			'data' => [
				[
					'Deal_Name' => $deal_id,
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
							'id' => '5653678000000021003', // Zoho ID for VAT
							'value' => (float) (((float) $proposal_data['subTotal'] - (float) $proposal_data['discount']) * 5 / 100),
						]
					],
					'Adjustment' => (float) ($proposal_data['adjustment'] ?? 0),
					'Grand_Total' => (float) ($proposal_data['grandTotal'] ?? 0),
					'Contact_Name' => [
						'id' => $contact_id,
					],
					'Quoted_Items' => $quoted_items,
				]
			]
		]);
	
		// Step 4: Submit the quote to Zoho
		$this->access_token = $this->get_access_token();
	
		if (!$this->access_token) {
			return ['error' => 'Access token not found.'];
		}
	
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
	
		// Log the full Zoho CRM response for debugging
		log_message('debug', 'Zoho CRM Response: ' . print_r($response_body, true));
	
		// Step 5: Handle Zoho response and store data in the database
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
				log_message('error', 'Quote number missing from Zoho CRM response: ' . print_r($response_body, true));
				return ['error' => 'Quote number not found in Zoho CRM response.'];
			}
	
			// Prepare quote data for database
			$quote_data = [
				'quote_id' => $quote_id,
				'quote_number' => $quote_number,
				'subject' => $proposal_data['subject'],
				'project_name' => $proposal_data['project'],
				'terms_of_payment' => $proposal_data['termsOfPayment'],
				'kind_attention' => $proposal_data['kind_attention'],
				'specification' => $proposal_data['specification'],
				'general_exclusion' => $proposal_data['generalExclusion'],
				'brand' => $proposal_data['brand'],
				'warranty' => $proposal_data['warranty'],
				'delivery' => $proposal_data['delivery'],
				'notes' => $proposal_data['notes'],
				'valid_until' => $proposal_data['validUntil'],
				// 'sub_total' => $proposal_data['subTotal'],
				'discount' => $proposal_data['discount'],
				'adjustment' => $proposal_data['adjustment'],
				'updated_at' => $modified_time
				// 'grand_total' => $proposal_data['grandTotal'],
			];
	
			// Save quote data to the database
			$this->Task_model->save_proposal_data($quote_data, $id);
	
			// Prepare items data for database
			$items_data = [];
			foreach ($proposal_data['items'] as $item) {
				$items_data[] = [
					'task_id' => $id,
					'quote_number' => $quote_number,
					'product_id' => $item['product_id'],
					'product_name' => $item['product_name'],
					'product_description' => $item['itemDescription'],
					'uom' => $item['uom'],
					'quantity' => $item['quantity'],
					'service_charge' => $item['unitPrice'],
					'item_discount' => $item['itemDiscount'],
					'total' => $item['total'],
				];
			}
	
			// Save items data to the database
			$this->Proposal_model->save_proposal_items($items_data);
			
			// Step 6: Update Deal status to "Proposal" in Zoho CRM
			$deal_update_result = $this->update_deal_in_zoho($deal_id, 'Proposal/Price Quote', $unitPrice);

			if (isset($deal_update_result['error'])) {
				log_message('error', 'Failed to update deal status: ' . $deal_update_result['error']);
				// Optionally, you can decide whether to fail the entire request here or just log it
				return ['error' => 'Failed to update deal status: ' . $deal_update_result['error']];
			}
	
			return ['success' => true, 'quote_id' => $quote_id, 'id' => $id];
		} else {
			log_message('error', 'Failed to create quote in Zoho CRM: ' . print_r($response_body, true));
			return ['error' => 'Failed to create quote in Zoho CRM.'];
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
		$this->check_logged_in();
		$data = json_decode($this->input->raw_input_stream, true) ?: $this->input->post();
	
		if ($data === null) {
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
			return;
		}
	
		$this->form_validation->set_rules('QuoteNumber', 'Quote Number', 'required');
		$this->form_validation->set_rules('subject', 'Subject', 'required');
		$this->form_validation->set_rules('project', 'Project Name', 'required');
		$this->form_validation->set_rules('termsOfPayment', 'Terms of Payment', 'required');
	
		$_POST = $data;
		if ($this->form_validation->run() == FALSE) {
			$errors = $this->form_validation->error_array();
			$errorMessage = reset($errors);
			$this->output->set_status_header(400);
			echo json_encode(['error' => $errorMessage ?: 'Validation failed.']);
			return;
		}
	
		$result = $this->update_quote_in_zoho($data['QuoteNumber'], $data);
	
		if (isset($result['error'])) {
			log_message('error', 'Failed to edit proposal in Zoho: ' . $result['error']);
			$this->output->set_status_header(500);
			echo json_encode(['success' => false, 'error' => $result['error']]);
			return;
		}
	
		echo json_encode(['success' => true, 'message' => 'Proposal edited successfully.', 'id' => $result['id']]);
	}

	private function update_quote_in_zoho($quote_deal_number, $proposal_data) {
		ini_set('display_errors', 0); // Hide errors from being directly output to the client
		ini_set('log_errors', 1);    // Enable error logging

		// Transform QUOTE-1254 to VTNX-1254
		$quote_number = str_replace("QUOTE", "VTNX", $quote_deal_number);
	
		print_r($quote_number);
		
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
		$deal_id = $quote_data['data'][0]['Deal_Name']['id'] ?? null;
	
		// Step 2: Create or update contact in Zoho CRM
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
	
		$contact_response_body = json_decode($contact_response['body'], true);
	
		if ($contact_response['http_code'] == 401) {
			if ($this->refresh_access_token()) {
				$this->access_token = $this->get_access_token();
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
			} else {
				log_message('error', 'Failed to refresh access token for contact creation.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
	
		if (isset($contact_response_body['data'][0]['details']['id'])) {
			$contact_id = $contact_response_body['data'][0]['details']['id'];
			$contact_name = $proposal_data['kind_attention'];
		} else {
			log_message('error', 'Failed to create contact in Zoho CRM: ' . print_r($contact_response_body, true));
			return ['error' => 'Failed to create contact.'];
		}
	
		// Step 3: Prepare quoted items from $proposal_data['items']
		$quoted_items = [];
		foreach ($proposal_data['items'] as $item) {
			$quoted_items[] = [
				'Product_Name' => [
					'id' => $item['itemName'], // Assuming itemName is the product ID
					'name' => $item['product_name'] // Human-readable name
				],
				'Quantity' => (float) $item['quantity'],
				'List_Price' => (float) $item['unitPrice'],
				'Discount' => (float) $item['itemDiscount'],
				'U_O_M' => $item['uom'],
				'Description' => $item['itemDescription'] ?? ''
			];
		}
	
		// Step 4: Prepare data for updating the quote in Zoho
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
							'percentage' => 5,
							'name' => 'Vat',
							'id' => '5653678000000021003',
							'value' => (float) (((float) $proposal_data['subTotal'] - (float) $proposal_data['discount']) * 5 / 100)
						]
					],
					'Adjustment' => (float) ($proposal_data['adjustment'] ?? 0),
					'Grand_Total' => (float) ($proposal_data['grandTotal'] ?? 0),
					'Contact_Name' => [
						'id' => $contact_id,
						'name' => $contact_name
					],
					'Quoted_Items' => $quoted_items
				]
			]
		]);
	
		// Step 5: Update the quote in Zoho CRM
		$headers = [
			'Content-Type: application/json',
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
	
		$response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Quotes/$quote_id",
			$headers,
			$data,
			'PATCH'
		);
	
		$response_body = json_decode($response['body'], true);
	
		if (isset($response_body['data'][0]['code']) && $response_body['data'][0]['code'] === 'SUCCESS') {
			// Fetch updated quote details
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
	
			// Fetch task ID using quote_id (assuming Task_model method exists)
			$id = $this->Task_model->get_id_by_quote_id($quote_id);
	
			if (!$id) {
				log_message('error', 'No task found for Quote ID: ' . $quote_id);
				return ['error' => 'No task found for the provided quote ID.'];
			}
	
			// Prepare quote data for database update
			$quote_data = [
				'quote_id' => $quote_id,
				'quote_number' => $quote_number,
				'subject' => $proposal_data['subject'],
				'project_name' => $proposal_data['project'],
				'terms_of_payment' => $proposal_data['termsOfPayment'],
				'kind_attention' => $proposal_data['kind_attention'],
				'specification' => $proposal_data['specification'],
				'general_exclusion' => $proposal_data['generalExclusion'],
				'brand' => $proposal_data['brand'],
				'warranty' => $proposal_data['warranty'],
				'delivery' => $proposal_data['delivery'],
				'notes' => $proposal_data['notes'],
				'valid_until' => $proposal_data['validUntil'],
				'discount' => $proposal_data['discount'],
				'adjustment' => $proposal_data['adjustment'],
				'updated_at' => $modified_time
			];
	
			// Save quote data to the database
			if (!$this->Task_model->save_proposal_data($quote_data, $id)) {
				log_message('error', 'Failed to update Task_model with data: ' . print_r($quote_data, true));
				return ['error' => 'Failed to update quote data in database.'];
			}
	
			// Prepare and save items data to the database
			$items_data = [];
			foreach ($proposal_data['items'] as $item) {
				$items_data[] = [
					'task_id' => $id,
					'quote_number' => $quote_number,
					'product_id' => $item['product_id'],
					'product_name' => $item['product_name'],
					'product_description' => $item['itemDescription'],
					'uom' => $item['uom'],
					'quantity' => $item['quantity'],
					'service_charge' => $item['unitPrice'],
					'item_discount' => $item['itemDiscount'],
					'total' => $item['total']
				];
			}
	
			// Delete existing items and save updated items (to sync with Zoho)
			$this->Proposal_model->delete_proposal_items_by_quote_number($quote_number); // Add this method if not exists
			$this->Proposal_model->save_proposal_items($items_data);
	
			return ['success' => true, 'quote_id' => $quote_id, 'id' => $id];
		} else {
			log_message('error', 'Failed to update quote in Zoho CRM: ' . print_r($response_body, true));
			return ['error' => 'Failed to update quote in Zoho CRM.'];
		}
	}
	
	public function get_quote_details() {
		// Get the JSON data from the POST request
		$data = json_decode($this->input->raw_input_stream, true);
	
		// Check if QuoteNumber is provided
		if (isset($data['QuoteNumber']) && count($data) === 1) {
			// Fetch quote details from the database
			$quote_number = $data['QuoteNumber'];
			// Fetch the logged-in user's id
			$id = $this->session->userdata('id');
			// Fetch the user_id based on the id
			$user_id = $this->User_model->get_user_id_by_id($id);
	
			// Fetch main quote data
			$quote_data = $this->Task_model->get_quote_by_number($quote_number, $user_id);
            
			// If quote is not found, return error
			if (empty($quote_data)) {
				log_message('error', 'Quote not found for Quote Number: ' . $quote_number);
				$this->output->set_status_header(200);
				echo json_encode(['success' => false, 'error' => 'Quote not found.']);
				return;
			}

			$task_id = $quote_data['id'];
	
			// Fetch associated items
			$items = $this->Proposal_model->get_proposal_items_by_task_id($task_id);
			if (empty($items)) {
				$items = []; // Ensure items is always an array
			}
	
			// Return the fetched quote data and items separately
			echo json_encode([
				'success' => true,
				'data' => $quote_data, // Main quote details
				'items' => $items       // Array of associated items
			]);
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
                    // Process the DealNumber to replace VTNX with DEAL
                    $originalDealNumber = $responseData['data'][0]['DealNumber'];
                    $newDealNumber = preg_replace('/^VTNX-/', 'DEAL-', $originalDealNumber);
                    return $this->output->set_output(json_encode(['success' => true, 'DealNumber' => $newDealNumber]));
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
                // Process the DealNumber to replace VTNX with DEAL
                $originalDealNumber = $responseData['data'][0]['DealNumber'];
                $newDealNumber = preg_replace('/^VTNX-/', 'DEAL-', $originalDealNumber);
                return $this->output->set_output(json_encode(['success' => true, 'DealNumber' => $newDealNumber]));
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

	private function send_request($url, $headers, $payload = null) {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($payload) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
    
        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        return [
            'status_code' => $status_code,
            'response' => json_decode($response, true)
        ];
    }

	public function create_item_in_zoho() {
		$content_type = $this->input->server('CONTENT_TYPE');
		$data = strpos($content_type, 'application/json') !== false ? json_decode($this->input->raw_input_stream, true) : $this->input->post();
	
		if ($data === null) {
			log_message('error', 'Invalid data format received.');
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'error' => 'Invalid data format.']);
			return;
		}
	
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules('Product_Name', 'Item Name', 'required');
		$this->form_validation->set_rules('Description', 'Description');
		$this->form_validation->set_rules('VZ_app_user_id', 'VZ App User ID', 'required');
	
		if ($this->form_validation->run() == FALSE) {
			log_message('error', 'Form validation failed: ' . json_encode($this->form_validation->error_array()));
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'errors' => $this->form_validation->error_array()]);
			return;
		}
	
		$access_token = $this->get_access_token();
		$headers = ["Authorization: Bearer $access_token", "Content-Type: application/json"];
		$url = "https://www.zohoapis.com/crm/v2/Products";
	
		// Check if a product with the same name already exists
		$product_name = $data['Product_Name'];
		$encoded_product_name = urlencode($product_name);
		$check_duplicate_url = "https://www.zohoapis.com/crm/v2/Products/search?criteria=(Product_Name:equals:$encoded_product_name)";
		
		log_message('error', 'Checking duplicate product name with URL: ' . $check_duplicate_url);
	
		$response = $this->send_request($check_duplicate_url, $headers);
	
		if ($response['status_code'] !== 200) {
			log_message('error', 'Error while checking for duplicate product: ' . json_encode($response));
		} else {
			log_message('error', 'Duplicate check response: ' . json_encode($response['response']));
		}
	
		if ($response['status_code'] === 200 && isset($response['response']['data']) && count($response['response']['data']) > 0) {
			log_message('error', 'Duplicate product found with name: ' . $product_name);
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'error' => 'An item with this name already exists. Please enter a unique name.']);
			return;
		}
	
		// Proceed with creating the item in Zoho CRM
		$max_attempts = 5;
		$attempt = 0;
	
		do {
			$zoho_data = [
				'Product_Name' => $data['Product_Name'],
				'Description' => $data['Description'],
				'VZ_app_user' => ['id' => $data['VZ_app_user_id']],
				'Owner' => '5653678000000401001',
				'Product_Active' => true
			];
	
			$payload = json_encode(['data' => [$zoho_data]]);
			log_message('error', 'Creating product attempt #' . ($attempt + 1) . ' with payload: ' . $payload);
	
			$response = $this->send_request($url, $headers, $payload);
			$attempt++;
	
			if ($response['status_code'] !== 201) {
				log_message('error', 'Create product response: ' . json_encode($response));
			}
		} while ($response['status_code'] === 200 && isset($response['response']['data'][0]['code']) && $response['response']['data'][0]['code'] === 'DUPLICATE_DATA' && $attempt < $max_attempts);
	
		if ($response['status_code'] === 201 && isset($response['response']['data'][0]['details']['id'])) {
			$zoho_product_id = $response['response']['data'][0]['details']['id'];
			log_message('error', 'Product created successfully with ID: ' . $zoho_product_id);
			echo json_encode([
				'success' => true,
				'product_id' => $zoho_product_id,
				'message' => 'Item created successfully in Zoho CRM'
			]);
		} elseif ($response['status_code'] === 401) {
			log_message('error', 'Access token expired. Attempting to refresh.');
			$access_token = $this->refresh_access_token();
			if ($access_token) {
				log_message('error', 'Access token refreshed successfully. Retrying...');
				$this->create_item_in_zoho();
			} else {
				log_message('error', 'Failed to refresh access token.');
				$this->output->set_status_header(401);
				echo json_encode(['success' => false, 'error' => 'Failed to retrieve or refresh access token']);
			}
		} else {
			log_message('error', 'Failed to create item in Zoho CRM: ' . json_encode($response));
			$this->output->set_status_header(500);
			echo json_encode([
				'success' => false,
				'error' => 'Failed to create item in Zoho CRM',
				'details' => $response['response']
			]);
		}
	}
	
    
    /**
     * Helper function to return error response
     */
    private function response_error($message, $status_code = 400, $details = null) {
        $this->output->set_status_header($status_code);
        echo json_encode([
            'success' => false,
            'error' => is_array($message) ? 'Validation errors occurred.' : $message,
            'errors' => is_array($message) ? $message : null,
            'details' => $details
        ]);
        return;
    }
			
}
