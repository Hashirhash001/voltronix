<!-- Main Content -->
<div class="content flex-grow-1" id="mainContent">

	<div class="container p-5" style="margin-top: 50px;">
		<!-- <h1>Job Details</h1> -->
		<div class="card shadow-sm mt-4">
			<div class="card-header text-white d-flex align-items-center gap-2" style="font-size: 1.5rem; font-weight: bold; border-radius: 0.25rem; background-color: #e71919fc; padding: 12px !important; background-color: rgba(0, 0, 0, .03); color: #000 !important;">
				<i class="bi bi-file-text"></i>
				Job Details
			</div>
			<div class="card-body custom-card">
				<div class="row">
					<div class="col-md-6">
						<p class="card-text">
							<i class="fas fa-tag"></i>
							<span class="ms-2">Deal Name :</span>
							<?= htmlspecialchars($task['deal_name'] ?? ''); ?>
						</p>

						<p class="card-text">
							<i class="fas fa-user-tie"></i>
							<span class="ms-2">Deal Owner :</span>
							<?= htmlspecialchars($username ?? ''); ?>
						</p>

						<p class="card-text">
							<i class="fas fa-dollar-sign"></i>
							<span class="ms-2">Amount :</span>
							<?= htmlspecialchars('AED ' . number_format($task['service_charge'] ?? 0, 2)); ?>
						</p>
						<p class="card-text">
							<i class="bi bi-info-circle"></i>
							<span class="ms-2">Description :</span>
							<?= htmlspecialchars($task['complaint_info']); ?>
						</p>
						<?php if (!empty($task['quote_number'])): ?>
							<p class="card-text">
								<i class="bi bi-file-earmark-check"></i>
								<span class="ms-2">Quote Number :</span>
								<?= htmlspecialchars($task['quote_deal_number'] ?? $task['quote_number']); ?>
							</p>
						<?php endif; ?>
					</div>
					<div class="col-md-6">
						<p class="card-text">
							<i class="fas fa-tag"></i>
							<span class="ms-2">Deal Number :</span>
							<?= htmlspecialchars($task['qual_deal_number'] ?? $task['deal_number']); ?>
						</p>
						<p class="card-text">
							<i class="fas fa-building"></i>
							<span class="ms-2">Account Name :</span>
							<?= htmlspecialchars($task['account_name'] ?? ''); ?>
						</p>
						<p class="card-text">
							<i class="bi bi-envelope"></i>
							<span class="ms-2">Email :</span>
							<?= htmlspecialchars($task['customer_email']); ?>
						</p>
						<p class="card-text">
							<i class="bi bi-telephone"></i>
							<span class="ms-2">Contact :</span>
							<?= htmlspecialchars($task['customer_contact']); ?>
						</p>
						<div class="card-text d-flex gap-4" style="margin-bottom: 1rem;">
							<div>
								<i class="bi bi-info-circle"></i>
								<span class="ms-2">Status :</span>
							</div>
							<form id="updateStatusForm" enctype="multipart/form-data">
								<div class="d-flex">
									<div class="custom-dropdown" style="position: relative; width: 200px;">
										<button class="dropdown-toggle form-select form-select-sm" id="dropdownStatusButton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
											<span id="selectedStatus" class="d-inline-flex align-items-center gap-2" style="margin-right: 15px; width: 87%;">
												<i class="bi bi-check-circle-fill"></i> <?= $task['status']; ?>
											</span>
										</button>
										<ul class="dropdown-menu" aria-labelledby="dropdownStatusButton" id="statusDropdown" style="min-width: 12.5rem;">
											<?php if ($task['status'] === 'Pending'): ?>
												<li>
													<a class="dropdown-item" data-value="Site Visit" data-color="#98d681">
														<div style="display: inline-flex; align-items: center;">
															<div style="width: 15px; height: 15px; background-color: #98d681; border-radius: 100%; border: 1px solid rgba(0, 0, 0, .2); margin-right: 8px;"></div>
															Site Visit
														</div>
													</a>
												</li>
												<li>
													<a class="dropdown-item" data-value="Omitted" data-color="#eb4d4d">
														<div style="display: inline-flex; align-items: center;">
															<div style="width: 15px; height: 15px; background-color: #eb4d4d; border-radius: 100%; border: 1px solid rgba(0, 0, 0, .2); margin-right: 8px;"></div>
															Omitted
														</div>
													</a>
												</li>
											<?php elseif ($task['status'] === 'Site Visit'): ?>
												<li>
													<a class="dropdown-item" data-value="Close to Won" data-color="#28a745">
														<div style="display: inline-flex; align-items: center;">
															<div style="width: 15px; height: 15px; background-color: #28a745; border-radius: 100%; border: 1px solid rgba(0, 0, 0, .2); margin-right: 8px;"></div>
															Job Confirmed
														</div>
													</a>
												</li>
												<li>
													<a class="dropdown-item" data-value="Omitted" data-color="#eb4d4d">
														<div style="display: inline-flex; align-items: center;">
															<div style="width: 15px; height: 15px; background-color: #eb4d4d; border-radius: 100%; border: 1px solid rgba(0, 0, 0, .2); margin-right: 8px;"></div>
															Omitted
														</div>
													</a>
												</li>
											<?php elseif ($task['status'] === 'Proposal'): ?>
												<li>
													<a class="dropdown-item" data-value="Close to Won" data-color="#28a745">
														<div style="display: inline-flex; align-items: center;">
															<div style="width: 15px; height: 15px; background-color: #28a745; border-radius: 100%; border: 1px solid rgba(0, 0, 0, .2); margin-right: 8px;"></div>
															Job Confirmed
														</div>
													</a>
												</li>
												<li>
													<a class="dropdown-item" data-value="Omitted" data-color="#eb4d4d">
														<div style="display: inline-flex; align-items: center;">
															<div style="width: 15px; height: 15px; background-color: #eb4d4d; border-radius: 100%; border: 1px solid rgba(0, 0, 0, .2); margin-right: 8px;"></div>
															Omitted
														</div>
													</a>
												</li>
											<?php endif; ?>

										</ul>
									</div>

									<!-- Submit button -->
									<button type="submit" id="submitButton" class="btn btn-link p-0 ms-2" style="text-decoration: none; color: #00f; margin-top: 0 !important; display: none;">
										<i class="bi bi-check-circle"></i>
									</button>

									<!-- Clear button -->
									<button type="button" id="clearButton" class="btn btn-link p-0 ms-2" style="text-decoration: none; color: #f00; margin-top: 0 !important;" aria-label="Clear Form">
										<i class="bi bi-x-circle" style="color: #313949;"></i>
									</button>
								</div>

								<!-- Existing Additional Fields -->
								<div id="additionalFields" style="display: none;" class="mt-3">
									<!-- Extra Amount Field (Visible Only for 'Close to Won') -->
									<div id="extraAmountField" style="display: none;" class="mt-3">
										<label for="extraAmount" class="form-label">Amount:</label>
										<input type="number" class="form-control" name="service_charge" id="extraAmount" step="0.01" required>
									</div>

									<label for="remark" class="form-label">Add Remark:</label>
									<textarea class="form-control" name="remark" id="remark" rows="3"></textarea>

									<div class="mt-3">
										<label for="image" class="form-label">Upload Image:</label>
										<input type="file" class="form-control" name="photos[]" id="image" accept=".pdf,.xlsx,.xls,.png,.jpg,.jpeg,.webp">
									</div>
								</div>

							</form>

						</div>
					</div>
				</div>

				<?php if (!empty($task['quote_id'])): ?>
					<?php if ($quote_access === 'enabled'): ?>
						<div class="mt-3">
							<a href="<?= site_url('web/deal/download-quote/' . $task['id']); ?>"
								class="btn btn-success btn-sm"
								title="Download Quote" target="_blank"
								rel="noopener noreferrer">
								<i class="bi bi-download"></i> Download Quote
							</a>
						</div>
					<?php endif; ?>

				<?php endif; ?>
			</div>
		</div>

		<!-- Bootstrap Modal for File Upload -->
		<div class="modal fade" id="fileUploadModal" tabindex="-1" aria-labelledby="fileUploadModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Upload Attachments</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="uploadForm">
							<input type="hidden" value="<?= htmlspecialchars($task['zoho_crm_id'] ?? ''); ?>" id="dealId">
							<input type="file" id="attachmentInput" class="form-control" name="attachments[]" multiple>
							<div class="progress mt-3 d-none" id="uploadProgress">
								<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;" id="progressBar"></div>
							</div>
							<ul class="list-group mt-3" id="fileList"></ul>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="btn btn-danger" id="uploadBtn">Upload</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Card for Attachments -->
		<div class="card mt-3">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h4 class="text-dark" style="font-size: 1.2rem; font-weight: bold; margin: 0;">Attachments</h4>
				<button type="button" class="btn btn-danger btn-sm border-0" data-bs-toggle="modal" data-bs-target="#fileUploadModal">
					<i class="bi bi-paperclip"></i> Attach File
				</button>
			</div>
			<div class="card-body">
				<div id="attachmentDisplay" class="text-center p-3">
					<p>No Attachment</p>
				</div>
			</div>
		</div>

		<!-- Card for Notes -->
		<div class="card mt-3">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h4 class="text-dark" style="font-size: 1.2rem; font-weight: bold; margin: 0;">Notes</h4>
				<button type="button" class="btn btn-primary btn-sm border-0" data-bs-toggle="modal" data-bs-target="#noteAddModal" style="background-color: #dc3545;">
					<i class="bi bi-plus"></i> Add Note
				</button>
			</div>
			<div class="card-body">
				<div id="notesDisplay" class="p-3">
					<p style="text-align: center;">No Notes</p>
				</div>
			</div>
		</div>

		<!-- Bootstrap Modal for Adding Notes -->
		<div class="modal fade" id="noteAddModal" tabindex="-1" aria-labelledby="noteAddModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Add Note</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="noteForm">
							<input type="hidden" value="<?= htmlspecialchars($task['zoho_crm_id'] ?? ''); ?>" id="dealIdNote">
							<div class="mb-3">
								<label for="noteTitle" class="form-label">Note Title</label>
								<input type="text" class="form-control" id="noteTitle" name="note_title" required>
							</div>
							<div class="mb-3">
								<label for="noteContent" class="form-label">Note Content</label>
								<textarea class="form-control" id="noteContent" name="note_content" rows="4" required></textarea>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
						<button type="button" class="btn" id="saveNoteBtn" style="background-color: #dc3545; color: #fff;">Save Note</button>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
	// Upload File Upload to crm
	$(document).ready(function() {
		let dealId = $("#dealId").val();
		if (dealId) {
			fetchAttachments(dealId); // ✅ Fetch existing attachments on page load
		}

		let selectedFiles = [];

		const MAX_FILES = 5; // Maximum number of files
    	const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB in bytes

		$("#attachmentInput").on("change", function() {

			let newFiles = Array.from(this.files);

			// Validation: Check total file count
			if (selectedFiles.length + newFiles.length > MAX_FILES) {
				Swal.fire("Limit Exceeded", `You can upload a maximum of ${MAX_FILES} files.`, "error");
				this.value = "";
				return;
			}

			// Validation: Check individual file sizes
			for (let file of newFiles) {
				if (file.size > MAX_FILE_SIZE) {
					Swal.fire("File Too Large", `File "${file.name}" exceeds the 5MB size limit.`, "error");
					this.value = "";
					return;
				}
			}

			selectedFiles = [...selectedFiles, ...Array.from(this.files)];
			updateFileList();
			this.value = ""; // Reset input field
		});

		// Update file list
		function updateFileList() {
			let fileList = $("#fileList").empty();
			if (selectedFiles.length === 0) return;

			selectedFiles.forEach((file, index) => {
				fileList.append(`
						<li class="list-group-item d-flex justify-content-between align-items-center">
							<div class="d-flex align-items-center flex-wrap">
								<i class="bi bi-file-earmark-text text-primary me-2"></i> 
								<span class="file-name text-break">${file.name}</span>
							</div>
							<button class="btn btn-sm btn-danger" onclick="removeFile(${index})">
								<i class="bi bi-trash"></i>
							</button>
						</li>
					`);
			});
		}

		window.removeFile = function(index) {
			selectedFiles.splice(index, 1);
			updateFileList();
		};

		// Handle file upload
		$("#uploadBtn").click(function() {
			if (selectedFiles.length === 0) {
				Swal.fire("No Files Selected", "Please select at least one file to upload.", "warning");
				return;
			}

			let dealId = $("#dealId").val();
			if (!dealId) {
				Swal.fire("Missing Deal ID", "Deal ID is required to upload files.", "error");
				return;
			}

			let formData = new FormData();
			formData.append("deal_id", dealId);
			selectedFiles.forEach(file => formData.append("attachments[]", file));

			let progressBar = $("#progressBar");
			let uploadProgress = $("#uploadProgress").removeClass("d-none"); // Ensure it's visible
			let uploadBtn = $("#uploadBtn").prop("disabled", true);

			// Reset progress bar
			progressBar.css({
				"background-color": "#dc3545",
				"width": "0%"
			}).text("0%");

			let simulatedProgress = [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99];
			let index = 0;
			let progressInterval;

			function updateProgressBar() {
				if (index < simulatedProgress.length) {
					let percent = simulatedProgress[index];
					progressBar.css("width", percent + "%").text(percent + "%");
					index++;
				} else {
					clearInterval(progressInterval);
				}
			}

			progressInterval = setInterval(updateProgressBar, 100); // Steady 100ms updates

			$.ajax({
				url: "<?= site_url('ZohoAttachments/upload-attachment') ?>",
				type: "POST",
				data: formData,
				contentType: false,
				processData: false,
				dataType: "json",
				xhr: function() {
					let xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener("progress", function(e) {
						if (e.lengthComputable) {
							progressInterval = setInterval(updateProgressBar, 100); // Steady until response
						}
					});
					return xhr;
				},
				success: function(response) {
					if (response.success) {
						clearInterval(progressInterval);

						// Quickly fill progress to 100% after server response
						let fastProgressInterval = setInterval(() => {
							if (index < simulatedProgress.length) {
								let percent = simulatedProgress[index];
								progressBar.css("width", percent + "%").text(percent + "%");
								index++;
							} else {
								clearInterval(fastProgressInterval);
								progressBar.css("width", "100%").text("100%");

								// Ensure 100% is visible before hiding progress and showing success popup
								setTimeout(() => {
									uploadProgress.addClass("d-none"); // Hide progress bar
									progressBar.css("width", "0%").text("0%"); // Reset progress bar
									Swal.fire("Success", "Files uploaded successfully!", "success");
									$("#fileUploadModal").modal("hide");

									// Fetch and update attachments
									fetchAttachments(dealId);
								}, 500); // Keep 100% visible for 1 second before hiding
							}
						}, 10); // Speed up updates after server response

					} else {
						Swal.fire("Upload Failed", response.message, "error");
					}
				},
				error: function(xhr) {
					console.log(xhr.responseText);
					Swal.fire("Error", "An error occurred while uploading files.", "error");
				},
				complete: function() {
					clearInterval(progressInterval);
					uploadBtn.prop("disabled", false);
					selectedFiles = [];
					updateFileList();
				}
			});
		});

		// Remove attachment
		$(document).on("click", ".remove-attachment", function() {
			let button = $(this); // Get the clicked button
			let attachmentId = button.data("id");
			let dealId = $("#dealId").val(); // Get deal ID
			let filePath = button.closest("tr").find("a").attr("href");

			if (!attachmentId || !dealId || !filePath) {
				Swal.fire("Error", "Invalid attachment, deal ID, or file path", "error");
				return;
			}

			Swal.fire({
				title: "Are you sure?",
				text: "This attachment will be permanently deleted!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Yes, delete it!",
				showLoaderOnConfirm: true, // Show loading state
				preConfirm: () => {
					return new Promise((resolve) => {
						// Disable button & show spinner
						button.prop("disabled", true).html('<i class="spinner-border spinner-border-sm"></i>');

						$.ajax({
							url: "<?= base_url('ZohoAttachments/delete-attachment') ?>",
							type: "POST",
							data: {
								attachment_id: attachmentId,
								deal_id: dealId,
								file_path: filePath
							},
							dataType: "json",
							success: function(response) {
								if (response.success) {
									// Remove row instantly from the table for instant UI update
									button.closest("tr").remove();

									Swal.fire("Deleted!", "Attachment has been removed.", "success");

									// Fetch updated list with a slight delay for backend sync
									setTimeout(() => {
										fetchAttachments(dealId);
									}, 500);
								} else {
									Swal.fire("Error", response.message, "error");
								}
							},
							error: function() {
								Swal.fire("Error", "Something went wrong!", "error");
							},
							complete: function() {
								// Restore button state after request
								button.prop("disabled", false).html('<i class="bi bi-trash"></i>');
								resolve(); // Resolving promise to keep modal behavior
							}
						});
					});
				}
			});
		});

		// Fetch attachments and update table
		function fetchAttachments(dealId) {
			$.ajax({
				url: `<?= site_url('ZohoAttachments/get-attachments/') ?>${dealId}`,
				type: "GET",
				dataType: "json",
				success: function(response) {
					console.log("Fetched Attachments:", response); // Debugging

					if (response.success) {
						$("#attachmentDisplay").empty(); // Clear before updating
						if (response.files.length > 0) {
							updateAttachmentCard(response.files);
						} else {
							$("#attachmentDisplay").html('<p class="text-muted">No attachments available.</p>');
						}
					} else {
						$("#attachmentDisplay").html('<p class="text-danger">Failed to fetch attachments.</p>');
					}
				},
				error: function() {
					$("#attachmentDisplay").html('<p class="text-danger">Failed to fetch attachments.</p>');
				}
			});
		}

		// Update attachment card
		function updateAttachmentCard(files) {
			let attachmentDisplay = $("#attachmentDisplay").empty();

			if (files.length === 0) {
				attachmentDisplay.html('<p>No Attachments</p>');
				return;
			}

			let tableHTML = `
					<div class="card">
						<div class="card-body p-0">
							<div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
								<table class="table table-sm mb-0">
									<thead class="table-light sticky-header">
										<tr>
											<th class="px-3">File Name</th>
											<th class="px-3">Size</th>
											<th class="px-3">Date Added</th>
											<th class="px-3" style="text-align: center;">Action</th>
										</tr>
									</thead>
									<tbody style="border-top: unset;">
				`;

			function formatDate(dateString) {
				if (!dateString) return "Unknown Date"; // Handle missing dates

				const date = new Date(dateString);
				
				if (isNaN(date.getTime())) return "Invalid Date"; // Handle invalid dates

				return date.toLocaleString("en-US", {
					year: "numeric",
					month: "short", // "Feb"
					day: "2-digit", // "28"
					hour: "2-digit", // "09"
					minute: "2-digit", // "54"
					hour12: true, // "AM/PM"
				});
			}

			let rows = files.map(file => {
				let fileSize = (file.file_size / 1024).toFixed(2) + " KB";
				let fileDate = formatDate(file.created_at); // Use formatted date

				const baseURL = "<?php echo base_url(); ?>"; // Get base URL from PHP

				function getFileIcon(fileName) {
					const ext = fileName.split('.').pop().toLowerCase();
					
					const icons = {
						pdf: '<i class="bi bi-file-earmark-pdf text-danger"></i>',
						doc: '<i class="bi bi-file-earmark-word text-primary"></i>',
						docx: '<i class="bi bi-file-earmark-word text-primary"></i>',
						xls: '<i class="bi bi-file-earmark-excel text-success"></i>',
						xlsx: '<i class="bi bi-file-earmark-excel text-success"></i>',
						csv: '<i class="bi bi-file-earmark-spreadsheet text-success"></i>',
						ppt: '<i class="bi bi-file-earmark-ppt text-warning"></i>',
						pptx: '<i class="bi bi-file-earmark-ppt text-warning"></i>',
						txt: '<i class="bi bi-file-earmark-text text-secondary"></i>',
						jpg: '<i class="bi bi-file-earmark-image text-info"></i>',
						jpeg: '<i class="bi bi-file-earmark-image text-info"></i>',
						png: '<i class="bi bi-file-earmark-image text-info"></i>',
						gif: '<i class="bi bi-file-earmark-image text-info"></i>',
						zip: '<i class="bi bi-file-earmark-zip text-dark"></i>',
						rar: '<i class="bi bi-file-earmark-zip text-dark"></i>',
						default: '<i class="bi bi-file-earmark text-muted"></i>'
					};

					return icons[ext] || icons.default;
				}

				// Update table rows
				return `
				<tr class="attachment-row">
					<td class="px-3" style="white-space: normal; word-break: break-word; max-width: 200px; padding: 0.9rem 2.35rem !important;">
						<a href="${baseURL}${file.file_path}" target="_blank" class="d-flex align-items-center gap-2 text-break">
							${getFileIcon(file.file_name)} ${file.file_name}
						</a>
					</td>
					<td class="px-3" style="padding: 0.9rem 2.35rem !important;">${fileSize}</td>
					<td class="px-3" style="padding: 0.9rem 2.35rem !important;">${fileDate}</td>
					<td class="px-3" style="padding: 0.9rem 2.35rem !important; text-align: center;">
						<button class="btn btn-sm remove-attachment" data-id="${file.attachment_id}">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"> 
								<circle opacity="0.5" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"></circle> 
								<path d="M14.5 9.50002L9.5 14.5M9.49998 9.5L14.5 14.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path> 
							</svg>
						</button>
					</td>
				</tr>`;

			}).join('');

			tableHTML += rows + `</tbody></table></div></div></div>`;

			attachmentDisplay.append(tableHTML);
		}

	});
