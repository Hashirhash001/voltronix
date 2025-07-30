<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tasks Controller
 * @property session $session
 * @property User_model $User_model
 * @property form_validation $form_validation
 * @property User_model $User_model
 * @property User_task_model $User_task_model
 * @property input $input
 * @property output $output
 */

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->helper('token_validate');
    }

    public function register() {
		// Get the raw JSON input
		$json_input = file_get_contents('php://input');
		$data = json_decode($json_input, true);
	
		// Manually set form validation rules
		$this->form_validation->set_data($data);
	
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$this->form_validation->set_rules('user_id', 'User Id', 'required');
		$this->form_validation->set_rules('status', 'Status');
		$this->form_validation->set_rules('department', 'Status');
		$this->form_validation->set_rules('role', 'Role');
		$this->form_validation->set_rules('company', 'Company');
		$this->form_validation->set_rules('sale_name', 'Sale Name');
	
		if ($this->form_validation->run() === FALSE) {
			echo json_encode(array("status" => "error", "message" => validation_errors()));
		} else {
			// Hash the password
			$password_hash = password_hash($data['password'], PASSWORD_BCRYPT);
	
			// Prepare user data
			$user_data = array(
				'username' => $data['username'],
				'password' => $password_hash,
				'email' => $data['email'],
				'user_id' => $data['user_id'],
				'status' => $data['status'],
				'department' => $data['department'],
				'quote_access' => $data['quote_access'],
				'role' => $data['role'],
				'company' => $data['company'],
				'sale_name' => $data['sale_name'],
			);
	
			// Check if the user already exists by email or user_id
			$existing_user = $this->User_model->get_user_by_username_or_id($data['username'], $data['user_id']);
	
			if ($existing_user) {
				// Update the existing user
				$this->User_model->update_user($existing_user['id'], $user_data);
				echo json_encode(array("status" => "success", "message" => "User already exists"));
			} else {
				// Create a new user
				$this->User_model->create_user($user_data);
				echo json_encode(array("status" => "success", "message" => "User registered successfully"));
			}
		}
	}


    public function login() {
        // Set validation rules
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
    
        // Run validation
        if ($this->form_validation->run() === FALSE) {
            // Retrieve and format validation errors
            $errors = $this->form_validation->error_array();
            $formatted_errors = implode(' ', $errors); // Convert array to a single string
            
            $this->output
                ->set_status_header(400)
                ->set_output(json_encode(array("status" => "error", "message" => $formatted_errors)));
        } else {
            // Get user from model
            $user = $this->User_model->get_user($this->input->post('username'));
    
            if ($user['status'] == 'active') {
                // Verify user password
                if ($user && password_verify($this->input->post('password'), $user['password'])) {
                    // Check if user's department is not 'mobile_app'
                    if (!in_array($user['department'], ['mobile_app', 'web_and_mobile'])) {
                        $this->output
                            ->set_status_header(403)
                            ->set_output(json_encode(array(
                                "status" => "error",
                                "message" => "You do not have access."
                            )));
                    } else {
                        // Generate or retrieve the API key
                        if (empty($user['api_key'])) {
                            $api_key = $this->User_model->generate_api_key($user['id']);
                        } else {
                            $api_key = $user['api_key'];
                        }
    
                        $this->output
                            ->set_status_header(200)
                            ->set_output(json_encode(array(
                                "status" => "success",
                                "message" => "Login successful",
                                "user_id" => $user['id'],
                                "app_user_id" => $user['user_id'],
                                "api_key" => $api_key,
                                "department" => $user['department'],
                                "quote_access" => $user['quote_access'],
                            )));
                    }
                } else {
                    $this->output
                        ->set_status_header(401)
                        ->set_output(json_encode(array("status" => "error", "message" => "Invalid username or password")));
                }
            } else {
                $this->output
                    ->set_status_header(401)
                    ->set_output(json_encode(array("status" => "error", "message" => "User is not active")));
            }
        }
    }


	public function logout() {
		// Get the Authorization header
		$headers = $this->input->request_headers();
		$api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
	
		if ($api_key && validate_api_key($api_key)) {
			// Invalidate the API key by clearing it from the user's record
			$user_data = validate_api_key($api_key);
			$this->User_model->clear_api_key($user_data['id']);
	
			echo json_encode(['status' => 'success', 'message' => 'Logout successful']);
		} else {
			// Invalid API key
			$this->output
				->set_status_header(401)
				->set_output(json_encode(['status' => 'error', 'message' => 'Invalid API key']));
		}
	}
	
	public function delete_user() {
        // Get the raw JSON input
        $json_input = file_get_contents('php://input');
        $data = json_decode($json_input, true);
    
        // Validate that the user_id is provided
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('user_id', 'User Id', 'required');
    
        if ($this->form_validation->run() === FALSE) {
            echo json_encode(array("status" => "error", "message" => validation_errors()));
        } else {
            // Attempt to delete the user
            $deleted = $this->User_model->delete_user_by_id($data['user_id']);
    
            if ($deleted) {
                echo json_encode(array("status" => "success", "message" => "User deleted successfully"));
            } else {
                echo json_encode(array("status" => "error", "message" => "User not found or could not be deleted"));
            }
        }
    }

	
	public function validate_token() {
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
        if ($api_key && validate_api_key($api_key)) {
            echo json_encode(['status' => 'success', 'message' => 'Token is valid']);
        } else {
            $this->output
                ->set_status_header(401)
                ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid or expired token']));
        }
    }
}
