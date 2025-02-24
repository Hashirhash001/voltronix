<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';
/**
 * Login Controller
 *
 * @property CI_Form_validation $form_validation
 * @property CI_Output $output
 * @property CI_Input $input
 * @property CI_DB $db
 * @property User_model $User_model
 */

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->model('Task_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'form']);
        $this->load->library('Pdf');
        $this->load->helper('token_validate');
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
        
        // Debug session
        log_message('debug', 'Session in fetch_tasks: ' . json_encode($this->session->userdata()));
        
	    // Get the user ID from the session
        $id = $this->session->userdata('id'); 
    
        if (!$id) {
            // If the ID is not found in the session, handle the error
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'User ID not found in session.']);
            return;
        }
    
        // Retrieve user information by ID
        $user_id = $this->User_model->get_user_id_by_id($id);
    
        if (!$user_id) {
            // If the user ID is not found in the database, handle the error
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'User not found for this ID.']);
            return;
        }
    
        // Fetch tasks where assigned_to equals the user_id
        $tasks = $this->User_model->get_tasks_by_assigned_user($user_id);
    
        if (empty($tasks)) {
            // If no tasks are found, respond with an appropriate message
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'No tasks found for this user.']);
            return;
        }
    
        // Pass tasks data to the view
        $data = [
            'tasks' => $tasks
        ];
        
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
        $this->load->view('auth/myJobs', $data);
	}
	
	public function view_task($id) {
	    $this->check_logged_in();
        $this->load->model('Task_model');
        $task = $this->Task_model->get_task($id);
        
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
    
        // Get task data
        $data['task'] = $this->Task_model->get_task($id);
        ob_start();
        $this->load->view('deal_pdf_view', $data);
        $html = ob_get_contents();
        ob_end_clean();
    
        $mpdf = new \Mpdf\Mpdf([
            'margin_top' => 11,
            'margin_bottom' => 0,
            'margin_left' => 6,
            'margin_right' => 6,
            'default_font' => 'yorkten',
        ]);
    
        // Font data for Yorkten
        $mpdf->fontdata['yorkten'] = [
            'R' => $_SERVER['DOCUMENT_ROOT'] . '/voltronix/assets/fonts/Yorkten-NorReg.ttf',
            'B' => $_SERVER['DOCUMENT_ROOT'] . '/voltronix/assets/fonts/Yorkten-NorBol.ttf',
            'I' => $_SERVER['DOCUMENT_ROOT'] . '/voltronix/assets/fonts/Yorkten-NorRegIt.ttf',
            'BI' => $_SERVER['DOCUMENT_ROOT'] . '/voltronix/assets/fonts/Yorkten-NorBolIt.ttf',
        ];
    
        $mpdf->SetTitle('Quote - ' . $data['task']['quote_number']);
        $backgroundImage = base_url() . 'assets/photos/logo/databg.png';
        $mpdf->SetDefaultBodyCSS('background', "url('{$backgroundImage}')");
        $mpdf->SetDefaultBodyCSS('background-image-resize', 1);
    
        // Write HTML to PDF
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
    
        // Build the query for tasks specific to the user
        $this->db->select('id, deal_name, deal_number, complaint_info, status');
        $this->db->from('tasks');
        $this->db->where('assigned_to', $user_id); // Fetch only tasks assigned to this user
        $this->db->order_by('created_at', 'DESC');
    
        if (!empty($query)) {
            // If query is provided, search in deal_name and deal_number
            $this->db->group_start(); // Start grouping WHERE conditions
            $this->db->like('deal_name', $query);
            $this->db->or_like('deal_number', $query);
            $this->db->group_end(); // End grouping
        }
    
        $result = $this->db->get()->result_array();
    
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


}
