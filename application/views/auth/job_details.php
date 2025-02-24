

		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">

			<div class="container p-5" style="margin-top: 70px;">
				<h1>Job Details</h1>
				<div class="card shadow-sm mt-4">
					<div class="card-header text-white d-flex align-items-center gap-2" style="font-size: 1.5rem; font-weight: bold; border-radius: 0.25rem; background-color: #FF0100;">
						<i class="bi bi-file-text"></i>
						<?= htmlspecialchars($task['deal_name']); ?>
					</div>
					<div class="card-body">
						<p class="card-text">
							<i class="fas fa-tag"></i>
							<span class="ms-2">Deal Number:</span>
							<?= htmlspecialchars($task['deal_number'] ?? ''); ?>
						</p>
						<p class="card-text">
							<i class="fas fa-user-tie"></i>
							<span class="ms-2">Deal Owner:</span>
							<?= htmlspecialchars('Marieswaran'); ?>
						</p>
						<p class="card-text">
							<i class="fas fa-building"></i>
							<span class="ms-2">Account Name:</span>
							<?= htmlspecialchars($task['account_name'] ?? ''); ?>
						</p>
						<p class="card-text">
							<i class="fas fa-dollar-sign"></i>
							<span class="ms-2">Amount:</span>
							<?= htmlspecialchars('AED ' . $task['service_charge'] ?? ''); ?>
						</p>
						<p class="card-text">
							<i class="bi bi-info-circle"></i>
							<span class="ms-2">Description:</span>
							<?= htmlspecialchars($task['complaint_info']); ?>
						</p>
						<p class="card-text">
							<i class="bi bi-envelope"></i>
							<span class="ms-2">Email:</span>
							<?= htmlspecialchars($task['customer_email']); ?>
						</p>
						<p class="card-text">
							<i class="bi bi-telephone"></i>
							<span class="ms-2">Contact:</span>
							<?= htmlspecialchars($task['customer_contact']); ?>
						</p>
						<div class="card-text d-flex gap-4" style="margin-bottom: 1rem;">
							<div>
								<i class="bi bi-info-circle"></i>
								<span class="ms-2">Status:</span>
							</div>
							<form id="updateStatusForm" enctype="multipart/form-data">
								<div class="d-flex">
									<div class="custom-dropdown" style="position: relative; width: 200px;">
										<button class="dropdown-toggle form-select form-select-sm" id="dropdownStatusButton" type="button" data-bs-toggle="dropdown" aria-expanded="false">
											<span id="selectedStatus" class="d-inline-flex align-items-center gap-2" style="margin-right: 15px; width: 87%;">
												<i class="bi bi-check-circle-fill"></i> <?= $task['status']; ?>
											</span>
										</button>
										<ul class="dropdown-menu" aria-labelledby="dropdownStatusButton" id="statusDropdown">
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

								<!-- Remark and file upload -->
								<div id="additionalFields" style="display: none;" class="mt-3">
									<label for="remark" class="form-label">Add Remark:</label>
									<textarea class="form-control" name="remark" id="remark" rows="3"></textarea>

									<div class="mt-3">
										<label for="image" class="form-label">Upload Image:</label>
										<input type="file" class="form-control" name="photos[]" id="image" accept="image/*">
									</div>
								</div>
							</form>

						</div>
						<?php if (!empty($task['quote_number'])): ?>
							<p class="card-text">
								<i class="bi bi-file-earmark-check"></i>
								<span class="ms-2">Quote Number:</span>
								<?= htmlspecialchars($task['quote_number']); ?>
							</p>
						<?php endif; ?>


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
			</div>

		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
				item.addEventListener('click', function() {
					const selectedValue = this.getAttribute('data-value');
					const selectedColor = this.getAttribute('data-color');

					// Update the displayed status
					document.getElementById('selectedStatus').innerHTML = `
						<span style="display: inline-flex; align-items: center;">
							<i class="bi bi-check-circle-fill" style="margin-right: 5px; color: ${selectedColor};"></i>
							${selectedValue}
						</span>
					`;

					// Show the submit button if status changes
					if (selectedValue !== currentStatus) {
						document.getElementById('submitButton').style.display = 'block';
						document.getElementById('clearButton').style.display = 'block';
					} else {
						document.getElementById('submitButton').style.display = 'none';
						document.getElementById('clearButton').style.display = 'none';
					}

					// Hide additional fields if not 'Site Visit'
					if (selectedValue === 'Site Visit') {
						document.getElementById('additionalFields').style.display = 'block';
					} else {
						document.getElementById('additionalFields').style.display = 'none';
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
