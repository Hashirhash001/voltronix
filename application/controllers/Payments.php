<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Payment_model');
        $this->load->model('User_model');
        $this->load->helper('json_input');
        $this->load->library('form_validation');
        $this->load->helper('token_validate');
    }

    public function view_by_user($user_id) {
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
    
        // Check if the user_id from the URL matches the one from the token
        if ($header_user_id != $user_id) {
            $this->output->set_status_header(403); // Forbidden
            echo json_encode(['error' => 'Forbidden: User ID mismatch']);
            return;
        }
        
        $this->form_validation->set_data(['user_id' => $user_id]);
        $this->form_validation->set_rules('user_id', 'User ID', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            $this->output->set_status_header(400);
            echo json_encode(['error' => $errors]);
            return;
        }

        $payments = $this->Payment_model->get_payments_by_user($user_id);

        if (empty($payments)) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'No payment records found for this user.']);
        } else {
            $this->output->set_status_header(200);
            echo json_encode($payments);
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
}
