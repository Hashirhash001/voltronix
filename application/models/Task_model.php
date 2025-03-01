<?php
class Task_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create_task($data) {
        return $this->db->insert('tasks', $data);
    }

    public function get_task($id) {
        $query = $this->db->get_where('tasks', array('id' => $id));
        return $query->row_array();
    }

    public function update_task($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('tasks', $data);
    
        if ($this->db->affected_rows() === 0) {
            // Task ID was not found or no change was made
            return ['error' => 'Task not found or no changes made.'];
        }
    
        return true; // Task was found and updated
    }
    
    public function task_exists($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('tasks');
        
        return $query->num_rows() > 0;
    }

    public function get_tasks_by_user($user_id) {
        $query = $this->db->get_where('tasks', ['user_id' => $user_id]);
        return $query->result_array();
    }
    
    public function insert_task($data) {
        return $this->db->insert('tasks', $data);
    }
    
    // Method to retrieve a lead by zoho_crm_id
    public function get_task_by_zoho_crm_id($zoho_crm_id) {
        return $this->db->get_where('tasks', ['zoho_crm_id' => $zoho_crm_id])->row_array();
    }
    
    public function delete_task_by_zoho_crm_id($zoho_crm_id) {
        $this->db->where('zoho_crm_id', $zoho_crm_id);
        return $this->db->delete('tasks');
    }
    
    public function get_assigned_to_id($crm_id) {
        $this->db->select('assigned_to');
        $this->db->from('tasks');
        $this->db->where('zoho_crm_id', $crm_id);
        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->assigned_to : null;
    }
    
    public function get_zoho_crm_id($task_id) {
        $this->db->select('zoho_crm_id');
        $this->db->from('tasks');
        $this->db->where('id', $task_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row();
            return $result->zoho_crm_id;
        } else {
            return null;
        }
    }
    
    public function get_big_projects($user_id) {
		$this->db->select('tasks.*');
		$this->db->from('tasks');
		$this->db->where('status', 'Big Project');
		$this->db->where('assigned_to', $user_id);
		$query = $this->db->get();
		$result = $query->result_array();
		return $result;
	}
	
	public function get_id_by_deal_id($deal_id) {
		$this->db->select('id');
		$this->db->from('tasks');
		$this->db->where('zoho_crm_id', $deal_id);
		$query = $this->db->get();
	
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->id;
		} else {
			return null; // Return null if no record is found
		}
	}
	
	public function get_task_by_deal_id($deal_id) {
        $this->db->select('*');
        $this->db->from('tasks');
        $this->db->where('zoho_crm_id', $deal_id);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->row(); // Return the full row instead of just the `id`
        } else {
            return null; // Return null if no record is found
        }
    }
	
	public function get_id_by_quote_id($quote_no) {
		$this->db->select('id');
		$this->db->from('tasks');
		$this->db->where('quote_id', $quote_no);
		$query = $this->db->get();
	
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->id;
		} else {
			return null; // Return null if no record is found
		}
	}
	
	public function save_proposal_data($proposal_data, $id) {
        // Check if the proposal data already exists for the given task_id
        $this->db->where('id', $id);
        $query = $this->db->get('tasks');

        if ($query->num_rows() > 0) {
            // Update existing proposal data
            $this->db->where('id', $id);
            return $this->db->update('tasks', $proposal_data);
        } else {
            // Insert new proposal data
            return $this->db->insert('tasks', $proposal_data);
        }
    }
    
    public function get_quote_by_number($quote_number, $user_id) {
        $this->db->select('*');
        $this->db->from('tasks'); // Replace 'quotes' with your actual table name
        $this->db->where('quote_number', $quote_number);
		$this->db->where('assigned_to', $user_id);
        $query = $this->db->get();

        return $query->row_array(); // Return the first matching row as an associative array
    }

}
