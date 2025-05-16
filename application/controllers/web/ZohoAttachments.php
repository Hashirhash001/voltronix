<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tasks Controller
 * @property session $session
 * @property User_model $User_model
 * @property form_validation $form_validation
 * @property Task_model $Task_model
 * @property ZohoAttachments_model $ZohoAttachments_model
 * @property Access_token_model $Access_token_model
 * @property ZohoNotes_model $ZohoNotes_model
 * @property input $input
 * @property upload $upload
 * @property request $request
 * @property response $response
 */

 class ZohoAttachments extends CI_Controller {

    private $client_id;
    private $client_secret;
	private $refresh_token;
    private $access_token;

    public function __construct() {
        parent::__construct();
        $this->load->model('ZohoAttachments_model');
        $this->load->model('Access_token_model');
        $this->load->model('ZohoNotes_model');
		$this->load->helper('url');

		$this->client_id = '1000.0V519TDSRC8AYUHXMU2SYCAGN9UP3L';
        $this->client_secret = '0f58eea7da1716ec409099063b8f7e42218854e242';
		$this->refresh_token = '1000.4348d34c1a96e813abe7ff21bfc4a04b.0fd6bc7aeb9d83178d3b5f7f893744c8';
    }

	// Get access token
    private function get_access_token() {
        $this->access_token = $this->Access_token_model->get_access_token();
        return $this->access_token;
    }

	// Refresh access token
    private function refresh_access_token() {
        $url = "https://accounts.zoho.com/oauth/v2/token";

        $data = [
            'refresh_token' => $this->refresh_token,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (isset($response_data['access_token'])) {
            $this->Access_token_model->save_access_token($response_data['access_token']);
            $this->access_token = $response_data['access_token'];
            return $this->access_token;
        } else {
            log_message('error', 'Failed to refresh access token: ' . $response);
            return false;
        }
    }

	// Upload attachment
    public function upload_attachment()
	{
		$deal_id = $this->input->post('deal_id');
		if (empty($deal_id)) {
			echo json_encode(["success" => false, "message" => "Deal ID is required"]);
			return;
		}

		if (empty($_FILES['attachments'])) {
			echo json_encode(["success" => false, "message" => "No files uploaded"]);
			return;
		}

		$upload_dir = 'uploads/deal_attachments/';
		if (!is_dir($upload_dir)) {
			mkdir($upload_dir, 0777, true);
		}

		$access_token = $this->get_access_token();
		if (!$access_token) {
			echo json_encode(["success" => false, "message" => "Failed to retrieve access token"]);
			return;
		}

		$uploaded_files = [];
		foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
			$file_name = time() . '_' . $_FILES['attachments']['name'][$key];
			$file_size = $_FILES['attachments']['size'][$key];
			$file_type = $_FILES['attachments']['type'][$key];
			$file_path = $upload_dir . $file_name;

			if (move_uploaded_file($tmp_name, $file_path)) {
				// Upload to Zoho
				$zoho_response = $this->upload_to_zoho($deal_id, $file_path, $file_name, $access_token);

				if ($zoho_response['success']) {
					$attachment_id = $zoho_response['attachment_id']; // Get Zoho Attachment ID

					// Store in DB
					$file_id = $this->ZohoAttachments_model->store_file($deal_id, $file_name, $file_size, $file_type, $file_path, $attachment_id);

					$uploaded_files[] = [
						'file_id' => $file_id,
						'file_name' => $file_name,
						'attachment_id' => $attachment_id
					];
				} else {
					unlink($file_path);
					echo json_encode(["success" => false, "message" => "Failed to upload file to Zoho"]);
					return;
				}
			} else {
				echo json_encode(["success" => false, "message" => "Failed to save file locally"]);
				return;
			}
		}

		echo json_encode(["success" => true, "files" => $uploaded_files]);
	}

	// Upload to Zoho
	private function upload_to_zoho($deal_id, $file_path, $file_name, $access_token)
	{
		$url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Attachments";

		$ch = curl_init($url);
		$data = [
			'file' => new CURLFile($file_path, mime_content_type($file_path), $file_name)
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Zoho-oauthtoken {$access_token}"
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$response_data = json_decode($response, true);

		if (isset($response_data['data'][0]['details']['id'])) {
			return [
				'success' => true,
				'attachment_id' => $response_data['data'][0]['details']['id']
			];
		} elseif (isset($response_data['code']) && $response_data['code'] == "INVALID_TOKEN") {
			$new_token = $this->refresh_access_token();
			if ($new_token) {
				return $this->upload_to_zoho($deal_id, $file_path, $file_name, $new_token);
			}
		}

		return ['success' => false, 'message' => $response_data];
	}

	// Fetch Attachments
    public function get_attachments($dealId)
	{
		if (!$dealId) {
			echo json_encode(["success" => false, "message" => "Deal ID is required"]);
			return;
		}

		// ✅ Debugging: Log dealId
		log_message("error", "Fetching attachments for deal ID: " . $dealId);

		$attachments = $this->ZohoAttachments_model->getAttachmentsByDeal($dealId);

		// ✅ Debugging: Check what we got from DB
		// log_message("Query Result: " . print_r($attachments, true));
		log_message("error", "Query Result: " . print_r($attachments, true));

		if (empty($attachments)) {
			echo json_encode(["success" => true, "files" => []]);
			return;
		}

		echo json_encode(["success" => true, "files" => $attachments]);
	}

	// Delete Attachment
    public function delete_attachment()
	{
		$attachment_id = $this->input->post('attachment_id');
		$deal_id = $this->input->post('deal_id');
		$file_path = $this->input->post('file_path');

		if (empty($attachment_id) || empty($deal_id) || empty($file_path)) {
			echo json_encode(["success" => false, "message" => "Attachment ID, Deal ID, and file path are required"]);
			return;
		}

		$access_token = $this->get_access_token();
		if (!$access_token) {
			echo json_encode(["success" => false, "message" => "Failed to retrieve access token"]);
			return;
		}

		$delete_response = $this->delete_from_zoho($deal_id, $attachment_id, $access_token);

		if ($delete_response['success']) {
			// Delete file from folder
			$full_path = FCPATH . 'uploads/deal_attachments/' . basename($file_path);
			
			if (file_exists($full_path)) {
				unlink($full_path);
			}

			// Delete record from database
			$this->ZohoAttachments_model->delete_attachment($attachment_id);

			echo json_encode(["success" => true, "message" => "Attachment deleted successfully"]);
		} else {
			echo json_encode(["success" => false, "message" => $delete_response['message']]);
		}
	}

	// Delete from Zoho
	private function delete_from_zoho($deal_id, $attachment_id, $access_token)
	{
		$url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Attachments/{$attachment_id}";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Zoho-oauthtoken {$access_token}"
		]);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$response_data = json_decode($response, true);

		if ($httpCode == 200) {
			return ['success' => true];
		} elseif (isset($response_data['code']) && $response_data['code'] == "INVALID_TOKEN") {
			$new_token = $this->refresh_access_token();
			if ($new_token) {
				return $this->delete_from_zoho($deal_id, $attachment_id, $new_token);
			}
		}

		return ['success' => false, 'message' => $response_data];
	}
	
	// Add note to Zoho CRM and store locally
    public function add_note()
    {
        $deal_id = $this->input->post('deal_id');
        $note_title = $this->input->post('note_title');
        $note_content = $this->input->post('note_content');

        if (empty($deal_id) || empty($note_title) || empty($note_content)) {
            echo json_encode(["success" => false, "message" => "All fields are required"]);
            return;
        }

        $access_token = $this->get_access_token();
        if (!$access_token) {
            echo json_encode(["success" => false, "message" => "Failed to retrieve access token"]);
            return;
        }

        $zoho_response = $this->add_note_to_zoho($deal_id, $note_title, $note_content, $access_token);

        if ($zoho_response['success']) {
            $zoho_note_id = $zoho_response['note_id'];

            // Store in local database
            $local_note_id = $this->ZohoNotes_model->store_note($deal_id, $note_title, $note_content, $zoho_note_id);

            echo json_encode([
                "success" => true,
                "note_id" => $zoho_note_id,
                "local_note_id" => $local_note_id
            ]);
        } else {
            echo json_encode(["success" => false, "message" => $zoho_response['message']]);
        }
    }

	// Add note to Zoho CRM
	private function add_note_to_zoho($deal_id, $note_title, $note_content, $access_token)
	{
		$url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Notes";

		$data = [
			'data' => [
				[
					'Note_Title' => $note_title,
					'Note_Content' => $note_content
				]
			]
		];

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Zoho-oauthtoken {$access_token}",
			"Content-Type: application/json"
		]);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		curl_close($ch);

		$response_data = json_decode($response, true);

		if (isset($response_data['data'][0]['details']['id'])) {
			return [
				'success' => true,
				'note_id' => $response_data['data'][0]['details']['id']
			];
		} elseif (isset($response_data['code']) && $response_data['code'] == "INVALID_TOKEN") {
			$new_token = $this->refresh_access_token();
			if ($new_token) {
				return $this->add_note_to_zoho($deal_id, $note_title, $note_content, $new_token);
			}
		}

		return ['success' => false, 'message' => $response_data['message'] ?? 'Failed to add note'];
	}

	// Get notes for a deal
	public function get_notes()
	{
		$deal_id = $this->input->post('deal_id');
		if (empty($deal_id)) {
			echo json_encode(["success" => false, "message" => "Deal ID is required"]);
			return;
		}

		$notes = $this->ZohoNotes_model->get_notes_by_deal($deal_id);
		echo json_encode(["success" => true, "notes" => $notes]);
	}

	// Update note
    public function update_note()
    {
        $local_note_id = $this->input->post('local_note_id');
        $deal_id = $this->input->post('deal_id');
        $note_title = $this->input->post('note_title');
        $note_content = $this->input->post('note_content');

        if (empty($local_note_id) || empty($deal_id) || empty($note_title) || empty($note_content)) {
            echo json_encode(["success" => false, "message" => "All fields are required"]);
            return;
        }

        $access_token = $this->get_access_token();
        if (!$access_token) {
            echo json_encode(["success" => false, "message" => "Failed to retrieve access token"]);
            return;
        }

        // Get the note from the model
        $note = $this->ZohoNotes_model->get_note_by_id($local_note_id);
        if (!$note) {
            echo json_encode(["success" => false, "message" => "Note not found"]);
            return;
        }

        $zoho_note_id = $note['zoho_note_id'];

        // Update in Zoho CRM
        $zoho_response = $this->update_note_in_zoho($deal_id, $zoho_note_id, $note_title, $note_content, $access_token);

        if ($zoho_response['success']) {
            // Update in local database using model
            $this->ZohoNotes_model->update_note($local_note_id, $note_title, $note_content);

            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $zoho_response['message']]);
        }
    }

	// Update note in Zoho
	private function update_note_in_zoho($deal_id, $zoho_note_id, $note_title, $note_content, $access_token)
    {
        $url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Notes/{$zoho_note_id}";

        $data = [
            'data' => [
                [
                    'Note_Title' => $note_title,
                    'Note_Content' => $note_content
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Zoho-oauthtoken {$access_token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $response_data = json_decode($response, true);

        if (isset($response_data['data'][0]['status']) && $response_data['data'][0]['status'] == 'success') {
            return ['success' => true];
        } elseif (isset($response_data['code']) && $response_data['code'] == "INVALID_TOKEN") {
            $new_token = $this->refresh_access_token();
            if ($new_token) {
                return $this->update_note_in_zoho($deal_id, $zoho_note_id, $note_title, $note_content, $new_token);
            }
        }

        return ['success' => false, 'message' => $response_data['message'] ?? 'Failed to update note'];
    }

	// Delete note
	public function delete_note()
	{
		$local_note_id = $this->input->post('local_note_id');

		if (empty($local_note_id)) {
			echo json_encode(["success" => false, "message" => "Note ID is required"]);
			return;
		}

		$access_token = $this->get_access_token();
		if (!$access_token) {
			echo json_encode(["success" => false, "message" => "Failed to retrieve access token"]);
			return;
		}

		$note = $this->ZohoNotes_model->get_note_by_id($local_note_id);
		if (!$note) {
			echo json_encode(["success" => false, "message" => "Note not found in local database"]);
			return;
		}

		$deal_id = $note['deal_id'];
		$zoho_note_id = $note['zoho_note_id'];

		log_message('debug', "Attempting to delete note - Local ID: $local_note_id, Deal ID: $deal_id, Zoho Note ID: $zoho_note_id");

		$zoho_response = $this->delete_note_from_zoho($deal_id, $zoho_note_id, $access_token);

		if ($zoho_response['success']) {
			$this->ZohoNotes_model->delete_note($local_note_id);
			echo json_encode(["success" => true, "message" => "Note deleted successfully"]);
		} elseif (isset($zoho_response['code']) && $zoho_response['code'] == 'INVALID_DATA') {
			$this->ZohoNotes_model->delete_note($local_note_id);
			echo json_encode([
				"success" => true,
				"message" => "Note not found in Zoho CRM, removed from local database",
				"warning" => "Zoho record was already deleted or invalid"
			]);
		} else {
			echo json_encode([
				"success" => false,
				"message" => $zoho_response['message'] ?? "Unknown error occurred while deleting note from Zoho",
				"zoho_note_id" => $zoho_note_id
			]);
		}
	}

	// Delete note from Zoho
	private function delete_note_from_zoho($deal_id, $zoho_note_id, $access_token)
	{
		$url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Notes/{$zoho_note_id}";

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Zoho-oauthtoken {$access_token}"
		]);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$response_data = json_decode($response, true);
		log_message('debug', "Zoho Delete Note Response (HTTP $http_code): " . print_r($response_data, true));

		// Check success within data[0] structure
		if ($http_code == 200 && isset($response_data['data'][0]['status']) && $response_data['data'][0]['status'] == 'success') {
			return ['success' => true];
		} elseif (isset($response_data['code']) && $response_data['code'] == "INVALID_TOKEN") {
			$new_token = $this->refresh_access_token();
			if ($new_token) {
				log_message('debug', "Token refreshed, retrying with new token: $new_token");
				return $this->delete_note_from_zoho($deal_id, $zoho_note_id, $new_token);
			}
			return ['success' => false, 'message' => 'Token refresh failed'];
		} else {
			$error_message = "Failed to delete note from Zoho (HTTP $http_code)";
			$error_code = null;

			if (isset($response_data['data']) && !empty($response_data['data']) && isset($response_data['data'][0]['message'])) {
				$error_message = $response_data['data'][0]['message'];
				if (isset($response_data['data'][0]['code'])) {
					$error_message .= " - Code: " . $response_data['data'][0]['code'];
					$error_code = $response_data['data'][0]['code'];
				}
			} elseif (isset($response_data['message'])) {
				$error_message = $response_data['message'];
				if (isset($response_data['code'])) {
					$error_message .= " - Code: " . $response_data['code'];
					$error_code = $response_data['code'];
				}
			}

			return [
				'success' => false,
				'message' => $error_message,
				'code' => $error_code
			];
		}
	}
	
	private function download_from_zoho($deal_id, $attachment_id, $access_token)
    {
        $url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}/Attachments/{$attachment_id}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Zoho-oauthtoken {$access_token}"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $file_content = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200) {
            return ['success' => true, 'file_content' => $file_content];
        } elseif ($http_code == 401) {
            $new_token = $this->refresh_access_token();
            if ($new_token) {
                return $this->download_from_zoho($deal_id, $attachment_id, $new_token);
            }
        }
        log_message('error', "Failed to download attachment {$attachment_id}: HTTP {$http_code}");
        return ['success' => false, 'message' => 'Failed to download attachment'];
    }

    private function notify_zoho_deal_update($deal_id, $access_token)
    {
        $url = "https://www.zohoapis.com/crm/v2/Deals/{$deal_id}";
        $data = [
            'data' => [
                [
                    'Last_Attachment_Update' => date('c') // ISO 8601, e.g., 2025-05-13T16:50:00+05:30
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Zoho-oauthtoken {$access_token}",
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code == 200 || $http_code == 201) {
            log_message('info', "Successfully updated Deal {$deal_id} with Last_Attachment_Update");
        } elseif ($http_code == 401) {
            $new_token = $this->refresh_access_token();
            if ($new_token) {
                $this->notify_zoho_deal_update($deal_id, $new_token);
            } else {
                log_message('error', "Failed to refresh access token for Deal update: {$deal_id}");
            }
        } else {
            log_message('error', "Failed to update Deal {$deal_id}: HTTP {$http_code} - " . $response);
        }
    }

    public function sync_attachments()
    {
        $input_data = json_decode(file_get_contents('php://input'), true);
        $deal_id = $input_data['deal_id'] ?? null;
        $attachments = $input_data['attachments'] ?? [];

        if (empty($deal_id)) {
            echo json_encode(["success" => false, "message" => "Deal ID is required"]);
            return;
        }

        if (empty($attachments)) {
            echo json_encode(["success" => true, "message" => "No attachments to sync"]);
            return;
        }

        $upload_dir = 'Uploads/deal_attachments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $access_token = $this->get_access_token();
        if (!$access_token) {
            echo json_encode(["success" => false, "message" => "Failed to retrieve access token"]);
            return;
        }

        $uploaded_files = [];
        foreach ($attachments as $attachment) {
            $attachment_id = $attachment['attachment_id'];
            $file_name = time() . '_' . $attachment['file_name'];
            $file_size = $attachment['file_size'];
            $file_path = $upload_dir . $file_name;

            $download_response = $this->download_from_zoho($deal_id, $attachment_id, $access_token);
            if ($download_response['success']) {
                $file_content = $download_response['file_content'];
                if (file_put_contents($file_path, $file_content)) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $file_type = finfo_file($finfo, $file_path);
                    finfo_close($finfo);

                    $file_id = $this->ZohoAttachments_model->store_file($deal_id, $file_name, $file_size, $file_type, $file_path, $attachment_id);
                    $uploaded_files[] = [
                        'file_id' => $file_id,
                        'file_name' => $file_name,
                        'attachment_id' => $attachment_id
                    ];

                    // Update Deal to trigger Modified_Time
                    $this->notify_zoho_deal_update($deal_id, $access_token);
                } else {
                    log_message('error', "Failed to save file locally: $file_name");
                    echo json_encode(["success" => false, "message" => "Failed to save file locally: $file_name"]);
                    return;
                }
            } else {
                log_message('error', "Failed to download attachment: $attachment_id - " . $download_response['message']);
                continue;
            }
        }

        echo json_encode(["success" => true, "files" => $uploaded_files]);
    }

}