</script>

<script>
	$(document).ready(function() {
		// Load existing notes on page load (optional)
		loadExistingNotes();

		// Define reusable functions
		function addNote() {
			let dealId = $('#dealIdNote').val();
			let noteTitle = $('#noteTitle').val();
			let noteContent = $('#noteContent').val();

			if (!dealId || !noteTitle || !noteContent) {
				Swal.fire({
					icon: 'error',
					title: 'Oops...',
					text: 'All fields are required!'
				});
				return;
			}

			$('#noteAddModal').modal('hide');
			
			Swal.fire({
				title: 'Saving Note...',
				text: 'Please wait while the note is being added.',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: '<?= base_url('ZohoAttachments/add-note'); ?>',
				type: 'POST',
				data: {
					deal_id: dealId,
					note_title: noteTitle,
					note_content: noteContent
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						Swal.fire({
							icon: 'success',
							title: 'Note Added',
							text: 'The note has been successfully added to Zoho CRM!',
							timer: 2000,
							showConfirmButton: false
						}).then(() => {
							$('#noteForm')[0].reset();
							updateNotesDisplay(response.local_note_id, noteTitle, noteContent);
						});
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: response.message || 'Failed to add note to Zoho CRM.'
						});
					}
				},
				error: function() {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'An error occurred while saving the note.'
					});
				}
			});
		}

		function updateNote(localNoteId) {
			let dealId = $('#dealIdNote').val();
			let noteTitle = $('#noteTitle').val();
			let noteContent = $('#noteContent').val();

			$('#noteAddModal').modal('hide');

			Swal.fire({
				title: 'Updating Note...',
				text: 'Please wait while the note is being updated.',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: '<?= base_url('ZohoAttachments/update-note'); ?>',
				type: 'POST',
				data: {
					local_note_id: localNoteId,
					deal_id: dealId,
					note_title: noteTitle,
					note_content: noteContent
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						Swal.fire({
							icon: 'success',
							title: 'Note Updated',
							text: 'The note has been successfully updated!',
							timer: 2000,
							showConfirmButton: false
						}).then(() => {
							$('#noteForm')[0].reset();
							let noteItem = $(`.note-item[data-local-note-id="${localNoteId}"]`);
							noteItem.find('h6').text(noteTitle);
							noteItem.find('p').text(noteContent);
							// Reset the button mode to "add"
							$('#saveNoteBtn').data('mode', 'add');
						});
					} else {
						Swal.fire('Error', response.message, 'error');
					}
				},
				error: function() {
					Swal.fire('Error', 'An error occurred while updating the note.', 'error');
				}
			});
		}

		// Set the save button handler with a mode flag
		$('#saveNoteBtn').data('mode', 'add'); // Default mode is "add"
		$('#saveNoteBtn').on('click', function() {
			let mode = $(this).data('mode');
			if (mode === 'update') {
				let localNoteId = $(this).data('local-note-id');
				updateNote(localNoteId);
			} else {
				addNote();
			}
		});

		// Edit note handler
		$('#notesDisplay').on('click', '.edit-note', function() {
			let localNoteId = $(this).data('local-note-id');
			let noteItem = $(this).closest('.note-item');
			let title = noteItem.find('h6').text();
			let content = noteItem.find('p').text();

			// Populate the modal with existing note data
			$('#noteTitle').val(title);
			$('#noteContent').val(content);
			$('#dealIdNote').val($('#dealIdNote').val()); // Keep the deal ID
			$('#noteAddModal').modal('show');

			// Switch save button to update mode
			$('#saveNoteBtn').data('mode', 'update').data('local-note-id', localNoteId);
		});

		// Delete note handler
		$('#notesDisplay').on('click', '.delete-note', function() {
			let localNoteId = $(this).data('local-note-id');
			Swal.fire({
				title: 'Are you sure?',
				text: 'This note will be deleted permanently.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					deleteNote(localNoteId);
				}
			});
		});

		function deleteNote(localNoteId) {
			$('#noteAddModal').modal('hide');

			Swal.fire({
				title: 'Deleting Note...',
				text: 'Please wait while the note is being deleted.',
				allowOutsideClick: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			$.ajax({
				url: '<?= base_url('ZohoAttachments/delete-note'); ?>',
				type: 'POST',
				data: { local_note_id: localNoteId },
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						let message = response.message || 'The note has been successfully deleted!';
						if (response.warning) {
							message += ' (' + response.warning + ')';
							Swal.fire({
								icon: 'warning',
								title: 'Note Deleted Locally',
								text: message,
								timer: 3000,
								showConfirmButton: false
							});
						} else {
							Swal.fire({
								icon: 'success',
								title: 'Note Deleted',
								text: message,
								timer: 2000,
								showConfirmButton: false
							});
						}
						$(`.note-item[data-local-note-id="${localNoteId}"]`).remove();
						if ($('#notesDisplay').children().length === 0) {
							$('#notesDisplay').html('<p style="text-align: center;">No Notes</p>');
						}
					} else {
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: (response.message || 'An unknown error occurred') + 
								(response.zoho_note_id ? ' (Zoho Note ID: ' + response.zoho_note_id + ')' : '')
						});
					}
				},
				error: function() {
					Swal.fire({
						icon: 'error',
						title: 'Error',
						text: 'A network error occurred while deleting the note.'
					});
				}
			});
		}

		function updateNotesDisplay(localNoteId, title, content) {
			let notesDisplay = $('#notesDisplay');
			if (notesDisplay.text().trim() === 'No Notes') {
				notesDisplay.empty();
			}
			notesDisplay.append(`
				<div class="note-item mb-3 p-3 border rounded shadow-sm" data-local-note-id="${localNoteId}">
					<div class="d-flex align-items-center justify-content-between">
						<div class="d-flex align-items-center">
							<i class="bi bi-sticky me-2 text-primary" style="font-size: 1.2rem;"></i>
							<h6 class="font-weight-bold mb-0">${title}</h6>
						</div>
						<div>
							<button class="btn-sm edit-note me-1 edit-btn border-0 bg-transparent" data-local-note-id="${localNoteId}">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5">
									<path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z" stroke="currentColor" stroke-width="1.5"></path>
									<path opacity="0.5" d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015" stroke="currentColor" stroke-width="1.5"></path>
								</svg>
							</button>
							<button class="btn-sm delete-note delete-btn border-0 bg-transparent" data-local-note-id="${localNoteId}">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
									<path opacity="0.5" d="M9.17065 4C9.58249 2.83481 10.6937 2 11.9999 2C13.3062 2 14.4174 2.83481 14.8292 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
									<path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
									<path d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
									<path opacity="0.5" d="M9.5 11L10 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
									<path opacity="0.5" d="M14.5 11L14 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
								</svg>
							</button>
						</div>
					</div>
					<p class="ms-4 mt-2">${content}</p>
				</div>
			`);
		}

		// Optional: Load existing notes
		function loadExistingNotes() {
			let dealId = $('#dealIdNote').val();
			$.ajax({
				url: '<?= site_url('ZohoAttachments/get-notes'); ?>',
				type: 'POST',
				data: { deal_id: dealId },
				dataType: 'json',
				success: function(response) {
					if (response.success && response.notes.length > 0) {
						$('#notesDisplay').empty();
						response.notes.forEach(note => {
							updateNotesDisplay(note.id, note.note_title, note.note_content);
						});
					}
				}
			});
		}
	});
