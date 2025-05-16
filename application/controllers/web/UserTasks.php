<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Tasks Controller
 * @property session $session
 * @property User_model $User_model
 * @property form_validation $form_validation
 * @property Task_model $Task_model
 * @property User_task_model $User_task_model
 * @property input $input
 */

class UserTasks extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('User_model');
		$this->load->model('Task_model');
		$this->load->model('User_task_model');
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

	// Assign a task view
	public function assignTask()
	{
		$data['current_user_id'] = $this->session->userdata('user_id');
		$data['role'] = $this->session->userdata('role');
		$data['id'] = $this->session->userdata('id');

		// Check if the current user is authorized
		$current_user_id = $this->session->userdata('user_id');

		if ($data['role'] !== '1') {
			show_error('Unauthorized access', 403);
			return;
		}

		$data['members'] = $this->User_task_model->get_team_members($data['id']); // Fetch team members

		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/task_assign', $data);
	}

	// Assign a task
	public function assign()
	{
		$current_user_id = $this->session->userdata('user_id');

		$data['role'] = $this->session->userdata('role');
        
		if ($data['role'] != '1') {
			show_error('Unauthorized access', 403);
			return;
		}

		$this->form_validation->set_rules('task_title', 'Task Title', 'required');
		$this->form_validation->set_rules('assigned_to', 'Assigned To', 'required');
		$this->form_validation->set_rules('due_date', 'Due Date', 'required');
		$this->form_validation->set_rules('priority', 'Priority', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->assignTask();
		} else {
			// Generate a unique task_id with prefix "VTSK"
			$unique_id = uniqid(); // Generates a unique string
			$task_id = 'VTSK-' . strtoupper(substr(md5($unique_id), 0, 5)); // Shortened hash for uniqueness

			$task_data = array(
				'task_id'     => $task_id,
				'title'       => $this->input->post('task_title'),
				'assigned_to' => $this->input->post('assigned_to'),
				'due_date'    => $this->input->post('due_date'),
				'priority'    => $this->input->post('priority'),
				'description' => $this->input->post('description'),
				'created_by'  => $current_user_id
			);

			if ($this->User_task_model->assign_task($task_data)) {
				$this->session->set_flashdata('success', 'Task assigned successfully.');
			} else {
				$this->session->set_flashdata('error', 'Failed to assign task.');
			}

			redirect('web/assignTask/all-tasks');
		}
	}

	// View all tasks
	public function all_tasks()
	{
		$data['current_user_id'] = $this->session->userdata('user_id');
		$data['role'] = $this->session->userdata('role');
		$data['id'] = $this->session->userdata('id');

		// Check if the current user is authorized
		$current_user_id = $this->session->userdata('user_id');
		if ($data['role'] != '1') {
			show_error('Unauthorized access', 403);
			return;
		}

		// Fetch members and pass them to the view
		$data['members'] = $this->User_task_model->get_all_members($current_user_id);

		// If AJAX request, return paginated tasks
		if ($this->input->is_ajax_request()) {
			$page = $this->input->get('page', TRUE) ?? 1;
			$limit = 10;
			$offset = ($page - 1) * $limit;

			// Get filters
			$search = $this->input->get('search', TRUE);
			$assigned_to = $this->input->get('assigned_to', TRUE);
			$status = $this->input->get('status', TRUE);
			$priority = $this->input->get('priority');

			// Fetch paginated & filtered tasks
			$tasks = $this->User_task_model->get_assigned_tasks($current_user_id, $limit, $offset, $search, $assigned_to, $status, $priority);
			$total_tasks = $this->User_task_model->count_tasks($current_user_id, $search, $assigned_to, $status, $priority);
			$total_pages = ceil($total_tasks / $limit);

			// Generate pagination links with modern styling
			$pagination = "<nav aria-label='Page navigation'><ul class='pagination justify-content-center'>";

			// Previous Button
			if ($page > 1) {
				$pagination .= "<li class='page-item'>
									<a class='page-link rounded-pill' href='#' data-page='" . ($page - 1) . "'>
										<i class='fas fa-chevron-left'></i>
									</a>
								</li>";
			}

			// Page Numbers
			for ($i = 1; $i <= $total_pages; $i++) {
				$active = ($i == $page) ? 'active' : '';
				$pagination .= "<li class='page-item $active'>
									<a class='page-link rounded-pill' href='#' data-page='$i'>$i</a>
								</li>";
			}

			// Next Button
			if ($page < $total_pages) {
				$pagination .= "<li class='page-item'>
									<a class='page-link rounded-pill' href='#' data-page='" . ($page + 1) . "'>
										<i class='fas fa-chevron-right'></i>
									</a>
								</li>";
			}

			$pagination .= "</ul></nav>";

			// Return JSON response
			echo json_encode([
				"success" => true,
				"tasks" => $tasks,
				"pagination" => $pagination,
				"per_page" => $limit,
				"current_page" => $page,
				"total_pages" => $total_pages
			]);
			return;
		}

		// Load the main page view for non-AJAX requests
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/all_tasks', $data);
	}

	public function get_task()
	{
		$taskId = $this->input->post('id');
		$task = $this->User_task_model->get_task_by_id($taskId);

		if ($task) {
			echo json_encode($task);
		} else {
			echo json_encode(null);
		}
	}

	public function update()
	{
		$taskId = $this->input->post('task_id');
		$data = [
			'title' => $this->input->post('task_title'),
			'assigned_to' => $this->input->post('assigned_to'),
			'due_date' => $this->input->post('due_date'),
			'priority' => $this->input->post('priority'),
			'description' => $this->input->post('description')
		];

		if ($this->User_task_model->update_task($taskId, $data)) {
			echo json_encode(["success" => true, "message" => "Task updated successfully!"]);
		} else {
			echo json_encode(["success" => false, "message" => "Failed to update task."]);
		}
	}

	public function delete()
	{
		$this->load->model('Task_model'); // Load the model
		$taskId = $this->input->post('id');

		if ($taskId && $this->User_task_model->delete_task($taskId)) {
			echo json_encode(["success" => true, "message" => "Task deleted successfully."]);
		} else {
			echo json_encode(["success" => false, "message" => "Failed to delete task."]);
		}
	}

	public function my_tasks()
	{
		$current_user_id = $this->session->userdata('user_id');

		if (!$current_user_id) {
			show_error('Unauthorized access', 403);
			return;
		}

		// Fetch members and pass them to the view
		$data['members'] = $this->User_task_model->get_all_members($current_user_id);

		if ($this->input->is_ajax_request()) {
			$page = $this->input->get('page', TRUE) ?? 1;
			$limit = 10;
			$offset = ($page - 1) * $limit;

			// Get filters
			$search = $this->input->get('search', TRUE);
			$created_by = $this->input->get('created_by', TRUE);
			$status = $this->input->get('status', TRUE);
			$priority = $this->input->get('priority', TRUE);

			$tasks = $this->User_task_model->get_tasks_assigned_to_me($current_user_id, $limit, $offset, $search, $created_by, $status, $priority);
			$total_tasks = $this->User_task_model->count_assigned_tasks($current_user_id, $search, $created_by, $status, $priority);
			$total_pages = ceil($total_tasks / $limit);

			// Pagination Styling
			$pagination = "<nav aria-label='Page navigation'><ul class='pagination justify-content-center'>";

			// Previous Button
			if ($page > 1) {
				$pagination .= "<li class='page-item'>
									<a class='page-link rounded-pill' href='#' data-page='" . ($page - 1) . "'>
										<i class='fas fa-chevron-left'></i>
									</a>
								</li>";
			}

			// Page Numbers
			for ($i = 1; $i <= $total_pages; $i++) {
				$active = ($i == $page) ? 'active' : '';
				$pagination .= "<li class='page-item $active'>
									<a class='page-link rounded-pill' href='#' data-page='$i'>$i</a>
								</li>";
			}

			// Next Button
			if ($page < $total_pages) {
				$pagination .= "<li class='page-item'>
									<a class='page-link rounded-pill' href='#' data-page='" . ($page + 1) . "'>
										<i class='fas fa-chevron-right'></i>
									</a>
								</li>";
			}

			$pagination .= "</ul></nav>";

			// Return JSON response
			echo json_encode([
				"success" => true,
				"tasks" => $tasks,
				"pagination" => $pagination,
				"per_page" => $limit,
				"current_page" => $page,
				"total_pages" => $total_pages
			]);
			return;
		}

		// Load the main page view for non-AJAX requests
		$this->load->view('auth/layout/header');
		$this->load->view('auth/layout/sidebar');
		$this->load->view('auth/my_tasks', $data);
	}
	
	// Fetch Task Details for Editing
    public function getTaskDetails()
    {
        $taskId = $this->input->get('task_id');

        if (empty($taskId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $task = $this->User_task_model->getTaskById($taskId);

        if ($task) {
            echo json_encode(['success' => true, 'task' => $task]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Task not found']);
        }
    }

    // Update Task Status
    public function updateStatus()
    {
        $taskId = $this->input->post('task_id');
        $status = $this->input->post('status');

        if (empty($taskId) || $status === null) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $updated = $this->User_task_model->updateTaskStatus($taskId, $status);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update task status']);
        }
    }

}
