<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function validate_api_key($api_key) {
    $CI =& get_instance();
    
    // Check if the database is loaded
    if (!$CI->db) {
        $CI->load->database();
    }

    $CI->db->where('api_key', $api_key);
    $query = $CI->db->get('users');

    if ($query->num_rows() > 0) {
        return $query->row_array(); // Return user data if key is valid
    } else {
        // Optional: Log error or debug information
        log_message('error', 'Invalid API key: ' . $api_key);
        return false; // Return false if the API key is invalid
    }
}
?>
