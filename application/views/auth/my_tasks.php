		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">

			<div class="container mt-4" style="margin-top: 100px !important;padding-left: 2.5rem; padding-right: 2.5rem; padding-bottom: 2.5rem;">
				<!-- <h2 class="mb-4 fw-bold">Task List</h2> -->

				<div class="d-flex flex-wrap align-items-center gap-3 mb-3 p-3 bg-white shadow-sm" style="    border-radius: .6rem !important;">
					<!-- Search Input -->
					<div class="position-relative flex-grow-1">
						<i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
						<input type="text" id="searchInput" class="form-control ps-5" placeholder="Search tasks..." style="box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important; border-radius: .25rem;">
					</div>

					<!-- Assigned To Filter -->
					<div class="position-relative flex-grow-1">
						<i class="bi bi-person-circle position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
						<select id="assignedByFilter" class="form-select ps-5" style="box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important;">
							<option value="">Assigned By</option>
							<?php foreach ($members as $member) : ?>
								<option value="<?php echo $member->user_id; ?>">
									<?php echo htmlspecialchars($member->username); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Status Filter -->
					<div class="position-relative flex-grow-1">
						<i class="bi bi-filter-circle position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);"></i>
						<select id="statusFilter" class="form-select ps-5" style="box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important;">
							<option value="">Task Status</option>
							<option value="0">Pending</option>
							<option value="1">In Progress</option>
							<option value="2">Completed</option>
							<option value="3">Overdue</option>
						</select>
					</div>

					<!-- Action Buttons -->
					<div class="d-flex gap-2">
						<button id="searchBtn" class="btn btn-primary d-flex align-items-center gap-2" style="    background: #d10908; border: none !important;">
							<i class="bi bi-search"></i> <span>Search</span>
						</button>
						<button id="clearFiltersBtn" class="btn btn-outline-secondary d-flex align-items-center gap-2">
							<i class="bi bi-x-circle"></i> <span>Clear</span>
						</button>
					</div>
				</div>

				<div class="table-container">
					<div class="table-responsive">
						<table id="taskTable" class="table align-middle">
							<thead>
								<tr>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">#</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Task Title</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Assigned By</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Due Date</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Description</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Status</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Actions</th>
								</tr>
							</thead>
							<tbody id="taskTableBody" style="border-top: unset;">
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
						<h5 class="modal-title" id="editTaskModalLabel">Update Task Status</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="editTaskForm">
							<input type="hidden" id="edit_task_id" name="task_id">

							<div class="mb-3">
								<label for="edit_status" class="form-label">Status</label>
								<select class="form-select" id="edit_status" name="status" required>
									<option value="">Update Status</option>
									<option value="0">Pending</option>
									<option value="1">In Progress</option>
									<option value="2">Completed</option>
									<option value="3">Overdue</option>
								</select>
							</div>

							<button type="submit" class="btn btn-primary w-100" id="saveTaskBtn">
								Save Changes
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- View Task Modal -->
		<div class="modal fade" id="viewTaskModal" tabindex="-1" aria-labelledby="viewTaskModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="viewTaskModalLabel">Task Details</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<p><strong>Title:</strong> <span id="view_task_title"></span></p>
						<p><strong>Assigned By:</strong> <span id="view_assigned_by"></span></p>
						<p><strong>Due Date:</strong> <span id="view_due_date"></span></p>
						<p><strong>Description:</strong> <span id="view_task_description"></span></p>
						<p><strong>Status:</strong> <span id="view_task_status" class="badge"></span></p>
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
				var search = $("#searchInput").val();
				var assignedBy = $("#assignedByFilter").val();
				var status = $("#statusFilter").val();

				$.ajax({
					url: "<?php echo base_url('web/assignTask/my-tasks'); ?>",
					type: "GET",
					data: { 
						page: page,
						search: search,
						created_by: assignedBy,
						status: status
					},
					dataType: "json",
					success: function (response) {
						if (response.success) {
							var tasks = response.tasks;
							var pagination = response.pagination;
							var totalPages = response.total_pages || 1;
							var taskRows = "";

							var perPage = response.per_page || 10;
							var startIndex = (page - 1) * perPage + 1;

							if (tasks.length > 0) {
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

									var formattedDate = new Date(task.due_date).toLocaleDateString("en-GB", {
										day: "2-digit",
										month: "short",
										year: "numeric"
									});

									taskRows += `
										<tr>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important;">${startIndex + index}</td>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important;">
												<span class="task-title" title="${task.title}">
													${task.title.length > 20 ? task.title.substring(0, 20) + '...' : task.title}
												</span>
											</td>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 170px;">
												<span class="scribble-text">${task.assigned_by_name}</span>
											</td>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 170px;">${formattedDate}</td>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important;">
												<span class="task-description" title="${task.description}">
													${task.description.length > 40 ? task.description.substring(0, 40) + '...' : task.description}
												</span>
											</td>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important; text-align: center;">
												<span class="badge" style="${statusClass}">
													${statusLabel[statusKey] || 'Pending'}
												</span>
											</td>
											<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 170px;">
												<button class="action-btn view-btn" data-id="${task.id}" title="View">
													<i class="fas fa-eye"></i>
												</button>
												<button class="action-btn edit-btn" data-id="${task.id}" title="Edit">
													<i class="fas fa-edit"></i>
												</button>
												<button class="action-btn delete-btn" data-id="${task.id}" title="Delete">
													<i class="fas fa-trash"></i>
												</button>
											</td>
										</tr>`;

								});
							} else {
								// Show "No tasks found" message when there are no tasks
								taskRows = `
									<tr>
										<td colspan="7" style="text-align: center; padding: 1.5rem; font-size: 16px; color: #999;">
											No tasks found
										</td>
									</tr>`;
							}

							$("#taskTableBody").html(taskRows);
							if (totalPages > 1) {
								$("#pagination").html(pagination).show();
							} else {
								$("#pagination").hide();
							}
						}
					}
				});
			}

			// Handle search & filters
			$("#searchBtn, #assignedByFilter, #statusFilter").on("click change", function () {
				loadTasks(1);
			});

			// Run search when "Enter" is pressed in the search input
			$("#searchInput").keypress(function (event) {
				if (event.which === 13) { // 13 is the Enter key
					event.preventDefault(); // Prevent default form submission
					loadTasks(1); // Trigger search
				}
			});

			$("#clearFiltersBtn").click(function () {
				$("#searchInput").val("");
				$("#assignedByFilter").val("");
				$("#statusFilter").val("");
				loadTasks(1); // Reload tasks with no filters
			});

			$("#searchBtn").click(function () {
				loadTasks(1);
			});

			// Handle pagination click
			$(document).on("click", ".pagination a", function (e) {
				e.preventDefault();
				var page = $(this).attr("data-page");
				loadTasks(page);
			});

			// View Task modal
			$(document).on("click", ".view-btn", function () {
				var taskId = $(this).data("id");

				$.ajax({
					url: "<?php echo base_url('web/assignTask/get-task-details'); ?>",
					type: "GET",
					data: { task_id: taskId },
					dataType: "json",
					success: function (response) {
						if (response.success) {
							var task = response.task;

							$("#view_task_title").text(task.title);
							$("#view_assigned_by").text(task.created_by_name);
							$("#view_due_date").text(new Date(task.due_date).toLocaleDateString("en-GB", {
								day: "2-digit",
								month: "short",
								year: "numeric"
							}));
							$("#view_task_description").text(task.description);
							
							// Status Badge
							var statusStyles = {
								0: "color: #805dca;",
								1: "color: #2196f3;",
								2: "color: #00ab55;",
								3: "color: #e7515a;"
							};

							var statusLabels = {
								0: "Pending",
								1: "In Progress",
								2: "Completed",
								3: "Overdue"
							};
							
							$("#view_task_status").attr("style", statusStyles[task.status] || "").text(statusLabels[task.status]);

							// Show Modal
							$("#viewTaskModal").modal("show");
						} else {
							Swal.fire("Error", "Failed to fetch task details.", "error");
						}
					},
					error: function () {
						Swal.fire("Error", "An error occurred while fetching task details.", "error");
					},
				});
			});

			// Open Edit Modal and Fetch Task Details
			$(document).on("click", ".edit-btn", function () {
				var taskId = $(this).data("id");

				$.ajax({
					url: "<?php echo base_url('web/assignTask/get-task-details'); ?>",
					type: "GET",
					data: { task_id: taskId },
					dataType: "json",
					success: function (response) {
						Swal.close(); // Close the loading SweetAlert

						if (response.success) {
							$("#edit_task_id").val(response.task.id);
							$("#edit_status").val(response.task.status); // Set current status
							$("#editTaskModal").modal("show");
						} else {
							Swal.fire("Error", "Failed to fetch task details.", "error");
						}
					},
					error: function () {
						Swal.fire("Error", "An error occurred while fetching task details.", "error");
					},
				});
			});

			// Handle Task Status Update
			$("#editTaskForm").submit(function (e) {
				e.preventDefault();

				var taskId = $("#edit_task_id").val();
				var status = $("#edit_status").val();

				if (!status) {
					Swal.fire("Warning", "Please select a status.", "warning");
					return;
				}

				// Disable button & show loader
				$("#saveTaskBtn").prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');

				$.ajax({
					url: "<?php echo base_url('web/assignTask/update-status'); ?>",
					type: "POST",
					data: {
						task_id: taskId,
						status: status,
					},
					dataType: "json",
					success: function (response) {
						// Re-enable button
						$("#saveTaskBtn").prop("disabled", false).html("Save Changes");

						if (response.success) {
							Swal.fire({
								title: "Success!",
								text: "Task status updated successfully.",
								icon: "success",
								timer: 2000,
								showConfirmButton: false,
							});

							$("#editTaskModal").modal("hide");
							loadTasks(1);
						} else {
							Swal.fire("Error", "Failed to update task status.", "error");
						}
					},
					error: function () {
						// Re-enable button
						$("#saveTaskBtn").prop("disabled", false).html("Save Changes");

						Swal.fire("Error", "An error occurred while updating task status.", "error");
					},
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


