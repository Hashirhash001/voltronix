<?php
class Task_photo_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function add_photo($data) {
        return $this->db->insert('task_photos', $data);
    }

    public function get_photos_by_task($task_id) {
        $query = $this->db->get_where('task_photos', array('task_id' => $task_id));
        return $query->result_array();
    }
    
    public function get_photos_by_task_id($task_id) {
		$this->db->select('photo');
		$this->db->from('task_photos');
		$this->db->where('task_id', $task_id);
		$query = $this->db->get();
		return $query->result_array();
	}
}
