<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';
// Suppress warnings for PDF generation
error_reporting(E_ERROR | E_PARSE);

/**
 * Login Controller
 *
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB $db
 * @property User_model $User_model
 * @property Task_model $Task_model
 * @property Proposal_model $Proposal_model
 * @property session $session
 */

class Login extends CI_Controller {

	// Add these properties at the top of your Login class
	private $client_id;
	private $client_secret;
	private $refresh_token;
	private $access_token;

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Task_model');
        $this->load->model('Proposal_model');
		$this->load->model('Access_token_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
        // $this->load->library('Pdf');
        $this->load->helper('token_validate');
		$this->load->helper('NumberToWords_helper');

		$this->client_id = '1000.0V519TDSRC8AYUHXMU2SYCAGN9UP3L';
		$this->client_secret = '0f58eea7da1716ec409099063b8f7e42218854e242';
		$this->refresh_token = '1000.4348d34c1a96e813abe7ff21bfc4a04b.0fd6bc7aeb9d83178d3b5f7f893744c8';
    }

	// Add this method to refresh access token
	private function refresh_access_token() {
		$url = "https://accounts.zoho.com/oauth/v2/token";

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
		// Removed curl_close($ch)

		$response_data = json_decode($response, true);

		if (isset($response_data['access_token'])) {
			// Save the new access token in the database
			$this->Access_token_model->save_access_token($response_data['access_token']);
			$this->access_token = $response_data['access_token'];
			return $this->access_token;
		} else {
			log_message('error', 'Failed to refresh access token: ' . $response);
			return false;
		}
	}

	// Add this method to get access token
	private function get_access_token() {
		$this->access_token = $this->Access_token_model->get_access_token();
		return $this->access_token;
	}


	public function check_logged_in() {
		if (!$this->session->userdata('logged_in')) {
			// If not logged in, redirect to login page
            	redirect('signin');
            // log_message('error', 'Unauthorized access - User not logged in');
            // $this->output->set_status_header(401);
            // echo 'Unauthorized access. Please log in.';
            // exit;

		}
	}

    // Display the login page
    public function index() {
        $this->load->view('auth/login');
    }

	public function dealsAndProposals() {
        // Check if the user is logged in
        $this->check_logged_in();
    
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
        $this->load->view('auth/deals-proposals');
    }

    // Handle the login form submission
    public function authenticate() {
		// Set validation rules
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
	
		// Run validation
		if ($this->form_validation->run() === FALSE) {
			// Load the login view with validation errors
			$data['errors'] = validation_errors();
			$this->load->view('auth/login', $data);
		} else {
			// Retrieve the user based on the provided username
			$user = $this->User_model->get_user($this->input->post('username'));
	
			// Check if user exists
			if (!$user) {
				// Specific error: Username not found
				$data['errors'] = 'Username not found.';
				$this->load->view('auth/login', $data);
			} elseif ($user['status'] != 'active') {
				// Specific error: Account is inactive
				$data['errors'] = 'Your account is inactive. Please contact support.';
				$this->load->view('auth/login', $data);
			} elseif (!in_array($user['department'], ['web_app', 'web_and_mobile'])) {
				// unauthorized access
				$data['errors'] = 'Unauthorized access.';
				$this->load->view('auth/login', $data);
			}
			elseif (!password_verify($this->input->post('password'), $user['password'])) {
				// Specific error: Incorrect password
				$data['errors'] = 'Incorrect password. Please try again.';
				$this->load->view('auth/login', $data);
			} else {
				// Set session data for logged-in user
				$this->session->set_userdata([
					'id' => $user['id'],
					'username' => $user['username'],
					'user_id' => $user['user_id'],
					'role' => $user['role'],
					'company' => $user['company'],
					'logged_in' => TRUE
				]);
				
				log_message('debug', 'Session after login: ' . json_encode($this->session->userdata()));

				// Redirect to dashboard
				redirect('web/dashboard'); 
			}
		}
	}
	
    // Logout the user
    public function logout() {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');
        $this->session->sess_destroy();

    	echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    }
    
