<?php
	class User_task_model extends CI_Model {

		public function __construct() {
			parent::__construct();
			$this->load->database();
		}

		public function get_team_members($id) {
			$this->db->select('id, ,user_id, username'); // Adjust fields as needed
			$this->db->from('users'); // Replace 'users' with your actual table name
			if ($id) {
				$this->db->where('id !=', $id);
			}
			return $this->db->get()->result();
		}

		public function assign_task($data) {
			return $this->db->insert('user_tasks', $data);
		}

		public function get_assigned_tasks($user_id, $limit = null, $offset = null, $search = "", $assigned_to = "", $status = "") {
			$this->db->select('ut.*, u.username AS assigned_to_name');
			$this->db->from('user_tasks ut');
			$this->db->join('users u', 'ut.assigned_to = u.user_id', 'left'); // Join with users table
			$this->db->where('ut.created_by', $user_id);
			$this->db->order_by('ut.created_at', 'DESC');

			// Apply filters
			if (!empty($search)) {
				$this->db->like('ut.title', $search);
			}
		
			if (!empty($assigned_to)) {
				$this->db->where('ut.assigned_to', $assigned_to);
			}
		
			if ($status !== "") {
				$this->db->where('ut.status', $status);
			}
		
			if ($limit !== null && $offset !== null) {
				$this->db->limit($limit, $offset);
			}
		
			return $this->db->get()->result();
		}

		public function get_all_members($auth_user_id) {
			$this->db->select('user_id, username'); // Ensure correct field names
			$this->db->from('users');
			$this->db->where('user_id !=', $auth_user_id); // Exclude the authenticated user
			$this->db->order_by('username', 'ASC'); // Order by name
		
			return $this->db->get()->result();
		}				

		public function count_tasks($user_id, $search = "", $assigned_to = "", $status = "") {
			$this->db->from('user_tasks');
			$this->db->where('created_by', $user_id);

			// Apply filters
			if (!empty($search)) {
				$this->db->like('title', $search);
			}
		
			if (!empty($assigned_to)) {
				$this->db->where('assigned_to', $assigned_to);
			}
		
			if ($status !== "") {
				$this->db->where('user_tasks.status', $status);
			}

			return $this->db->count_all_results();
		}		

		public function get_task_by_id($id)
		{
			return $this->db->where('id', $id)->get('user_tasks')->row();
		}

		public function update_task($id, $data)
		{
			return $this->db->where('id', $id)->update('user_tasks', $data);
		}
		
		public function delete_task($id)
		{
			return $this->db->where('id', $id)->delete('user_tasks');
		}

		public function get_tasks_assigned_to_me($user_id, $limit = null, $offset = null, $search = "", $created_by = "", $status = "")
		{
			$this->db->select('ut.*, u.username AS assigned_by_name');
			$this->db->from('user_tasks ut');
			$this->db->join('users u', 'ut.created_by = u.user_id', 'left'); // Join with users table
			$this->db->where('ut.assigned_to', $user_id);
			$this->db->order_by('ut.created_at', 'DESC');

			// Apply filters
			if (!empty($search)) {
				$this->db->like('ut.title', $search);
			}
		
			if (!empty($created_by)) {
				$this->db->where('ut.created_by', $created_by);
			}
		
			if ($status !== "") {
				$this->db->where('ut.status', $status);
			}
		
			if ($limit !== null && $offset !== null) {
				$this->db->limit($limit, $offset);
			}
		
			return $this->db->get()->result();

			if ($limit !== null && $offset !== null) {
				$this->db->limit($limit, $offset);
			}

			return $this->db->get()->result();
		}

		public function count_assigned_tasks($user_id, $search = "", $created_by = "", $status = "")
		{
			$this->db->where('assigned_to', $user_id);

			// Apply filters
			if (!empty($search)) {
				$this->db->like('title', $search);
			}
		
			if (!empty($created_by)) {
				$this->db->where('created_by', $created_by);
			}
		
			if ($status !== "") {
				$this->db->where('user_tasks.status', $status);
			}

			return $this->db->count_all_results('user_tasks');
		}

		public function getTaskById($taskId)
		{
			$this->db->select('
				user_tasks.*, 
				assigned_user.username as assigned_to_name, 
				creator_user.username as created_by_name
			');
			$this->db->from('user_tasks');
			$this->db->join('users as assigned_user', 'assigned_user.user_id = user_tasks.assigned_to', 'left');
			$this->db->join('users as creator_user', 'creator_user.user_id = user_tasks.created_by', 'left');
			$this->db->where('user_tasks.id', $taskId);

			return $this->db->get()->row_array();
		}

		public function updateTaskStatus($taskId, $status)
		{
			return $this->db->where('id', $taskId)->update('user_tasks', ['status' => $status]);
		}
		
		
	}
?>
