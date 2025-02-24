		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">

			<div class="container d-flex justify-content-center align-items-center vh-100">
				<div class="card shadow-lg p-4 w-50">
					<h3 class="mb-4 text-center fw-bold">Assign a Task</h3>

					<?php if ($current_user_id == '5653678000013160085'): ?>
						<form action="<?php echo site_url('web/assignTask/assign'); ?>" method="post" id="taskForm">
							<div class="mb-3">
								<label for="task_title" class="form-label fw-semibold">Task Title</label>
								<input type="text" class="form-control" id="task_title" name="task_title" placeholder="Enter task title" required>
							</div>

							<div class="mb-3">
								<label for="assigned_to" class="form-label fw-semibold">Assigned To</label>
								<select name="assigned_to" class="form-select" required>
									<option value="">Select Member</option>
									<?php foreach ($members as $member) : ?>
										<option value="<?php echo $member->user_id; ?>">
											<?php echo htmlspecialchars($member->username); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="mb-3">
								<label for="due_date" class="form-label fw-semibold">Due Date</label>
								<input type="date" class="form-control" id="due_date" name="due_date" required>
							</div>

							<div class="mb-3">
								<label for="description" class="form-label fw-semibold">Description</label>
								<textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter task description"  required></textarea>
							</div>

							<button type="submit" class="btn btn-primary w-100" id="submitBtn" style="background: #d10908; border: none;">
								<span id="btnText">Assign Task</span>
								<div id="loader" class="spinner-border spinner-border-sm d-none" role="status"></div>
							</button>
						</form>
					<?php else: ?>
						<div class="alert alert-danger text-center">You do not have permission to assign tasks.</div>
					<?php endif; ?>
				</div>
			</div>

		</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

	<script>
		document.addEventListener("DOMContentLoaded", function () {
			const dueDateInput = document.getElementById("due_date");
			const today = new Date().toISOString().split("T")[0]; 
			dueDateInput.setAttribute("min", today); 

			const form = document.getElementById("taskForm");
			const submitBtn = document.getElementById("submitBtn");
			const btnText = document.getElementById("btnText");
			const loader = document.getElementById("loader");

			form.addEventListener("submit", function (event) {
				event.preventDefault(); // Prevent immediate submission

				// Disable button and show loader
				submitBtn.disabled = true;
				btnText.textContent = "Assigning...";
				loader.classList.remove("d-none");

				// Submit form after a short delay
				setTimeout(() => {
					form.submit(); // Submit form
				}, 1000);
			}, { once: true }); // Ensure event fires only once
		});


	</script>
	<script>
		$(document).ready(function() {
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
