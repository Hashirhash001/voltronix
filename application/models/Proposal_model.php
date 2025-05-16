<?php

class Proposal_model extends CI_Model {
    // Save quote data
    public function save_quote($quote_data) {
        return $this->db->insert('quotes', $quote_data);
    }

    // Save proposal items
    public function save_proposal_items($items) {
        return $this->db->insert_batch('proposal_items', $items);
    }

	public function delete_proposal_items_by_quote_number($quote_number) {
		$this->db->where('quote_number', $quote_number);
		$this->db->delete('proposal_items');
		return $this->db->affected_rows() > 0;
	}

	// Get proposal items
	public function get_proposal_items($quote_number) {
		$this->db->where('quote_number', $quote_number);
		$query = $this->db->get('proposal_items');
		return $query->result_array();
	}

	public function get_proposal_items_by_task_id($task_id) {
        $this->db->select('*');
        $this->db->from('proposal_items');
        $this->db->where('task_id', $task_id);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return [];
        }
    }
}
