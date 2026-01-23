<?php
class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_user($username) {
        $query = $this->db->get_where('users', array('username' => $username));
        return $query->row_array();
    }

    public function create_user($data) {
        return $this->db->insert('users', $data);
    }

	public function get_user_by_username($username) {
		$this->db->select('*');
		$this->db->from('users');
		$this->db->where('username', $username);
		$query = $this->db->get();
		return $query->row_array(); // Returns user data as an array
	}	
    
    public function get_user_by_username_or_id($username, $user_id) {
		$this->db->where('username', $username);
		$this->db->or_where('user_id', $user_id);
		$query = $this->db->get('users');

		return $query->row_array();
	}

	public function update_user($id, $data) {
		$this->db->where('id', $id);
		$this->db->update('users', $data);

		return true;
	}
	
	public function delete_user_by_id($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->delete('users');
    
        return $this->db->affected_rows() > 0; // Returns TRUE if any rows were affected, FALSE otherwise
    }

    public function get_user_id_by_id($id) {
        $this->db->select('user_id');
        $this->db->from('users');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row_array();
            return $result['user_id'];
        } else {
            return false; // No user found with this id
        }
    }
    
    public function get_tasks_by_assigned_user($user_id) {
        $this->db->select('*');
        $this->db->from('tasks');
        $this->db->where('assigned_to', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();

        return $query->result_array(); // Return the result as an array
    }
    
    public function generate_api_key($user_id) {
		$api_key = bin2hex(random_bytes(16)); // Generate a random API key
		$this->db->where('id', $user_id);
		$this->db->update('users', array('api_key' => $api_key));
		return $api_key;
	}

	public function clear_api_key($user_id) {
		$this->db->where('id', $user_id);
		$this->db->update('users', array('api_key' => null)); // Set the api_key to null
	}
	
	public function get_owner_id_by_user_id($user_id) {
        $this->db->select('owner_id');
        $this->db->from('users');
        $this->db->where('id', $user_id);
        $query = $this->db->get();
        $result = $query->row();
        return $result ? $result->owner_id : null;
    }
    
    public function get_tasks_by_owner($owner_id) {
        $this->db->from('tasks');
        $this->db->where('assigned_to', $owner_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function get_quote_access($user_id)
    {
        $this->db->select('quote_access');
        $this->db->where('id', $user_id);
        $query = $this->db->get('users'); // Adjust table name as necessary
    
        if ($query->num_rows() > 0) {
            return $query->row()->quote_access;
        }
        return null; // Return null if no user is found
    }

	public function get_assigned_deals($user_id, $start_date, $end_date) {
		$this->db->select('status, COUNT(id) as total');
		$this->db->from('tasks');
		$this->db->where('assigned_to', $user_id);
		$this->db->where('status IS NOT NULL'); // Ensure status is not null
	
		// Apply date filters only if they are not NULL or empty
		if ($start_date !== null && $start_date !== '') {
			$this->db->where("created_at >= ", $start_date);
		}
		if ($end_date !== null && $end_date !== '') {
			$this->db->where("created_at <= ", $end_date . " 23:59:59");
		}
	
		$this->db->group_by('status');
		
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_deal_progression_analytics($user_id, $start_date, $end_date, $page = 1, $per_page = 10) {
		$this->db->select('
			t.id,
			t.deal_name,
			t.status,
			t.enq_number,
			t.enq_deal_date,
			t.qual_deal_number,
			t.qual_deal_date,
			t.site_deal_number,
			t.site_deal_date,
			t.quote_deal_number,
			t.quote_deal_date,
			t.job_deal_number,
			t.job_deal_date,
			t.lost_deal_number,
			t.lost_deal_date,
			t.account_name,
			t.deal_number,
			t.complaint_info,
			t.service_charge
		');
		$this->db->from('tasks t');
		$this->db->where('t.assigned_to', $user_id);
		$this->db->where('t.status IS NOT NULL');
	
		// Apply date filters only if they are not NULL or empty
		if ($start_date !== null && $start_date !== '') {
			$this->db->where("t.created_at >= ", $start_date);
		}
		if ($end_date !== null && $end_date !== '') {
			$this->db->where("t.created_at <= ", $end_date . " 23:59:59");
		}
	
		$this->db->order_by('t.created_at', 'DESC');
		$this->db->limit($per_page, ($page - 1) * $per_page);
		
		$query = $this->db->get();
		return $query->result_array();
	}

    public function get_total_deals($user_id) {
        $this->db->where('assigned_to', $user_id);
        $this->db->from('tasks');
		$this->db->where('status IS NOT NULL');
        return $this->db->count_all_results();
    }

	// Method 1: Get all tasks paginated (for initial load)
	public function get_tasks_by_assigned_user_paginated($user_id, $limit, $offset) {
		$this->db->select('*');
		$this->db->from('tasks');
		$this->db->where('assigned_to', $user_id);
		$this->db->where('status IS NOT NULL');
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();

		return $query->result_array();
	}

	// Method 2: Get tasks by specific status/category paginated (for infinite scroll)
	public function get_tasks_by_status_paginated($user_id, $statuses, $limit, $offset) {
		$this->db->select('*');
		$this->db->from('tasks');
		$this->db->where('assigned_to', $user_id);
		$this->db->where_in('status', $statuses);
		$this->db->order_by('created_at', 'DESC');
		$this->db->limit($limit, $offset);
		$query = $this->db->get();

		return $query->result_array();
	}

	// Method 3: Get total count by status (to know if more records exist)
	public function get_total_tasks_count_by_status($user_id, $statuses) {
		$this->db->from('tasks');
		$this->db->where('assigned_to', $user_id);
		$this->db->where_in('status', $statuses);
		return $this->db->count_all_results();
	}
	
	public function get_users_datatable($start, $length, $search, $order_column, $order_dir, $role_filter = '', $status_filter = '', $department_filter = '', $exclude_user_id = null) {
		$this->db->select('id, user_id, username, email, role, status, department, company, quote_access, created_at');
		$this->db->from('users');
		
		// Exclude logged-in user
		if ($exclude_user_id) {
			$this->db->where('id !=', $exclude_user_id);
		}
		
		// Search filter
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('username', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('department', $search);
			$this->db->or_like('company', $search);
			$this->db->group_end();
		}
		
		// Role filter
		if ($role_filter !== '') {
			$this->db->where('role', $role_filter);
		}
		
		// Status filter
		if (!empty($status_filter)) {
			$this->db->where('status', $status_filter);
		}
		
		// Department filter
		if (!empty($department_filter)) {
			$this->db->where('department', $department_filter);
		}
		
		// Ordering
		$this->db->order_by($order_column, $order_dir);
		
		// Pagination
		$this->db->limit($length, $start);
		
		$query = $this->db->get();
		return $query->result_array();
	}

	public function get_users_count($exclude_user_id = null) {
		if ($exclude_user_id) {
			$this->db->where('id !=', $exclude_user_id);
		}
		return $this->db->count_all_results('users');
	}

	public function get_users_filtered_count($search = '', $role_filter = '', $status_filter = '', $department_filter = '', $exclude_user_id = null) {
		$this->db->from('users');
		
		// Exclude logged-in user
		if ($exclude_user_id) {
			$this->db->where('id !=', $exclude_user_id);
		}
		
		// Search filter
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('username', $search);
			$this->db->or_like('email', $search);
			$this->db->or_like('department', $search);
			$this->db->or_like('company', $search);
			$this->db->group_end();
		}
		
		// Role filter
		if ($role_filter !== '') {
			$this->db->where('role', $role_filter);
		}
		
		// Status filter
		if (!empty($status_filter)) {
			$this->db->where('status', $status_filter);
		}
		
		// Department filter
		if (!empty($department_filter)) {
			$this->db->where('department', $department_filter);
		}
		
		return $this->db->count_all_results();
	}


	public function get_all_users() {
		$this->db->select('id, user_id, username, email, role, status, department, company, quote_access, created_at');
		$this->db->from('users');
		$this->db->order_by('created_at', 'DESC');
		$query = $this->db->get();
		
		return $query->result_array();
	}

	public function get_user_by_id($id) {
		$query = $this->db->get_where('users', ['id' => $id]);
		return $query->row_array();
	}

	public function get_user_tasks($user_id) {
		$this->db->select('
			t.*,
			t.deal_name,
			t.deal_number,
			t.account_name,
			t.status,
			t.service_charge,
			t.customer_contact,
			t.created_at,
			t.enq_number,
			t.enq_deal_date,
			t.qual_deal_number,
			t.qual_deal_date,
			t.site_deal_number,
			t.site_deal_date,
			t.quote_deal_number,
			t.quote_deal_date,
			t.job_deal_number,
			t.job_deal_date,
			t.lost_deal_number,
			t.lost_deal_date
		');
		$this->db->from('tasks t');
		$this->db->where('t.assigned_to', $user_id);
		// REMOVED: $this->db->where('t.status IS NOT NULL');
		$this->db->order_by('t.created_at', 'DESC');
		
		$query = $this->db->get();
		return $query->result_array();
	}


}
