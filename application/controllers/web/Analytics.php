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

	// Display the analytics page
	public function index()
	{
		// Check if the user is logged in
		$this->check_logged_in();

		// Get user ID from session
		$user_id = $this->session->userdata('user_id');

		// Get date filter inputs (default to last 30 days if not set)
		$start_date = $this->input->post('start_date') ? $this->input->post('start_date') : date('Y-m-d', strtotime('-180 days'));
		$end_date = $this->input->post('end_date') ? $this->input->post('end_date') : date('Y-m-d');

		// Fetch assigned deals and analytics with date filter
		$deals = $this->User_model->get_assigned_deals($user_id, $start_date, $end_date);
		$analytics = $this->User_model->get_deal_progression_analytics($user_id, $start_date, $end_date, 1, 10); // User ID, start date, end date, page 1, 10 per page

		// Pass deals data and dates to the view
		$data['deals'] = $deals;
		$data['analytics'] = $analytics;
		$data['start_date'] = $start_date;
		$data['end_date'] = $end_date;

		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/dashboard', $data);
	}

	// AJAX endpoint to fetch paginated deals
	public function getPaginatedDeals()
	{
		$user_id = $this->session->userdata('user_id');
		$page = $this->input->post('page') ? $this->input->post('page') : 1;
		$per_page = 10; // Number of records per page, adjustable

		$start_date = '';
		$end_date = '';

		// Fetch paginated analytics data
		$analytics = $this->User_model->get_deal_progression_analytics($user_id, $start_date, $end_date, $page, $per_page);
		$total_deals = $this->User_model->get_total_deals($user_id);
		$total_pages = ceil($total_deals / $per_page);

		// Prepare response
		$response = [
			'deal_progression' => $analytics,
			'total_pages' => $total_pages,
			'current_page' => $page
		];

		// Return JSON response for AJAX
		echo json_encode($response);
		exit;
	}

	// AJAX endpoint to fetch filtered deal status
	public function get_filtered_deal_status()
	{
		$user_id = $this->session->userdata('user_id');
		$start_date = $this->input->post('start_date') ? $this->input->post('start_date') : null;
		$end_date = $this->input->post('end_date') ? $this->input->post('end_date') : null;

		// Fetch deal status counts with or without date filter
		$deals = $this->User_model->get_assigned_deals($user_id, $start_date, $end_date);

		// Aggregate counts
		$deals_status_count = [
			'Site Visit' => 0,
			'Proposal' => 0,
			'Close to Won' => 0,
			'Closed Lost/Omitted' => 0
		];

		foreach ($deals as $deal) {
			switch ($deal['status']) {
				case 'Site Visit':
					$deals_status_count['Site Visit'] += $deal['total'];
					break;
				case 'Proposal':
					$deals_status_count['Proposal'] += $deal['total'];
					break;
				case 'Close to Won':
					$deals_status_count['Close to Won'] += $deal['total'];
					break;
				case 'Close to Lost':
				case 'Omitted':
					$deals_status_count['Closed Lost/Omitted'] += $deal['total'];
					break;
			}
		}

		// Return JSON response
		echo json_encode(['success' => true, 'deals_status_count' => $deals_status_count]);
		exit;
	}
	
}
