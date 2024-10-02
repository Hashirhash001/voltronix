<?php
	class Task_quote_model extends CI_Model {

		public function __construct() {
			parent::__construct();
			$this->load->database();
		}

		// Method to insert a task quote record
		public function create_task_quote($data) {
			// Insert new record
			if ($this->db->insert('quotes', $data)) {
				return ['success' => true];
			} else {
				return ['error' => 'Failed to insert new record.'];
			}
		}

		public function get_quotes_by_task($task_id) {
			$this->db->select('quote_id');
			$this->db->from('quotes');
			$this->db->where('task_id', $task_id);
			$query = $this->db->get();
			
			return $query->result_array();
		}		
		
	}
?>
