<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ZohoAttachments_model extends CI_Model
{

	public function store_file($deal_id, $file_name, $file_size, $file_type, $file_path, $attachment_id)
	{
		// Check if attachment already exists
		$existing = $this->db->get_where('job_attachments', ['attachment_id' => $attachment_id])->row_array();
		if ($existing) {
			return $existing['id']; // Return existing file ID
		}

		$data = [
			'deal_id' => $deal_id,
			'file_name' => $file_name,
			'file_size' => $file_size,
			'file_type' => $file_type,
			'file_path' => $file_path,
			'attachment_id' => $attachment_id, // Store attachment ID
			'created_at' => date('Y-m-d H:i:s')
		];
		
		$this->db->insert('job_attachments', $data);
		return $this->db->insert_id();
	}

	// Fetch attachments for a deal
	public function getAttachmentsByDeal($dealId)
	{
		return $this->db->where('deal_id', (string) $dealId) // Cast to string
						->order_by('created_at', 'DESC')
						->get('job_attachments')
						->result_array();
	}

	public function delete_attachment($attachment_id)
	{
		$this->db->where('attachment_id', $attachment_id);
		$this->db->delete('job_attachments');
	}


}
