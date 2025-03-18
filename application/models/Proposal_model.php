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

	// Get proposal items
	public function get_proposal_items($id) {
		$this->db->where('task_id', $id);
		$query = $this->db->get('proposal_items');
		return $query->result_array();
	}
}
