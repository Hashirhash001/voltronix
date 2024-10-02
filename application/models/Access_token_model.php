<?php
class Access_token_model extends CI_Model {
	public function __construct() {
        parent::__construct();
        $this->load->database();
    }

	// Save the access token in the database
    public function save_access_token($token) {
        $this->db->where('id', 1);
        return $this->db->update('access_token', ['token' => $token]);
    }

	public function get_access_token() {
		$query = $this->db->get('access_token');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			return $row->token;
		}
	
		return null;
	}
}
