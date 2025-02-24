<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Tasks Controller
 * @property session $session
 * @property User_model $User_model
 * @property form_validation $form_validation
 * @property Task_model $Task_model
 * @property input $input
 */

class Analytics extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('Task_model');
		$this->load->library(['form_validation', 'session']);
		$this->load->helper(['url', 'form']);
		$this->load->library('Pdf');
		$this->load->helper('token_validate');
	}

	public function check_logged_in()
	{
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
	public function index()
	{
		// Check if the user is logged in
		$this->check_logged_in();

		// Fetch assigned deals from Task_model
		$user_id = $this->session->userdata('user_id'); // Assuming user_id is stored in session
		$deals = $this->User_model->get_assigned_deals($user_id);

		// Pass deals data to the view
		$data['deals'] = $deals;

		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/dashboard', $data);
	}
	
}