</script>

<script>
	$(document).ready(function() {
		document.getElementById('clearButton').addEventListener('click', function() {
			// Reset the form fields
			const form = document.getElementById('updateStatusForm');
			form.reset();

			// Reset the custom dropdown to its initial value
			const initialStatus = "<?= $task['status']; ?>"; // Get the initial status from PHP
			const selectedStatusElement = document.getElementById('selectedStatus');
			selectedStatusElement.innerHTML = `
					<i class="bi bi-check-circle-fill"></i> ${initialStatus}
				`;

			// Hide additional fields, submit button, and clear button
			document.getElementById('additionalFields').style.display = 'none';
			document.getElementById('submitButton').style.display = 'none';
			document.getElementById('clearButton').style.display = 'none';
		});

		const currentStatus = '<?= $task["status"]; ?>';

		// Disable the dropdown if the current status is 'Omitted' or 'Close to Won'
		if (currentStatus === 'Omitted' || currentStatus === 'Close to Won' || currentStatus === 'Close to Lost') {
			document.getElementById('dropdownStatusButton').disabled = true;
			document.getElementById('dropdownStatusButton').classList.add('disabled'); // Optionally add a visual style
		}

		// Handle dropdown item selection
		document.querySelectorAll('#statusDropdown .dropdown-item').forEach(item => {
			item.addEventListener('click', function () {
				const selectedValue = this.getAttribute('data-value');
				const selectedColor = this.getAttribute('data-color');

				// Update the displayed status
				document.getElementById('selectedStatus').innerHTML = `
					<span style="display: inline-flex; align-items: center;">
						<i class="bi bi-check-circle-fill" style="margin-right: 5px; color: ${selectedColor};"></i>
						${selectedValue}
					</span>
				`;

				// Show submit and clear buttons if status changes
				const isStatusChanged = selectedValue !== currentStatus;
				document.getElementById('submitButton').style.display = isStatusChanged ? 'block' : 'none';
				document.getElementById('clearButton').style.display = isStatusChanged ? 'block' : 'none';

				// Handle visibility for additional fields
				const isSiteVisitOrCloseToWon = ['Site Visit', 'Close to Won'].includes(selectedValue);
				document.getElementById('additionalFields').style.display = isSiteVisitOrCloseToWon ? 'block' : 'none';

				// Show extra amount field only for 'Close to Won'
				const extraAmountField = document.getElementById('extraAmountField');
				extraAmountField.style.display = selectedValue === 'Close to Won' ? 'block' : 'none';

				// Handle required attribute for service_charge dynamically
				const serviceChargeInput = document.getElementById('extraAmount'); // Corrected ID
				if (serviceChargeInput) { // Ensure element exists before modifying attributes
					if (extraAmountField.style.display === 'block') {
						serviceChargeInput.setAttribute('required', 'required');
					} else {
						serviceChargeInput.removeAttribute('required');
					}
				}
			});
		});


		// Function to toggle the submit button and clear button visibility
		const toggleSubmitButton = () => {
			const selectedStatus = $('#status').val();
			if (selectedStatus && selectedStatus !== currentStatus) {
				$('#submitButton').show(); // Show the submit button if statuses are different
				$('#clearButton').show(); // Show the clear button if statuses are different
			} else {
				$('#submitButton').hide(); // Hide the submit button if statuses are the same
				$('#clearButton').hide(); // Hide the clear button if statuses are the same
			}
		};

		// Monitor changes in the dropdown
		$('#status').on('change', function() {
			toggleSubmitButton();

			const selectedStatus = $(this).val();
			// Show or hide additional fields for 'Site Visit'
			if (selectedStatus === 'Site Visit') {
				$('#additionalFields').slideDown();
			} else {
				$('#additionalFields').slideUp();
			}

			// Update the icon and color
			$(this).find('option').each(function() {
				const color = $(this).data('color');
				const isSelected = $(this).is(':selected');
				if (isSelected && color) {
					$(this).html(`<span style="display: inline-flex; align-items: center;">
							<i class="bi bi-check-circle-fill" style="margin-right: 5px;"></i>
							<div style="width: 10px; height: 10px; background-color: ${color}; border-radius: 50%; margin-right: 5px;"></div>
							${$(this).text()}
						</span>`);
				}
			});
		});

		// Initial toggle check on page load
		toggleSubmitButton();

		// Show/hide fields based on status selection
		$('#status').on('change', function() {
			const selectedStatus = $(this).val();

			if (selectedStatus === 'Site Visit') {
				// Show additional fields for Site Visit
				$('#additionalFields').slideDown();
			} else {
				// Hide additional fields
				$('#additionalFields').slideUp();
			}
		});

		$('#updateStatusForm').on('submit', function(e) {
			e.preventDefault(); // Prevent default form submission

			Swal.fire({
				title: 'Are you sure you want to update the status?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, update it!'
			}).then((result) => {
				if (result.isConfirmed) {
					// Hide and disable the clear button
					const clearButton = $('#clearButton');
					clearButton.hide();

					// Show loader on the button
					const submitButton = $('button[type="submit"]');
					submitButton.prop('disabled', true).html('<i class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></i> Updating...');

					const formData = new FormData(this); // Get form data including files
					const selectedStatus = $('#selectedStatus').text().trim(); // Get the selected status text
					formData.append('status', selectedStatus); // Add the selected status to the form data

					$.ajax({
						url: '<?= site_url("/deal/update/" . $task["id"]); ?>',
						type: 'POST',
						headers: {
							'X-Skip-API-Key': 'true' // Add the custom header
						},
						processData: false,
						contentType: false,
						data: formData,
						success: function(response) {
							if (response.success) {
								Swal.fire({
									icon: 'success',
									title: 'Status Updated',
									text: 'The status has been successfully updated.',
									showConfirmButton: false,
									timer: 1500
								}).then(() => {
									location.reload(); // Reload the page to reflect changes
								});
							} else {
								Swal.fire({
									icon: 'error',
									title: 'Update Failed',
									text: response.error || 'An unexpected error occurred.',
									showConfirmButton: true
								});
							}

							// Re-enable the button
							submitButton.prop('disabled', false).html('<i class="bi bi-check-circle"></i>');
							clearButton.show(); // Show the clear button again
						},
						error: function(xhr, status, error) {
							// Re-enable the button on error
							submitButton.prop('disabled', false).html('<i class="bi bi-check-circle"></i>');
							clearButton.show(); // Show the clear button again

							// Show an error message
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: 'An error occurred while updating the status. Please try again.',
								showConfirmButton: true
							});
						}
					});
				}
			});
		});

		// Handle logout button click
		$('#logoutButton').click(function(e) {
			e.preventDefault(); // Prevent the default button action

			// Optionally, show a confirmation popup before logout
			Swal.fire({
				title: 'Are you sure you want to log out?',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Yes, log out!',
				cancelButtonText: 'Cancel'
			}).then((result) => {
				if (result.isConfirmed) {
					// Show loader while logging out
					$('#logoutButton').prop('disabled', true).text('Logging out...');

					// AJAX request to logout the user
					$.ajax({
						url: '<?= base_url('web/Login/logout') ?>', // This should be the route for logging out
						type: 'POST',
						dataType: 'json',
						success: function(response) {
							if (response.success) {
								Swal.fire({
									icon: 'success',
									title: 'Logged Out',
									text: 'You have been successfully logged out.',
									showConfirmButton: false,
									timer: 1500
								}).then(() => {
									window.location.href = '<?= base_url('web/Login/index') ?>';
								});
							} else {
								// Handle any errors returned from the logout
								Swal.fire({
									icon: 'error',
									title: 'Logout Failed',
									text: response.message || 'An unexpected error occurred.',
									showConfirmButton: true
								});
							}
						},
						error: function(xhr) {
							// Hide loader and re-enable button on error
							$('#logoutButton').prop('disabled', false).text('Logout');

							// Show an error message
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: 'An error occurred while logging out. Please try again.',
								showConfirmButton: true
							});
						}
					});
				}
			});
		});
	});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
