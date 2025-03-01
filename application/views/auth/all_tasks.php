		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">

			<div class="container mt-4" style="margin-top: 100px !important;padding-left: 2.5rem; padding-right: 2.5rem; padding-bottom: 2.5rem;">
				<!-- <h2 class="mb-4 fw-bold">Task List</h2> -->

				<div class="d-flex flex-wrap align-items-center gap-3 mb-3 p-3 bg-white shadow-sm" style="    border-radius: .6rem !important;">
					<!-- Search Input -->
					<div class="position-relative flex-grow-1">
						<i class="bi bi-search position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-54%);"></i>
						<input type="text" id="searchInput" class="form-control ps-5" placeholder="Search tasks by task Id or title" style="box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important; border-radius: .25rem; font-size: 14px !important;">
					</div>

					<!-- Assigned To Filter -->
					<div class="position-relative flex-grow-1">
						<span class="position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-56%);">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5"></circle>
								<path d="M15 20.6151C14.0907 20.8619 13.0736 21 12 21C8.13401 21 5 19.2091 5 17C5 14.7909 8.13401 13 12 13C15.866 13 19 14.7909 19 17C19 17.3453 18.9234 17.6804 18.7795 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
							</svg>
						</span>
						<select id="assignedToFilter" class="form-select ps-5" style="padding-left: 40px; box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important; font-size: 14px !important;">
							<option value="">Assigned To</option>
							<?php foreach ($members as $member) : ?>
								<option value="<?php echo $member->user_id; ?>">
									<?php echo htmlspecialchars($member->username ?? ''); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<!-- Priority Filter -->
					<div class="position-relative flex-grow-1">
						<span class="position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 14px !important;">
							<!-- <i class="bi bi-list-task"></i> -->
							<i class="bi bi-exclamation-circle"></i>
						</span>
						<select id="priorityFilter" class="form-select ps-5" style="padding-left: 40px; font-size: 14px !important;">
							<option value="" data-icon="bi-exclamation-circle">Priority</option>
							<option value="0" data-icon="bi-arrow-down-circle">Low</option>
							<option value="1" data-icon="bi-arrow-right-circle">Normal</option>
							<option value="2" data-icon="bi-arrow-up-circle-fill">High</option>
						</select>

					</div>

					<!-- Status Filter -->
					<div class="position-relative flex-grow-1">
						<span class="position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%);">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<circle cx="19" cy="5" r="3" stroke="currentColor" stroke-width="1.5"></circle>
								<path d="M7 14H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
								<path d="M7 17.5H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
								<path d="M2 12C2 16.714 2 19.0711 3.46447 20.5355C4.92893 22 7.28595 22 12 22C16.714 22 19.0711 22 20.5355 20.5355C22 19.0711 22 16.714 22 12V10.5M13.5 2H12C7.28595 2 4.92893 2 3.46447 3.46447C2.49073 4.43821 2.16444 5.80655 2.0551 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
							</svg>
						</span>
						<select id="statusFilter" class="form-select ps-5" style="padding-left: 40px; box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important;font-size: 14px !important;">
							<option value="">Task Status</option>
							<option value="0">Pending</option>
							<option value="1">In Progress</option>
							<option value="2">Completed</option>
							<option value="3">Overdue</option>
						</select>
					</div>

					<!-- Action Buttons -->
					<div class="d-flex gap-2">
						<!-- <button id="searchBtn" class="btn btn-primary d-flex align-items-center gap-2" style="    background: #d10908; border: none !important;">
							<i class="bi bi-search"></i> <span>Search</span>
						</button> -->
						<button id="clearFiltersBtn" class="btn btn-outline-secondary d-flex align-items-center" style="box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px; border: unset !important; padding: 4px 7px;">
							<i class="bi bi-x-circle" style="font-size: 18px;"></i> <span></span>
						</button>
					</div>
				</div>
				
				<div class="table-container">
					<div class="table-responsive">
						<table id="taskTable" class="table align-middle">
							<thead>
								<tr>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Task Id</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Task Title</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;     min-width: 170px;">Assigned To</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Due Date</th>
									<!-- <th style="border: none !important; padding: 1.15rem 2.35rem !important;">Description</th> -->
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Priority</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Status</th>
									<th style="border: none !important; padding: 1.15rem 2.35rem !important;">Actions</th>
								</tr>
							</thead>
							<tbody id="taskTableBody"  style="border-top: 1px solid rgb(210 210 210);">
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
								<label for="priority" class="form-label fw-semibold">Priority</label>
								<select name="priority" id="edit_priority" class="form-select" required>
									<option value="0">Low</option>
									<option value="1">Normal</option>
									<option value="2">High</option>
								</select>
							</div>

							<div class="mb-3">
								<label for="edit_description" class="form-label">Description</label>
								<textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
							</div>

							<button type="submit" class="btn btn-primary w-100 border-0" id="saveTaskBtn" style="background: #d10908;">
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
						<p><strong>Task Id:</strong> <span id="view_task_id"></span></p>
						<p><strong>Title:</strong> <span id="view_task_title"></span></p>
						<p><strong>Assigned To:</strong> <span id="view_assigned_by"></span></p>
						<p><strong>Due Date:</strong> <span id="view_due_date"></span></p>
						<p><strong>Description:</strong> <span id="view_task_description"></span></p>
						<p><strong>Priority:</strong> <span id="view_task_priority"></span></p>
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

			// Function to truncate text with "..."
			function truncateText(text, maxLength) {
				return text.length > maxLength ? text.substring(0, maxLength) + "..." : text;
			}

			// Function to load tasks
			function loadTasks(page) {
				var search = $("#searchInput").val();
				var assignedTo = $("#assignedToFilter").val();
				var status = $("#statusFilter").val();
				var priority = $("#priorityFilter").val();

				$.ajax({
					url: "<?php echo base_url('web/assignTask/all-tasks'); ?>",
					type: "GET",
					data: { 
						page: page,
						search: search,
						assigned_to: assignedTo,
						status: status,
						priority: priority
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

									var priorityLabel = {
										0: '<span class="badge bg-success">Low</span>',
										1: '<span class="badge bg-warning">Normal</span>',
										2: '<span class="badge bg-danger">High</span>'
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
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;min-width: 170px;">${task.task_id}</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;text-wrap: auto;">
											<span class="task-title" title="${task.title}">
												${truncateText(task.title, 20)}
											</span>
										</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 170px;">
											<span class="scribble-text">${task.assigned_to_name}</span>
										</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 170px;">${formattedDate}</td>
										
										<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 170px;">${priorityLabel[task.priority] || '<span class="badge bg-secondary">Unknown</span>'}</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important;">
											<span class="badge" style="${statusClass}">
												${statusLabel[statusKey] || 'Pending'}
											</span>
										</td>
										<td style="border: none !important; padding: 1.15rem 2.35rem !important; min-width: 181px;">
											<button class="action-btn view-btn" data-id="${task.id}" title="View">
												<i class="fas fa-eye"></i>
											</button>
											<button class="action-btn edit-btn" data-id="${task.id}" title="Edit">
												<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-4.5 h-4.5">
                                                    <path d="M15.2869 3.15178L14.3601 4.07866L5.83882 12.5999L5.83881 12.5999C5.26166 13.1771 4.97308 13.4656 4.7249 13.7838C4.43213 14.1592 4.18114 14.5653 3.97634 14.995C3.80273 15.3593 3.67368 15.7465 3.41556 16.5208L2.32181 19.8021L2.05445 20.6042C1.92743 20.9852 2.0266 21.4053 2.31063 21.6894C2.59466 21.9734 3.01478 22.0726 3.39584 21.9456L4.19792 21.6782L7.47918 20.5844L7.47919 20.5844C8.25353 20.3263 8.6407 20.1973 9.00498 20.0237C9.43469 19.8189 9.84082 19.5679 10.2162 19.2751C10.5344 19.0269 10.8229 18.7383 11.4001 18.1612L11.4001 18.1612L19.9213 9.63993L20.8482 8.71306C22.3839 7.17735 22.3839 4.68748 20.8482 3.15178C19.3125 1.61607 16.8226 1.61607 15.2869 3.15178Z" stroke="currentColor" stroke-width="1.5"></path>
                                                    <path opacity="0.5" d="M14.36 4.07812C14.36 4.07812 14.4759 6.04774 16.2138 7.78564C17.9517 9.52354 19.9213 9.6394 19.9213 9.6394M4.19789 21.6777L2.32178 19.8015" stroke="currentColor" stroke-width="1.5"></path>
                                                </svg>
											</button>
											<button class="action-btn delete-btn" data-id="${task.id}" title="Delete">
												<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5">
                                                    <path opacity="0.5" d="M9.17065 4C9.58249 2.83481 10.6937 2 11.9999 2C13.3062 2 14.4174 2.83481 14.8292 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                    <path d="M20.5001 6H3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                    <path d="M18.8334 8.5L18.3735 15.3991C18.1965 18.054 18.108 19.3815 17.243 20.1907C16.378 21 15.0476 21 12.3868 21H11.6134C8.9526 21 7.6222 21 6.75719 20.1907C5.89218 19.3815 5.80368 18.054 5.62669 15.3991L5.16675 8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                    <path opacity="0.5" d="M9.5 11L10 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                    <path opacity="0.5" d="M14.5 11L14 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"></path>
                                                </svg>
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
			$("#searchBtn, #assignedToFilter, #statusFilter, #priorityFilter").on("click change", function () {
				loadTasks(1);
			});

			// Run search when "Enter" is pressed in the search input
			$("#searchInput").keypress(function (event) {
				if (event.which === 13) { // 13 is the Enter key
					event.preventDefault(); // Prevent default form submission
					loadTasks(1); // Trigger search
				}
			});

			// Clear filters
			$("#clearFiltersBtn").click(function () {
				$("#searchInput").val("");
				$("#assignedToFilter").val("");
				$("#statusFilter").val("");
				$("#priorityFilter").val("");

				// Reset the priority icon
				let iconElement = document.querySelector(".position-absolute i");
				if (iconElement) {
					iconElement.className = "bi bi-exclamation-circle"; // Default icon or remove the icon
				}

				loadTasks(1); // Reload tasks with no filters
			});

			// Search
			$("#searchBtn").click(function () {
				loadTasks(1);
			});

			// Handle pagination click
			$(document).on("click", ".pagination a", function (e) {
				e.preventDefault();
				var page = $(this).attr("data-page");
				loadTasks(page);
			});

			// Change priority icon dynamically
			document.getElementById("priorityFilter").addEventListener("change", function() {
				let selectedOption = this.options[this.selectedIndex];
				let iconClass = selectedOption.getAttribute("data-icon");
				let iconElement = document.querySelector(".position-absolute i");

				if (iconElement) {
					if (iconClass) {
						iconElement.className = "bi " + iconClass; // Update Bootstrap icon
					} else {
						iconElement.className = "bi-exclamation-circle"; // Reset to default
					}
				}
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

							$("#view_task_id").text(task.task_id);
							$("#view_task_title").text(task.title);
							$("#view_assigned_by").text(task.assigned_to_name);
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

							var priorityLabel = {
								0: '<span class="badge bg-success">Low</span>',
								1: '<span class="badge bg-warning">Normal</span>',
								2: '<span class="badge bg-danger">High</span>'
							};

							$("#view_task_priority").html(priorityLabel[task.priority] || '<span class="badge bg-secondary">Unknown</span>');

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
							$("#edit_priority").val(task.priority);
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
								showConfirmButton: false,
								timer: 1500,
								allowOutsideClick: false
							});

							// Update the table row dynamically
							var taskId = $("#edit_task_id").val();
							var row = $("button[data-id='" + taskId + "']").closest("tr");

							// Get new values
							var newTitle = $("#edit_task_title").val();
							var newAssignedTo = $("#edit_assigned_to option:selected").text();
							var newDueDate = $("#edit_due_date").val();
							var newDescription = $("#edit_description").val();
							var newPriority = parseInt($("#edit_priority").val(), 10); // Convert to integer

							var priorityLabel = {
								0: '<span class="badge bg-success">Low</span>',
								1: '<span class="badge bg-warning">Normal</span>',
								2: '<span class="badge bg-danger">High</span>'
							};

							// Truncate long texts
							var truncatedTitle = truncateText(newTitle, 30); // Limit to 30 characters
							var truncatedDescription = truncateText(newDescription, 50); // Limit to 50 characters

							// Convert date to readable format
							var formattedDate = new Date(newDueDate).toLocaleDateString("en-GB", {
								day: "2-digit",
								month: "short",
								year: "numeric"
							});

							// Update row with truncated content
							row.find("td:eq(1)").text(truncatedTitle);
							row.find("td:eq(2)").html(`<span class="">${newAssignedTo}</span>`);
							row.find("td:eq(3)").text(formattedDate);
							row.find("td:eq(4)").html(priorityLabel[newPriority] || '<span class="badge bg-secondary">Unknown</span>');
							// row.find("td:eq(5)").text(truncatedDescription);

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


