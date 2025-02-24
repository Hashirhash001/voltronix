<?php
class Payment_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    // public function get_payments_by_user($user_id) {

    //     $this->db->select('payments.id AS payment_id, payments.user_id, payments.task_id, payments.payment_mode, payments.amount, payments.created_at, payments.updated_at, payments.payment_date, tasks.complaint_info as task_name, users.username');
    //     $this->db->from('payments');
    //     $this->db->join('tasks', 'payments.task_id = tasks.id', 'left');
    //     $this->db->join('users', 'payments.user_id = users.id', 'left');
    //     $this->db->where('payments.user_id', $user_id);
    //     $query = $this->db->get();
    //     return $query->result_array();
    // }
    
    // Check if a payment with the given task_id exists
	public function payment_exists($task_id) {
		$this->db->where('task_id', $task_id);
		$query = $this->db->get('payments');
		
		return $query->num_rows() > 0;
	}

	// Update the payment where task_id matches
	public function update_payment_by_task_id($task_id, $data) {
		$this->db->where('task_id', $task_id);
		return $this->db->update('payments', $data);
	}
    
    public function add_payment($data) {
        return $this->db->insert('payments', $data);
    }
    
    public function get_payments_by_user($user_id) {
        $this->db->select('
            payments.*,
            tasks.deal_name,
            tasks.complaint_info,
            tasks.remark,
            tasks.customer_contact,
            tasks.customer_email,
            tasks.deal_name
        ');
        $this->db->from('payments');
        $this->db->join('tasks', 'tasks.id = payments.task_id');
        $this->db->where('tasks.assigned_to', $user_id);
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return $query->result_array(); // Return the payments with associated task details
        } else {
            return null; // No payments found
        }
    }


}
