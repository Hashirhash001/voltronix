<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Login Controller
 *
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB $db
 * @property User_model $User_model
 */

class dashboard extends CI_Controller {
	private $client_id;
    private $client_secret;
	private $refresh_token;
    private $access_token;

	public function __construct() {
		parent::__construct();
		$this->load->library(['form_validation', 'session']);
		$this->load->model('Task_model');
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
		$this->form_validation->set_rules('accountName', 'Account Name', 'required');
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
			log_message('error', 'Validation errors: ' . json_encode($this->form_validation->error_array()));
			$this->output->set_status_header(400);
			echo json_encode(['success' => false, 'errors' => $this->form_validation->error_array()]);
			return;
		}
	
		// Add proposal to Zoho
		$result = $this->add_proposal_to_zoho($data['dealNumber'], $data, '318');
	
		if (isset($result['error'])) {
			log_message('error', 'Failed to add proposal to Zoho: ' . $result['error']);
			return $this->response($result, 500);
		}
	
		// Log successful addition
		log_message('info', 'Proposal successfully added to Zoho for Deal ID: ' . $data['dealNumber']);
	
		return $this->response(['success' => true, 'message' => 'Proposal added successfully.'], 201);
	}        
	
	private function add_proposal_to_zoho($deal_number, $proposal_data, $id) {
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
			$contact_name = $proposal_data['kind_attention'];
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
							'Description' => $product_description 
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
	
		log_message('debug', 'Headers: ' . print_r($headers, true));
		log_message('debug', 'Post Data: ' . $data);
	
		$response = $this->execute_curl_request(
			"https://www.zohoapis.com/crm/v2.1/Quotes",
			$headers,
			$data,
			'POST'
		);
	
		$response_body = json_decode($response['body'], true);
		log_message('debug', 'Response Body: ' . print_r($response_body, true));
	
		if (isset($response_body['data'][0]['details']['id'])) {
			$quote_id = $response_body['data'][0]['details']['id'];
			return ['success' => true, 'message' => 'Quote created successfully.'];
		} else {
			log_message('debug', 'Quote ID not found in Zoho response: ' . json_encode($response_body));
			return ['error' => 'Quote ID not found in Zoho CRM response.'];
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
}
