<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tasks Controller
 * @property session $session
 * @property User_model $User_model
 */

class Task extends CI_Controller {
    
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
		// Check if the user is logged in
		$this->check_logged_in();
	
		
	}
	
}
