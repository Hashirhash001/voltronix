<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ZohoNotes_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // Store note details in the local database
    public function store_note($deal_id, $note_title, $note_content, $zoho_note_id) {
        $data = [
            'deal_id' => $deal_id,
            'note_title' => $note_title,
            'note_content' => $note_content,
            'zoho_note_id' => $zoho_note_id
        ];

        $this->db->insert('job_notes', $data);
        return $this->db->insert_id();
    }

    // Get note by local ID
    public function get_note_by_id($local_note_id) {
        $this->db->where('id', $local_note_id);
        $query = $this->db->get('job_notes');
        return $query->row_array();
    }

    // Get notes by deal ID
    public function get_notes_by_deal($deal_id) {
        $this->db->where('deal_id', $deal_id);
        $query = $this->db->get('job_notes');
        return $query->result_array();
    }

    // Update note
    public function update_note($local_note_id, $note_title, $note_content) {
        $data = [
            'note_title' => $note_title,
            'note_content' => $note_content
        ];
        $this->db->where('id', $local_note_id);
        return $this->db->update('job_notes', $data);
    }

    // Delete note
    public function delete_note($local_note_id) {
        $this->db->where('id', $local_note_id);
        return $this->db->delete('job_notes');
    }
}
