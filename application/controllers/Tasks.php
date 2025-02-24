<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require FCPATH.'vendor/autoload.php';

class Tasks extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Task_model');
        $this->load->model('Task_photo_model');
        $this->load->model('Task_quote_model');
        $this->load->model('User_model');
        $this->load->helper(['url', 'json_input', 'form']);
        $this->load->helper('token_validate');
        $this->load->library(['form_validation', 'upload']);
        $this->load->library('Pdf');
    }

    public function index() {
        echo "Welcome to the Tasks API!";
    }

    public function create() {
        $data = get_json_input();

        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('zoho_crm_id', 'Zoho CRM ID', 'required');
        $this->form_validation->set_rules('customer_name', 'Customer Name', 'required');
        $this->form_validation->set_rules('customer_email', 'Customer Email', 'required|valid_email');
        $this->form_validation->set_rules('customer_contact', 'Customer Contact', 'required');
        $this->form_validation->set_rules('location', 'Location', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        $this->form_validation->set_rules('device_category', 'Device Category', 'required');
        $this->form_validation->set_rules('complaint_info', 'Complaint Info', 'required');
        $this->form_validation->set_rules('user_id', 'User ID', 'required');
        $this->form_validation->set_rules('latitude', 'Latitude', 'required');
        $this->form_validation->set_rules('longitude', 'Longitude', 'required');

        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            $this->output->set_status_header(400);
            echo json_encode(['error' => $errors]);
            return;
        }

        if ($this->Task_model->create_task($data)) {
            $response = ['id' => $this->db->insert_id(), 'message' => 'Task created successfully.'];
            $this->output->set_status_header(201);
            echo json_encode($response);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'Failed to create task.']);
        }
    }

    public function view($id) {
        // Validate API key
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
        if (!$api_key || !$user_data = validate_api_key($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }
        
        $task = $this->Task_model->get_task($id);

        if (empty($task)) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'Task not found.']);
        } else {
            $photos = $this->Task_photo_model->get_photos_by_task($id);
            $task['photos'] = $photos;
            
            // Fetch the quotes related to the task
            // $quotes = $this->Task_quote_model->get_quotes_by_task($id);
            // $task['quotes'] = $quotes;
            
            $this->output->set_status_header(200);
            echo json_encode($task);
        }
    }
    public function deal_pdf($id) {
        // Validate API key
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
        if (!$api_key || !$user_data = validate_api_key($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }
    
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
    
    public function view_deal($id) {
    
        // Get task data
        $task = $this->Task_model->get_task($id);
        if (empty($task)) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'Task not found.']);
            return;
        }
    
        // Load the view and pass the task data to it
        $this->load->view('deal_pdf_view', ['task' => $task]);
    }

    public function update($id) {
        // Validate API key
        $headers = $this->input->request_headers();
        $api_key = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
    
        if (!$api_key || !$user_data = validate_api_key($api_key)) {
            $this->output->set_status_header(401);
            echo json_encode(['error' => 'Unauthorized access']);
            return;
        }
    
        // Get JSON input or POST data
        $data = get_json_input();
    
        if ($data === null) {
            $data = $this->input->post();
        }
    
        // Set validation rules (no required rule since fields are nullable)
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('status', 'Status', 'trim');
        $this->form_validation->set_rules('remark', 'Remark', 'trim');
        $this->form_validation->set_rules('service_charge', 'Service Charge', 'trim|numeric');
    
        // Run validation
        if ($this->form_validation->run() === FALSE) {
            $errors = validation_errors();
            $this->output->set_status_header(400);
            echo json_encode(['error' => $errors]);
            return;
        }
    
        // Update the task
        if ($this->Task_model->update_task($id, $data)) {
            $response = ['message' => 'Task updated successfully.'];
    
            // Handle multiple file uploads
            if (isset($_FILES['photos'])) {
                $files = $_FILES;
                $cpt = count($files['photos']['name']);
                $upload_path = './assets/photos/';
    
                // Create directory if it doesn't exist
                if (!is_dir($upload_path) && !mkdir($upload_path, 0777, TRUE)) {
                    $this->output->set_status_header(500);
                    echo json_encode(['error' => 'Failed to create upload directory.']);
                    return;
                }
    
                $config['upload_path'] = $upload_path;
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = 2048;
    
                $this->load->library('upload', $config);
    
                for ($i = 0; $i < $cpt; $i++) {
                    $_FILES['photo']['name'] = $files['photos']['name'][$i];
                    $_FILES['photo']['type'] = $files['photos']['type'][$i];
                    $_FILES['photo']['tmp_name'] = $files['photos']['tmp_name'][$i];
                    $_FILES['photo']['error'] = $files['photos']['error'][$i];
                    $_FILES['photo']['size'] = $files['photos']['size'][$i];
    
                    $this->upload->initialize($config);
    
                    if (!$this->upload->do_upload('photo')) {
                        $this->output->set_status_header(400);
                        echo json_encode(['error' => $this->upload->display_errors()]);
                        return;
                    } else {
                        $upload_data = $this->upload->data();
                        $photo_data = [
                            'task_id' => $id,
                            'photo' => $upload_data['file_name']
                        ];
    
                        if (!$this->Task_photo_model->add_photo($photo_data)) {
                            $this->output->set_status_header(500);
                            echo json_encode(['error' => 'Failed to save photo details.']);
                            return;
                        }
                    }
                }
    
                // Return uploaded photo names
                $response['photos'] = array_map(function($file) {
                    return $file['name'];
                }, $files['photos']);
            }
    
            $this->output->set_status_header(200);
            echo json_encode($response);
        } else {
            $this->output->set_status_header(500);
            echo json_encode(['error' => 'Failed to update task.']);
        }
    }

    
    public function list_tasks_per_user($id) {
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
    
        // Fetch the user_id based on the id passed in the URL
        $user_id = $this->User_model->get_user_id_by_id($id);
    
        if (!$user_id) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'User not found for this id.']);
            return;
        }
    
        // Check if the user_id fetched matches the one from the token
        if ($header_user_id != $id) {
            $this->output->set_status_header(403); // Forbidden
            echo json_encode(['error' => 'Forbidden: User ID mismatch']);
            return;
        }
    
        // Fetch tasks where assigned_to equals the user_id
        $tasks = $this->User_model->get_tasks_by_assigned_user($user_id);
    
        if (empty($tasks)) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'No tasks found for this user.']);
        } else {
            foreach ($tasks as &$task) {
                $task['photos'] = $this->Task_photo_model->get_photos_by_task($task['id']);
            }
            $this->output->set_status_header(200);
            echo json_encode($tasks);
        }
    }
    
    public function get_big_projects($id) {

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

		$projects = $this->Task_model->get_big_projects($user_id);

		if (empty($projects)) {
			$this->output->set_status_header(404);
			echo json_encode(['error' => 'No projects found.']);
		}

		// Set response headers
		$this->output->set_status_header(200);
		echo json_encode($projects);
	}

}
