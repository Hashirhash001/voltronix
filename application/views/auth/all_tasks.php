		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">

			<div class="container mt-4" style="margin-top: 100px !important;padding-left: 2.5rem; padding-right: 2.5rem; padding-bottom: 2.5rem;">
				<!-- <h2 class="mb-4 fw-bold">Task List</h2> -->

				<div class="table-container">
					<div class="table-responsive">
						<table id="taskTable" class="table align-middle">
							<thead>
								<tr>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">#</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Task Title</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Assigned To</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Due Date</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Description</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Status</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Actions</th>
								</tr>
							</thead>
							<tbody id="taskTableBody">
								<!-- Data will be inserted here dynamically -->
							</tbody>
						</table>
					</div>

					<!-- Pagination -->
					<div class="pagination-container d-flex justify-content-center mt-4">
						<ul class="pagination pagination-modern" id="pagination">
							<!-- Pagination Links Will Be Injected Here -->
						</ul>
					</div>
					
				</div>
			</div>

		</div>

		<!-- Edit Task Modal -->
		<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="editTaskForm">
							<input type="hidden" id="edit_task_id" name="task_id">
							
							<div class="mb-3">
								<label for="edit_task_title" class="form-label">Task Title</label>
								<input type="text" class="form-control" id="edit_task_title" name="task_title" required>
							</div>

							<div class="mb-3">
								<label for="edit_assigned_to" class="form-label">Assigned To</label>
								<select class="form-select" id="edit_assigned_to" name="assigned_to" required>
									<option value="">Select Member</option>
									<?php foreach ($members as $member) : ?>
										<option value="<?php echo $member->user_id; ?>">
											<?php echo htmlspecialchars($member->username ?? ''); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="mb-3">
								<label for="edit_due_date" class="form-label">Due Date</label>
								<input type="date" class="form-control" id="edit_due_date" name="due_date" required>
							</div>

							<div class="mb-3">
								<label for="edit_description" class="form-label">Description</label>
								<textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
							</div>

							<button type="submit" class="btn btn-primary w-100" id="saveTaskBtn">
								Save Changes
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>


	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		$(document).ready(function() {

			loadTasks(1); // Load first page initially

			function loadTasks(page) {
				$.ajax({
					url: "<?php echo base_url('web/assignTask/all-tasks'); ?>", // Change to your endpoint
					type: "GET",
					data: { page: page },
					dataType: "json",
					success: function (response) {
						if (response.success) {
							var tasks = response.tasks;
							var pagination = response.pagination;
							var totalPages = response.total_pages || 1;
							var taskRows = "";

							var perPage = response.per_page || 10; // Default to 10 if undefined
                			var startIndex = (page - 1) * perPage + 1; // Correct calculation
							
							$.each(tasks, function (index, task) {
								var statusClasses = {
									0: "color: #805dca;",
									1: "color: #2196f3;",
									2: "color: #00ab55;",
									3: "color: #e7515a;"
								};

								var statusLabel = {
									0: "Pending",
									1: "In Progress",
									2: "Completed",
									3: "Overdue"
								};

								var statusKey = parseInt(task.status, 10);
								var statusClass = statusClasses[statusKey] || '';

								var formattedDate = new Date(task.due_date * 1000).toLocaleDateString("en-GB", {
									day: "2-digit",
									month: "short",
									year: "numeric"
								});

								taskRows += `
									<tr>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">${startIndex + index}</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">${task.title}</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">
											<span class="scribble-text">${task.assigned_to_name}</span>
										</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">${formattedDate}</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">${task.description}</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">
											<span class="badge" style="${statusClass}">
												${statusLabel[statusKey] || 'Pending'}
											</span>
										</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">
											<button class="action-btn edit-btn" data-id="${task.id}" title="Edit">
												<i class="fas fa-edit"></i>
											</button>
											<button class="action-btn delete-btn" data-id="${task.id}" title="Delete">
												<i class="fas fa-trash"></i>
											</button>
										</td>
									</tr>`;
							});

							$("#taskTableBody").html(taskRows);
							
							// Hide pagination if only one page
							if (totalPages > 1) {
								$("#pagination").html(pagination).show();
							} else {
								$("#pagination").hide();
							}
						}
					}
				});
			}

			// Handle pagination click
			$(document).on("click", ".pagination a", function (e) {
				e.preventDefault();
				var page = $(this).attr("data-page");
				loadTasks(page);
			});

			// Edit Task modal
			$(document).on("click", ".edit-btn", function () {
				var taskId = $(this).data("id");

				$.ajax({
					url: "<?php echo base_url('web/task/get-task'); ?>", // Adjust URL as needed
					type: "POST",
					data: { id: taskId },
					dataType: "json",
					success: function (task) {
						if (task) {
							$("#edit_task_id").val(task.id);
							$("#edit_task_title").val(task.title);
							$("#edit_assigned_to").val(task.assigned_to);
							$("#edit_due_date").val(task.due_date);
							$("#edit_description").val(task.description);
							$("#editTaskModal").modal("show");
						} else {
							Swal.fire("Error", "Task not found!", "error");
						}
					},
					error: function () {
						Swal.fire("Error", "Failed to fetch task data!", "error");
					}
				});
			});

			// Update Task
			$("#editTaskForm").submit(function (e) {
				e.preventDefault();

				// Show loader and disable button
				$("#saveTaskBtn").prop("disabled", true).html(`
					<div class="spinner-border spinner-border-sm text-light" role="status"></div> Saving...
				`);

				$.ajax({
					url: "<?php echo base_url('web/task/update'); ?>", // Adjust URL as needed
					type: "POST",
					data: $("#editTaskForm").serialize(),
					dataType: "json",
					success: function (response) {
						if (response.success) {
							Swal.fire({
								icon: "success",
								title: response.message,
								showConfirmButton: false, // Remove OK button
								timer: 1500, // Auto-close after 1.5 seconds
								allowOutsideClick: false
							});

							// Update the table row dynamically
							var taskId = $("#edit_task_id").val();
							var row = $("button[data-id='" + taskId + "']").closest("tr");

							row.find("td:eq(1)").text($("#edit_task_title").val());
							row.find("td:eq(2)").html(`<span class="scribble-text">${$("#edit_assigned_to option:selected").text()}</span>`);
														
							// Convert date to timestamp and format it
							var rawDate = $("#edit_due_date").val();
							var timestamp = new Date(rawDate).getTime() / 1000; // Convert to timestamp
							var formattedDate = new Date(timestamp * 1000).toLocaleDateString("en-GB", {
								day: "2-digit",
								month: "short",
								year: "numeric"
							});

							row.find("td:eq(3)").text(formattedDate); // Insert formatted date
							row.find("td:eq(4)").text($("#edit_description").val());

							$("#editTaskModal").modal("hide");
						} else {
							Swal.fire("Error", response.message, "error");
						}
					},
					error: function () {
						Swal.fire("Error", "Something went wrong!", "error");
					},
					complete: function () {
						// Reset button state
						$("#saveTaskBtn").prop("disabled", false).html("Save Changes");
					}
				});
			});

			// Delete Task
			$(document).on("click", ".delete-btn", function () {
				var taskId = $(this).data("id");
				var row = $(this).closest("tr"); // Get the row to remove

				Swal.fire({
					title: "Are you sure?",
					text: "You won't be able to revert this!",
					icon: "warning",
					showCancelButton: true,
					confirmButtonColor: "#d33",
					cancelButtonColor: "#3085d6",
					confirmButtonText: "Yes, delete it!"
				}).then((result) => {
					if (result.isConfirmed) {
						$.ajax({
							url: "<?php echo base_url('web/task/delete'); ?>", // Adjust this URL
							type: "POST",
							data: { id: taskId },
							dataType: "json",
							success: function (response) {
								if (response.success) {
									Swal.fire("Deleted!", response.message, "success");
									row.fadeOut(500, function () {
										$(this).remove();
									});
								} else {
									Swal.fire("Error!", response.message, "error");
								}
							},
							error: function () {
								Swal.fire("Error!", "Something went wrong.", "error");
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

		document.addEventListener("DOMContentLoaded", function () {
			const dueDateInput = document.getElementById("edit_due_date");
			const today = new Date().toISOString().split("T")[0]; 
			dueDateInput.setAttribute("min", today); 
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


