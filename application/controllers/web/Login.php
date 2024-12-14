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
    }

	public function check_logged_in() {
		if (!$this->session->userdata('logged_in')) {
			// If not logged in, redirect to login page
			redirect('web/login');
		}
	}

    // Display the login page
    public function index() {
        $this->load->view('auth/login');
    }

	public function dashboard() {
        // Check if the user is logged in
        $this->check_logged_in();
    
        $this->load->view('auth/dashboard');
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

				// Redirect to dashboard
				redirect('web/deals'); 
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
            $this->load->view('auth/task_details', $data); // Load the view and pass the task data
        } else {
            show_404(); // Show a 404 error if the task is not found
        }
    }
    
    public function download_deal_pdf($id) {
		$this->check_logged_in();
    
        // Get task data
      	$data['task'] = $this->Task_model->get_task($id);
      	ob_start();
      	$this->load->view('deal_pdf_view',$data);
		$html = ob_get_contents();
		ob_end_clean();
		$mpdf = new \Mpdf\Mpdf([
			'margin_top' => 0,
			'margin_bottom' => 0,
			'margin_left' => 5,
			'margin_right' => 5,
		]);
		$mpdf->SetTitle('Quote - ' . $data['task']['quote_number']);
		$backgroundImage= base_url().'assets/photos/logo/databg.png';
      	$mpdf->SetDefaultBodyCSS('background', "url('{$backgroundImage}')");
		$mpdf->SetDefaultBodyCSS('background-image-resize', 1);
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
        $this->db->select('id, deal_name, deal_number, complaint_info');
        $this->db->from('tasks');
        $this->db->where('assigned_to', $user_id); // Fetch only tasks assigned to this user
    
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
}