    public function fetch_tasks(){
		$this->check_logged_in();
		
		log_message('debug', 'Session in fetch_tasks: ' . json_encode($this->session->userdata()));
		
		$id = $this->session->userdata('id'); 

		if (!$id) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'User ID not found in session.']);
			return;
		}

		$user_id = $this->User_model->get_user_id_by_id($id);

		if (!$user_id) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'User not found for this ID.']);
			return;
		}

		// Load initial 10 tasks per category
		$statusMap = [
			'Qualification' => ['Pending'],
			'Site Visit' => ['Site Visit'],
			'Proposal/Price Quote' => ['Proposal'],
			'Closed Won' => ['Close to Won'],
			'Closed Lost' => ['Close to Lost', 'Proposal-Omitted', 'Omitted']
		];

		$tasks = [];
		foreach ($statusMap as $category => $statuses) {
			$categoryTasks = $this->User_model->get_tasks_by_status_paginated($user_id, $statuses, 10, 0);
			$tasks = array_merge($tasks, $categoryTasks);
		}

		if (empty($tasks)) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'No tasks found for this user.']);
			return;
		}

		$data = [
			'tasks' => $tasks
		];
		
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/myJobs', $data);
	}

	public function fetch_tasks_paginated() {
		$this->check_logged_in();
		
		// Get pagination parameters
		$page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
		$category = $this->input->get('category'); // Get the specific category
		$per_page = 10; // Load 10 tasks per category per scroll
		$offset = ($page - 1) * $per_page;
		
		// Get the user ID from the session
		$id = $this->session->userdata('id');
		
		if (!$id) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'User ID not found in session.']));
		}
		
		// Retrieve user information by ID
		$user_id = $this->User_model->get_user_id_by_id($id);
		
		if (!$user_id) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'User not found for this ID.']));
		}
		
		// Map category to statuses
		$statusMap = [
			'Qualification' => ['Pending'],
			'Site Visit' => ['Site Visit'],
			'Proposal/Price Quote' => ['Proposal'],
			'Closed Won' => ['Close to Won'],
			'Closed Lost' => ['Close to Lost', 'Proposal-Omitted', 'Omitted']
		];
		
		if (!isset($statusMap[$category])) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'Invalid category.']));
		}
		
		$statuses = $statusMap[$category];
		
		// Fetch paginated tasks for specific statuses
		$tasks = $this->User_model->get_tasks_by_status_paginated($user_id, $statuses, $per_page, $offset);
		$total_tasks = $this->User_model->get_total_tasks_count_by_status($user_id, $statuses);
		
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'data' => $tasks,
				'has_more' => (count($tasks) === $per_page),
				'total' => $total_tasks,
				'current_page' => $page,
				'category' => $category
			]));
	}
	
	public function view_task($id) {
	    $this->check_logged_in();
		$username = $this->session->userdata('username');
		$data['username'] = $username;
        $task = $this->Task_model->get_task($id);
		
		if (!$task) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'Task not found.']);
			return;
		}
        
        // Fetch the logged-in user's quote access
        $user_id = $this->session->userdata('id'); // Get the logged-in user's ID from the session
        $quote_access = $this->User_model->get_quote_access($user_id);
    
        if ($task) {
            $data['task'] = $task;
            $data['quote_access'] = $quote_access;
			$this->load->view('auth/layout/header');
			$this->load->view('auth/layout/sidebar');
            $this->load->view('auth/job_details', $data); // Load the view and pass the task data
        } else {
            show_404(); // Show a 404 error if the task is not found
        }
    }
    
    public function download_deal_pdf($id) {
		$this->check_logged_in();
	
		$username = $this->session->userdata('username');
		$user = $this->User_model->get_user_by_username($username);
		if (!$user) {
			show_error('User not found', 404);
			return;
		}

		$sale_name = isset($user['sale_name']) ? $user['sale_name'] : null;
	
		$company = isset($user['company']) ? $user['company'] : null;
	
		$data['task'] = $this->Task_model->get_task($id);
		if (!$data['task']) {
			show_error('Task not found', 404);
			return;
		}
	
		$quote_number = $data['task']['quote_number'] ?? null;
	
		// Initialize items array
		$data['items'] = [];
	
		// For VOLTRONIX CONTRACTING LLC, fetch from tasks first, then proposal_items
		if ($company === 'VOLTRONIX CONTRACTING LLC') {
			// Fetch single item from tasks table if product_name exists
			$task_items = $this->Task_model->get_task_items($id);
			if (!empty($task_items)) {
				$data['items'] = array_merge($data['items'], $task_items);
			}
	
			// Fetch additional items from proposal_items table
			$proposal_items = $this->Proposal_model->get_proposal_items($quote_number);
			if (!empty($proposal_items)) {
				$data['items'] = array_merge($data['items'], $proposal_items);
			}
		} else {
			// For other companies (e.g., VOLTRONIX SWITCHGEAR LLC), use only proposal_items
			$data['items'] = $this->Proposal_model->get_proposal_items($quote_number);
		}
	
		// Calculate totals
		$totalAmount = 0;
		$vatAmount = 0;
		$grandTotal = 0;
		foreach ($data['items'] as $item) {
			$serviceCharge = (float)($item['service_charge'] ?? 0);
			$quantity = (float)($item['quantity'] ?? 0);
			$itemDiscount = (float)($item['item_discount'] ?? 0);
			$itemTotal = $quantity * $serviceCharge * (1 - $itemDiscount / 100); // Apply discount if present
			$totalAmount += $itemTotal;
			$vatAmount += $itemTotal * 0.05; // 5% VAT
			$grandTotal += $itemTotal * 1.05; // Total including VAT
		}
		$data['totalAmount'] = $totalAmount;
		$data['vatAmount'] = $vatAmount;
		$data['grandTotal'] = $grandTotal;
	
		$data['username'] = $username;
		$data['sale_name'] = $sale_name;
	
		// Select view based on company
		$company_views = [
			'VOLTRONIX CONTRACTING LLC' => 'quotes/quote_contracting',
			'VOLTRONIX SWITCHGEAR LLC' => 'quotes/quote_switchgear',
		];
		$view = isset($company_views[$company]) ? $company_views[$company] : 'quotes/quote_contracting';
	
		// Configure mPDF based on company
		if ($company === 'VOLTRONIX CONTRACTING LLC') {
			$mpdf = new \Mpdf\Mpdf([
				'margin_top' => 35,
				'margin_bottom' => 20,
				'margin_left' => 4,
				'margin_right' => 4,
				'default_font' => 'yorkten',
				'mode' => 'utf-8',
				'format' => 'A4',
				'autoPageBreak' => true,
			]);
			$backgroundImage = base_url('assets/photos/logo/databg.png');
			$mpdf->SetDefaultBodyCSS('background', "url('{$backgroundImage}')");
			$mpdf->SetDefaultBodyCSS('background-image-resize', 1);
			// Set PDF title
			$mpdf->SetTitle("Quotation - {$quote_number} - VOLTRONIX CONTRACTING LLC");
		} elseif ($company === 'VOLTRONIX SWITCHGEAR LLC') {
			$mpdf = new \Mpdf\Mpdf([
				'margin_top' => 35,
				'margin_bottom' => 35,
				'margin_left' => 2,
				'margin_right' => 2,
				'mode' => 'utf-8',
				'format' => [215, 280],
				'autoPageBreak' => false,
			]);
			$backgroundImage = base_url('assets/photos/logo/switchgear_bg4.png');
			log_message('info', "backgroundImage: {$backgroundImage}");
			$mpdf->SetDefaultBodyCSS('background', "url('{$backgroundImage}')");
			$mpdf->SetDefaultBodyCSS('background-image-resize', 1);
			// Set PDF title
			$mpdf->SetTitle("Quotation - {$quote_number} - VOLTRONIX SWITCHGEAR LLC");
		} else {
            // Default case: use settings similar to VOLTRONIX CONTRACTING LLC
            $mpdf = new \Mpdf\Mpdf([
                'margin_top' => 0,
                'margin_bottom' => 0,
                'margin_left' => 4,
                'margin_right' => 4,
                'default_font' => 'yorkten',
                'mode' => 'utf-8',
                'format' => 'A4',
                'autoPageBreak' => true,
            ]);
            $backgroundImage = base_url('assets/photos/logo/databg.png');
            $mpdf->SetDefaultBodyCSS('background', "url('{$backgroundImage}')");
            $mpdf->SetDefaultBodyCSS('background-image-resize', 1);
        }
	
		ob_start();
		$this->load->view($view, $data);
		$html = ob_get_contents();
		ob_end_clean();
	
		$mpdf->WriteHTML($html);
	
		$filename = 'quote_' . $data['task']['quote_number'] . '.pdf';
		$mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
	}
	
    public function search() {
		$this->check_logged_in();
	
		// Debug session
		log_message('debug', 'Session in fetch_tasks: ' . json_encode($this->session->userdata()));
	
		// Get the user ID from the session
		$id = $this->session->userdata('id');
	
		if (!$id) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'User ID not found in session.']);
			return;
		}
	
		// Retrieve user information by ID
		$user_id = $this->User_model->get_user_id_by_id($id);
	
		if (!$user_id) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'User not found for this ID.']);
			return;
		}
	
		// Get the search query
		$query = $this->input->post('query', true);
	
		// Fetch tasks using Task_model
		$result = $this->Task_model->search_tasks($user_id, $query);
	
		if (empty($result)) {
			// If no results are found
			echo json_encode([
				'success' => false,
				'message' => 'No Jobs found.'
			]);
			return;
		}
	
		// Return all tasks or filtered results
		echo json_encode([
			'success' => true,
			'data' => $result
		]);
	}
    
    private function validate_api_key() {
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;

		return $api_key ? validate_api_key($api_key) : false;
	}
    
    private function _send_response($data, $status_code = 200)
    {
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header($status_code)
            ->set_output(json_encode($data));
    }
    
    public function search2()
    {
        // Validate API key and get user ID
        $user_id = $this->validate_api_key();
        if (!$user_id) {
            return $this->_send_response(['success' => false, 'error' => 'Unauthorized access'], 401);
        }
    
        // Detect Content-Type and retrieve data
        $content_type = $this->input->server('CONTENT_TYPE');
        $data = strpos($content_type, 'application/json') !== false 
            ? json_decode($this->input->raw_input_stream, true) 
            : $this->input->post();
    
        // Handle invalid JSON or empty data
        if ($data === null) {
            return $this->_send_response(['success' => false, 'error' => 'Invalid data format.'], 400);
        }
    
        // Validate input data
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('query', 'Query', 'trim|required');
    
        if ($this->form_validation->run() === false) {
            return $this->_send_response([
                'success' => false,
                'error' => 'Validation failed.',
                'details' => $this->form_validation->error_array(),
            ], 422);
        }
    
        // Extract query from input data
        $query = $data['query'];
    
        // Build the query for tasks specific to the user
        $this->db->select('id, deal_name, deal_number, complaint_info');
        $this->db->from('tasks');
        $this->db->where('assigned_to', $user_id); // Fetch only tasks assigned to this user
        $this->db->order_by('created_at', 'DESC');
    
        if (!empty($query)) {
            // If query is provided, search in deal_name and deal_number
            $this->db->group_start();
            $this->db->like('deal_name', $query);
            $this->db->or_like('deal_number', $query);
            $this->db->group_end();
        }
    
        $result = $this->db->get()->result_array();
    
        if (empty($result)) {
            // If no results are found
            return $this->_send_response([
                'success' => true,
                'message' => 'No tasks found.',
                'data' => []
            ]);
        }
    
        // Return the results in JSON format
        return $this->_send_response([
            'success' => true,
            'data' => $result
        ]);
    }

	public function update_deal($id) {
		$this->check_logged_in();
		
		// Parse input data
		$data = json_decode($this->input->raw_input_stream, true);
		
		if ($data === null) {
			return $this->output
				->set_status_header(400)
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'Invalid data format.']));
		}
		
		// Get task details
		$task = $this->Task_model->get_task($id);
		if (!$task) {
			return $this->output
				->set_status_header(404)
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'Task not found.']));
		}
		
		// Prepare update data for Zoho
		$zoho_data = [
			'data' => [
				[
					'id' => $task['zoho_crm_id'],
					'Deal_Name' => $data['deal_name'] ?? $task['deal_name'],
					'Account_Name' => $data['account_name'] ?? $task['account_name'],
					'Amount' => (float)($data['amount'] ?? $task['service_charge']),
					'Description' => $data['description'] ?? $task['complaint_info'],
				]
			]
		];
		
		// Update in Zoho CRM
		$result = $this->update_deal_in_zoho($zoho_data);
		
		if (isset($result['error'])) {
			return $this->output
				->set_status_header(500)
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => $result['error']]));
		}
		
		// Update local database
		$local_update_data = [
			'deal_name' => $data['deal_name'] ?? $task['deal_name'],
			'account_name' => $data['account_name'] ?? $task['account_name'],
			'service_charge' => $data['amount'] ?? $task['service_charge'],
			'complaint_info' => $data['description'] ?? $task['complaint_info'],
			'customer_email' => $data['customer_email'] ?? $task['customer_email'],
			'customer_contact' => $data['customer_contact'] ?? $task['customer_contact'],
		];
		
		$update_result = $this->Task_model->update_task($id, $local_update_data);

		if (isset($update_result['error'])) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => $update_result['error']]));
		}
		
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode(['success' => true, 'message' => 'Deal updated successfully.']));
	}

	// Update the update_deal_in_zoho method
	private function update_deal_in_zoho($data) {
		// Get fresh access token
		$this->access_token = $this->get_access_token();
		
		if (!$this->access_token) {
			return ['error' => 'Access token not found.'];
		}
		
		$headers = [
			'Content-Type: application/json',
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/Deals");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		
		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		// Removed curl_close($ch)
		
		// Handle 401 (expired token)
		if ($http_code == 401) {
			if ($this->refresh_access_token()) {
				// Retry with new token
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];
				
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/Deals");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				
				$response = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				// Removed curl_close($ch)
			} else {
				log_message('error', 'Failed to refresh access token for deal update.');
				return ['error' => 'Failed to refresh access token.'];
			}
		}
		
		$response_body = json_decode($response, true);
		
		if ($http_code == 200 && isset($response_body['data'][0]['status']) && $response_body['data'][0]['status'] == 'success') {
			return ['success' => true];
		}
		
		log_message('error', 'Zoho API Error: ' . print_r($response_body, true));
		return ['error' => 'Failed to update deal in Zoho CRM: ' . ($response_body['message'] ?? 'Unknown error')];
	}

	public function users() {
		$this->check_logged_in();
		
		// Check if user is admin
		if ($this->session->userdata('role') != '1') {
			show_error('Unauthorized access. Admin only.', 403);
			return;
		}
		
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/users');
	}

	public function get_users_ajax() {
		$this->check_logged_in();
		
		// Check if user is admin
		if ($this->session->userdata('role') != '1') {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'Unauthorized']));
		}
		
		// Get logged-in user ID to exclude
		$logged_in_user_id = $this->session->userdata('id');
		
		// Get DataTables parameters
		$draw = $this->input->post('draw');
		$start = $this->input->post('start');
		$length = $this->input->post('length');
		$search = $this->input->post('search')['value'];
		$order_column_index = $this->input->post('order')[0]['column'];
		$order_dir = $this->input->post('order')[0]['dir'];
		
		// Get filter values
		$role_filter = $this->input->post('role_filter');
		$status_filter = $this->input->post('status_filter');
		$department_filter = $this->input->post('department_filter');
		
		// Column mapping for ordering (removed created_at)
		$columns = ['username', 'email', 'role', 'department', 'company', 'status', 'quote_access'];
		$order_column = $columns[$order_column_index] ?? 'username';
		
		// Get filtered and paginated users (excluding logged-in user)
		$users = $this->User_model->get_users_datatable($start, $length, $search, $order_column, $order_dir, $role_filter, $status_filter, $department_filter, $logged_in_user_id);
		$total_records = $this->User_model->get_users_count($logged_in_user_id);
		$filtered_records = $this->User_model->get_users_filtered_count($search, $role_filter, $status_filter, $department_filter, $logged_in_user_id);
		
		// Format data for DataTables
		$data = [];
		foreach ($users as $user) {
			$data[] = [
				'username' => '<a href="' . site_url('web/users/details/' . $user['id']) . '" class="text-decoration-none fw-bold text-dark user-link">' . $user['username'] . '</a>',
				'email' => $user['email'] ?? '-',
				'role' => $user['role'] == '1' ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-primary">User</span>',
				'department' => $user['department'] ? str_replace('_', ' ', ucwords(str_replace('_', ' ', $user['department']))) : '-',
				'company' => $user['company'] ?? '-',
				'status' => $user['status'] == 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>',
				'quote_access' => $user['quote_access'] == '1' ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-warning">No</span>',
				'actions' => '
					<div class="d-flex gap-2">
						<a href="' . site_url('web/users/details/' . $user['id']) . '" class="btn btn-sm btn-view" title="View Details">
							<i class="bi bi-eye-fill"></i>
						</a>
						<button class="btn btn-sm btn-edit" onclick="editUser(' . $user['id'] . ')" title="Edit User">
							<i class="bi bi-pencil-fill"></i>
						</button>
					</div>'
			];
		}
		
		$response = [
			'draw' => intval($draw),
			'recordsTotal' => $total_records,
			'recordsFiltered' => $filtered_records,
			'data' => $data
		];
		
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}

	public function update_user() {
		$this->check_logged_in();

		// Check if user is admin
		if ($this->session->userdata('role') != '1') {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'Unauthorized']));
		}

		$id = $this->input->post('id');
		$data = [
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'role' => $this->input->post('role'),
			'status' => $this->input->post('status'),
			'department' => $this->input->post('department'),
			'company' => $this->input->post('company'),
			'quote_access' => $this->input->post('quote_access')
		];

		// Get user's Zoho CRM ID before updating
		$user = $this->User_model->get_user_by_id($id);

		if (!$user) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'User not found']));
		}

		// Update local database
		$update_result = $this->User_model->update_user($id, $data);

		if (!$update_result) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'Failed to update user']));
		}

		// Update in Zoho CRM if user_id exists
		if (!empty($user['user_id'])) {
			$zoho_data = [
				'data' => [
					[
						'id' => $user['user_id'],
						'Name' => $data['username'],  // VX App User Name (API: Name)
						'Email' => $data['email'],    // Email (API: Email)
						'Role' => $data['role'] == '1' ? 'Admin' : 'User',  // Role (API: Role)
						'user_status' => ucfirst($data['status']),  // user status (API: user_status)
						'department' => $this->format_department_name($data['department']),  // department (API: department)
						'Company' => $data['company'],  // Company (API: Company)
						'Quote_access' => $data['quote_access'] == '1' ? 'Yes' : 'No'  // Quote access (API: Quote_access)
					]
				]
			];

			$zoho_result = $this->update_user_in_zoho($zoho_data);

			if (isset($zoho_result['error'])) {
				log_message('error', 'Zoho CRM update failed: ' . $zoho_result['error']);
			}
		}

		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode(['success' => true, 'message' => 'User updated successfully']));
	}

	public function edit_user($id) {
		$this->check_logged_in();
		
		// Check if user is admin
		if ($this->session->userdata('role') != '1') {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'Unauthorized']));
		}
		
		$user = $this->User_model->get_user_by_id($id);
		
		if (!$user) {
			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode(['success' => false, 'error' => 'User not found']));
		}
		
		return $this->output
			->set_content_type('application/json')
			->set_output(json_encode(['success' => true, 'data' => $user]));
	}

	private function update_user_in_zoho($data) {
		$this->access_token = $this->get_access_token();

		if (!$this->access_token) {
			return ['error' => 'Access token not found.'];
		}

		$headers = [
			'Content-Type: application/json',
			"Authorization: Zoho-oauthtoken " . $this->access_token
		];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/VZ_App_Users");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		// Handle 401 (expired token)
		if ($http_code == 401) {
			if ($this->refresh_access_token()) {
				// Retry with new token
				$headers = [
					'Content-Type: application/json',
					"Authorization: Zoho-oauthtoken " . $this->access_token
				];

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "https://www.zohoapis.com/crm/v2/VX_App_Users");
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

				$response = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			} else {
				return ['error' => 'Failed to refresh access token.'];
			}
		}

		$response_body = json_decode($response, true);

		if ($http_code == 200 && isset($response_body['data'][0]['status']) && $response_body['data'][0]['status'] == 'success') {
			return ['success' => true];
		}

		log_message('error', 'Zoho API Error: ' . print_r($response_body, true));
		return ['error' => 'Failed to update user in Zoho CRM: ' . ($response_body['message'] ?? 'Unknown error')];
	}

	private function format_department_name($department) {
		// Convert department value to proper display format
		$departments = [
			'web_app' => 'Web App',
			'web_and_mobile' => 'Web and Mobile',
			'sales' => 'Sales'
		];

		return $departments[$department] ?? $department;
	}

	public function user_details($id) {
		$this->check_logged_in();
		
		// Check if user is admin
		if ($this->session->userdata('role') != '1') {
			redirect('web/dashboard');
			return;
		}
		
		// Get user details
		$user = $this->User_model->get_user_by_id($id);
		if (!$user) {
			show_404();
			return;
		}
		
		// Get user's tasks using the working method
		$tasks = $this->User_model->get_tasks_by_assigned_user($user['user_id']);
		
		// Pass data to view
		$data['user'] = $user;
		$data['tasks'] = $tasks;
		
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/user_details', $data);
	}

	public function export_user_tasks_csv($id) {
		$this->check_logged_in();
		
		// Check if user is admin
		if ($this->session->userdata('role') != '1') {
			show_404();
			return;
		}
		
		// Get user details by database ID
		$user = $this->User_model->get_user_by_id($id);
		if (!$user) {
			show_404();
			return;
		}
		
		// Get user's tasks
		$tasks = $this->User_model->get_tasks_by_assigned_user($user['user_id']);
		
		// Apply status filter
		$status_filter = $this->input->get('status_filter');
		$filtered_tasks = $tasks; // Keep original for statistics
		
		if ($status_filter && $status_filter !== 'all') {
			$filtered_tasks = array_filter($tasks, function($task) use ($status_filter) {
				return ($task['status'] ?? null) === $status_filter;
			});
			$filtered_tasks = array_values($filtered_tasks); // Reindex array
		}
		
		// Calculate statistics from ALL tasks (not filtered)
		$total_tasks = count($tasks);
		$status_counts = [
			'Pending' => 0,
			'Site Visit' => 0,
			'Proposal' => 0,
			'Close to Won' => 0,
			'Close to Lost' => 0
		];
		
		$closed_won_total = 0;
		
		// Categorize ALL tasks by their STATUS field
		$tasks_by_status = [
			'Pending' => [],
			'Site Visit' => [],
			'Proposal' => [],
			'Close to Won' => [],
			'Close to Lost' => []
		];
		
		foreach ($tasks as $task) {
			$status = $task['status'] ?? null;
			
			if ($status && isset($status_counts[$status])) {
				$status_counts[$status]++;
				$tasks_by_status[$status][] = $task;
				
				// Calculate closed won total
				if ($status === 'Close to Won') {
					$closed_won_total += floatval($task['service_charge'] ?? 0);
				}
			}
		}
		
		// Prepare CSV content
		$filter_text = $status_filter && $status_filter !== 'all' ? '_' . str_replace(' ', '_', strtolower($status_filter)) : '';
		$filename = 'user_tasks_' . str_replace(' ', '_', strtolower($user['username'])) . $filter_text . '_' . date('Y-m-d_His') . '.csv';
		
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		
		$output = fopen('php://output', 'w');
		
		// Add BOM for Excel UTF-8 support
		fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
		
		// User Information Section
		fputcsv($output, ['USER INFORMATION']);
		fputcsv($output, ['Username', $user['username']]);
		fputcsv($output, ['Email', $user['email'] ?? 'N/A']);
		fputcsv($output, ['Department', $user['department'] ? str_replace('_', ' ', ucwords(str_replace('_', ' ', $user['department']))) : 'N/A']);
		fputcsv($output, ['Company', $user['company'] ?? 'N/A']);
		fputcsv($output, ['Status', ucfirst($user['status'])]);
		fputcsv($output, ['Quote Access', $user['quote_access'] == '1' ? 'Yes' : 'No']);
		fputcsv($output, []);
		
		// Filter Information
		if ($status_filter && $status_filter !== 'all') {
			fputcsv($output, ['FILTER APPLIED']);
			fputcsv($output, ['Status Filter', $status_filter]);
			fputcsv($output, ['Filtered Jobs Count', count($filtered_tasks)]);
			fputcsv($output, []);
		}
		
		// Task Statistics Summary (ALL tasks)
		fputcsv($output, ['OVERALL TASK STATISTICS']);
		fputcsv($output, ['Total Jobs', $total_tasks]);
		fputcsv($output, ['Qualification (Pending)', $status_counts['Pending']]);
		fputcsv($output, ['Site Visit', $status_counts['Site Visit']]);
		fputcsv($output, ['Proposal', $status_counts['Proposal']]);
		fputcsv($output, ['Closed Won (Count)', $status_counts['Close to Won']]);
		fputcsv($output, ['Closed Won (Total Amount)', '₹' . number_format($closed_won_total, 2)]);
		fputcsv($output, ['Closed Lost', $status_counts['Close to Lost']]);
		fputcsv($output, []);
		fputcsv($output, []);
		
		// Export filtered tasks
		if ($status_filter && $status_filter !== 'all') {
			// Single status export
			$status_labels = [
				'Pending' => 'QUALIFICATION (PENDING)',
				'Site Visit' => 'SITE VISIT',
				'Proposal' => 'PROPOSAL',
				'Close to Won' => 'CLOSED WON',
				'Close to Lost' => 'CLOSED LOST'
			];
			
			fputcsv($output, [$status_labels[$status_filter] . ' (' . count($filtered_tasks) . ' Jobs)']);
			
			// Column headers
			fputcsv($output, [
				'Deal Name',
				'Deal Number',
				'Account',
				'Status',
				'Amount',
				'Contact',
				'ENQ Number',
				'ENQ Date',
				'QUAL Number',
				'QUAL Date',
				'SITE Number',
				'SITE Date',
				'QUOTE Number',
				'QUOTE Date',
				'JOB Number',
				'JOB Date',
				'LOST Number',
				'LOST Date',
				'Created Date'
			]);
			
			// Task rows
			foreach ($filtered_tasks as $task) {
				fputcsv($output, [
					$task['deal_name'] ?? '-',
					$task['deal_number'] ?? '-',
					$task['account_name'] ?? '-',
					$task['status'] ?? '-',
					$task['service_charge'] ?? '-',
					$task['customer_contact'] ?? '-',
					$task['enq_number'] ?? '-',
					$task['enq_deal_date'] ? date('d M Y H:i', strtotime($task['enq_deal_date'])) : '-',
					$task['qual_deal_number'] ?? '-',
					$task['qual_deal_date'] ? date('d M Y H:i', strtotime($task['qual_deal_date'])) : '-',
					$task['site_deal_number'] ?? '-',
					$task['site_deal_date'] ? date('d M Y H:i', strtotime($task['site_deal_date'])) : '-',
					$task['quote_deal_number'] ?? '-',
					$task['quote_deal_date'] ? date('d M Y H:i', strtotime($task['quote_deal_date'])) : '-',
					$task['job_deal_number'] ?? '-',
					$task['job_deal_date'] ? date('d M Y H:i', strtotime($task['job_deal_date'])) : '-',
					$task['lost_deal_number'] ?? '-',
					$task['lost_deal_date'] ? date('d M Y H:i', strtotime($task['lost_deal_date'])) : '-',
					$task['created_at'] ? date('d M Y H:i', strtotime($task['created_at'])) : '-'
				]);
			}
		} else {
			// All statuses export (organized by status)
			$status_labels = [
				'Pending' => 'QUALIFICATION (PENDING)',
				'Site Visit' => 'SITE VISIT',
				'Proposal' => 'PROPOSAL',
				'Close to Won' => 'CLOSED WON',
				'Close to Lost' => 'CLOSED LOST'
			];
			
			foreach ($status_labels as $status_key => $status_label) {
				if ($status_counts[$status_key] > 0) {
					// Status header
					fputcsv($output, [$status_label . ' (' . $status_counts[$status_key] . ' Jobs)']);
					
					// Column headers for this section
					fputcsv($output, [
						'Deal Name',
						'Deal Number',
						'Account',
						'Status',
						'Amount',
						'Contact',
						'ENQ Number',
						'ENQ Date',
						'QUAL Number',
						'QUAL Date',
						'SITE Number',
						'SITE Date',
						'QUOTE Number',
						'QUOTE Date',
						'JOB Number',
						'JOB Date',
						'LOST Number',
						'LOST Date',
						'Created Date'
					]);
					
					// Task rows for this status
					foreach ($tasks_by_status[$status_key] as $task) {
						fputcsv($output, [
							$task['deal_name'] ?? '-',
							$task['deal_number'] ?? '-',
							$task['account_name'] ?? '-',
							$task['status'] ?? '-',
							$task['service_charge'] ?? '-',
							$task['customer_contact'] ?? '-',
							$task['enq_number'] ?? '-',
							$task['enq_deal_date'] ? date('d M Y H:i', strtotime($task['enq_deal_date'])) : '-',
							$task['qual_deal_number'] ?? '-',
							$task['qual_deal_date'] ? date('d M Y H:i', strtotime($task['qual_deal_date'])) : '-',
							$task['site_deal_number'] ?? '-',
							$task['site_deal_date'] ? date('d M Y H:i', strtotime($task['site_deal_date'])) : '-',
							$task['quote_deal_number'] ?? '-',
							$task['quote_deal_date'] ? date('d M Y H:i', strtotime($task['quote_deal_date'])) : '-',
							$task['job_deal_number'] ?? '-',
							$task['job_deal_date'] ? date('d M Y H:i', strtotime($task['job_deal_date'])) : '-',
							$task['lost_deal_number'] ?? '-',
							$task['lost_deal_date'] ? date('d M Y H:i', strtotime($task['lost_deal_date'])) : '-',
							$task['created_at'] ? date('d M Y H:i', strtotime($task['created_at'])) : '-'
						]);
					}
					
					// Add blank rows between sections
					fputcsv($output, []);
					fputcsv($output, []);
				}
			}
		}
		
		// If no tasks were found
		if (count($filtered_tasks) == 0) {
			fputcsv($output, ['NO TASKS FOUND']);
			fputcsv($output, ['No tasks match the selected filter.']);
		}
		
		fclose($output);
		exit;
	}

	public function export_user_tasks_pdf($id) {
		$this->check_logged_in();
		
		if ($this->session->userdata('role') != '1') {
			show_404();
			return;
		}
		
		$user = $this->User_model->get_user_by_id($id);
		if (!$user) {
			show_404();
			return;
		}
		
		$tasks = $this->User_model->get_tasks_by_assigned_user($user['user_id']);
		
		// Apply filter
		$status_filter = $this->input->get('status_filter');
		if ($status_filter && $status_filter !== 'all') {
			$tasks = array_filter($tasks, function($task) use ($status_filter) {
				return ($task['status'] ?? null) === $status_filter;
			});
			$tasks = array_values($tasks);
		}
		
		// Initialize status categories
		$categorized_tasks = [
			'Pending' => [],
			'Site Visit' => [],
			'Proposal' => [],
			'Close to Won' => [],
			'Close to Lost' => []
		];
		
		$won_total = 0;
		
		// Categorize tasks properly
		foreach ($tasks as $task) {
			$status = isset($task['status']) && !empty($task['status']) ? trim($task['status']) : 'Pending';
			
			// Map status to categories
			if (isset($categorized_tasks[$status])) {
				$categorized_tasks[$status][] = $task;
			} else {
				// Default to Pending if status doesn't match any category
				$categorized_tasks['Pending'][] = $task;
			}
			
			// Calculate won total
			if ($status === 'Close to Won') {
				$won_total += floatval($task['service_charge'] ?? 0);
			}
		}
		
		// Count by status
		$status_counts = [];
		foreach ($categorized_tasks as $status => $status_tasks) {
			$status_counts[$status] = count($status_tasks);
		}
		
		// Load TCPDF
		require_once(APPPATH . 'libraries/tcpdf/tcpdf.php');
		
		$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator('CRM System');
		$pdf->SetAuthor($user['username']);
		$pdf->SetTitle('User Tasks Report - ' . $user['username']);
		$pdf->SetMargins(12, 18, 12);
		$pdf->SetAutoPageBreak(TRUE, 20);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		
		// PAGE 1: HEADER & OVERVIEW
		$pdf->AddPage();
		
		// Title
		$pdf->SetFont('helvetica', 'B', 24);
		$pdf->SetTextColor(41, 62, 134);
		$pdf->Cell(0, 12, 'User Tasks Report', 0, 1, 'C');
		
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetTextColor(100, 100, 100);
		$pdf->Cell(0, 5, 'Generated on ' . date('d M Y H:i:s'), 0, 1, 'C');
		$pdf->Ln(8);
		
		// User Information Card
		$pdf->SetFont('helvetica', 'B', 11);
		$pdf->SetFillColor(41, 62, 134);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(0, 8, 'USER INFORMATION', 0, 1, 'L', true);
		
		$pdf->SetFont('helvetica', '', 10);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFillColor(245, 245, 245);
		
		$pdf->Cell(50, 7, 'Username:', 0, 0, 'L', true);
		$pdf->SetFont('helvetica', 'B', 10);
		$pdf->Cell(0, 7, $user['username'], 0, 1, 'L', true);
		
		$pdf->SetFont('helvetica', '', 10);
		$pdf->Cell(50, 7, 'Email:', 0, 0, 'L', false);
		$pdf->Cell(0, 7, $user['email'] ?? 'N/A', 0, 1, 'L', false);
		
		$pdf->Cell(50, 7, 'Department:', 0, 0, 'L', true);
		$dept = $user['department'] ? str_replace('_', ' ', ucwords(str_replace('_', ' ', $user['department']))) : 'N/A';
		$pdf->Cell(0, 7, $dept, 0, 1, 'L', true);
		
		$pdf->Cell(50, 7, 'Status:', 0, 0, 'L', false);
		$pdf->SetFont('helvetica', 'B', 10);
		$status_color = $user['status'] == 'active' ? [40, 167, 69] : [100, 100, 100];
		$pdf->SetTextColor($status_color[0], $status_color[1], $status_color[2]);
		$pdf->Cell(0, 7, ucfirst($user['status']), 0, 1, 'L', false);
		
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Ln(6);
		
		// Summary Statistics
		$pdf->SetFont('helvetica', 'B', 11);
		$pdf->SetFillColor(41, 62, 134);
		$pdf->SetTextColor(255, 255, 255);
		$pdf->Cell(0, 8, 'SUMMARY STATISTICS', 0, 1, 'L', true);
		
		$pdf->SetFont('helvetica', '', 9);
		$pdf->SetTextColor(0, 0, 0);
		
		// Stats HTML
		$stats_html = '<style>
			table { border-collapse: collapse; width: 100%; }
			th { background-color: #f5f5f5; font-weight: bold; padding: 8px; border: 1px solid #ddd; font-size: 9px; }
			td { padding: 7px; border: 1px solid #ddd; font-size: 9px; }
			.total { font-weight: bold; background-color: #f0f0f0; }
			.won { background-color: #d4edda; font-weight: bold; color: #155724; }
		</style>
		<table>
			<tr>
				<th style="width: 35%;">Status</th>
				<th style="width: 20%; text-align: center;">Count</th>
				<th style="width: 45%; text-align: right;">Value</th>
			</tr>
			<tr class="total">
				<td><strong>TOTAL JOBS</strong></td>
				<td style="text-align: center;"><strong>' . count($tasks) . '</strong></td>
				<td style="text-align: right;">-</td>
			</tr>
			<tr>
				<td>⏳ Pending (Qualification)</td>
				<td style="text-align: center;">' . $status_counts['Pending'] . '</td>
				<td style="text-align: right;">-</td>
			</tr>
			<tr>
				<td>📍 Site Visit</td>
				<td style="text-align: center;">' . $status_counts['Site Visit'] . '</td>
				<td style="text-align: right;">-</td>
			</tr>
			<tr>
				<td>📋 Proposal</td>
				<td style="text-align: center;">' . $status_counts['Proposal'] . '</td>
				<td style="text-align: right;">-</td>
			</tr>
			<tr class="won">
				<td><strong>✓ Close to Won</strong></td>
				<td style="text-align: center;"><strong>' . $status_counts['Close to Won'] . '</strong></td>
				<td style="text-align: right;"><strong>AED ' . number_format($won_total, 2) . '</strong></td>
			</tr>
			<tr>
				<td>✗ Closed Lost</td>
				<td style="text-align: center;">' . $status_counts['Close to Lost'] . '</td>
				<td style="text-align: right;">-</td>
			</tr>
		</table>';
		
		$pdf->writeHTML($stats_html, true, false, true, false, '');
		$pdf->Ln(8);
		
		// CATEGORIZED TASKS BY STATUS
		foreach ($categorized_tasks as $status => $status_tasks) {
			if (count($status_tasks) == 0) continue;
			
			// Add new page for each status section
			$pdf->AddPage();
			
			// Status Header - Same blue color as page headers
			$pdf->SetFont('helvetica', 'B', 11);
			$pdf->SetFillColor(41, 62, 134);
			$pdf->SetTextColor(255, 255, 255);
			
			// Add emoji based on status
			$icon = '';
			switch($status) {
				case 'Pending': $icon = '⏳ '; break;
				case 'Site Visit': $icon = '📍 '; break;
				case 'Proposal': $icon = '📋 '; break;
				case 'Close to Won': $icon = '✓ '; break;
				case 'Close to Lost': $icon = '✗ '; break;
			}
			
			$pdf->Cell(0, 8, $icon . strtoupper($status) . ' (' . count($status_tasks) . ' Jobs)', 0, 1, 'L', true);
			
			$pdf->SetFont('helvetica', '', 8);
			$pdf->SetTextColor(0, 0, 0);
			
			// Build Tasks Table for this status
			$tasks_html = '<style>
				table { border-collapse: collapse; width: 100%; }
				thead tr { background-color: #293e86; }
				th { 
					font-weight: bold; 
					padding: 7px; 
					border: 1px solid #ddd; 
					font-size: 8px;
					color: #ffffff;
				}
				td { 
					padding: 6px; 
					border: 1px solid #ddd; 
					font-size: 8px;
				}
				tr:nth-child(even) { background-color: #f9f9f9; }
				tr:nth-child(odd) { background-color: #ffffff; }
			</style>
			<table>
				<thead>
				<tr>
					<th style="width: 20%;">Deal Name</th>
					<th style="width: 12%;">Deal #</th>
					<th style="width: 18%;">Account Name</th>
					<th style="width: 13%; text-align: right;">Amount (AED)</th>
					<th style="width: 15%;">Contact</th>
					<th style="width: 12%;">Stage</th>
					<th style="width: 10%;">Days</th>
				</tr>
				</thead>
				<tbody>';
			
			foreach ($status_tasks as $task) {
				// Calculate days in stage
				$created_date = isset($task['created_on']) ? strtotime($task['created_on']) : time();
				$days_in_stage = floor((time() - $created_date) / 86400);
				
				// Determine latest stage
				$stage = 'ENQ';
				if (!empty($task['lost_deal_number'])) $stage = 'LOST';
				elseif (!empty($task['job_deal_number'])) $stage = 'JOB';
				elseif (!empty($task['quote_deal_number'])) $stage = 'QUOTE';
				elseif (!empty($task['site_deal_number'])) $stage = 'SITE';
				elseif (!empty($task['qual_deal_number'])) $stage = 'QUAL';
				
				$deal_name = htmlspecialchars(substr($task['deal_name'] ?? '-', 0, 30));
				$deal_num = htmlspecialchars($task['deal_number'] ?? '-');
				$account = htmlspecialchars(substr($task['account_name'] ?? '-', 0, 25));
				$amount = number_format(floatval($task['service_charge'] ?? 0), 2);
				$contact = htmlspecialchars(substr($task['customer_contact'] ?? '-', 0, 18));
				
				$tasks_html .= '<tr>
					<td>' . $deal_name . '</td>
					<td>' . $deal_num . '</td>
					<td>' . $account . '</td>
					<td style="text-align: right;">AED ' . $amount . '</td>
					<td>' . $contact . '</td>
					<td style="text-align: center;"><strong>' . $stage . '</strong></td>
					<td style="text-align: center;">' . $days_in_stage . 'd</td>
				</tr>';
			}
			
			$tasks_html .= '</tbody></table>';
			
			$pdf->writeHTML($tasks_html, true, false, true, false, '');
		}
		
		// Footer on last page
		$pdf->SetY(-20);
		$pdf->SetFont('helvetica', 'I', 8);
		$pdf->SetTextColor(100, 100, 100);
		$pdf->Cell(0, 5, 'Report generated on ' . date('d M Y \a\t H:i:s A'), 0, 0, 'L');
		$pdf->Cell(0, 5, 'Page ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 1, 'R');
		
		// Output
		if (ob_get_length()) {
			ob_end_clean();
		}
		
		$filter_text = $status_filter && $status_filter !== 'all' ? '_' . str_replace(' ', '_', strtolower($status_filter)) : '';
		$filename = 'user_tasks_' . str_replace(' ', '_', strtolower($user['username'])) . $filter_text . '_' . date('Ymd_His') . '.pdf';
		
		$pdf->Output($filename, 'D');
		exit;
	}

}
