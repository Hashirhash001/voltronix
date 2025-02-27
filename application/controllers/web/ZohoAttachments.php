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

}
