<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - <?= htmlspecialchars($user['username']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --primary-color: #dc3545;
            --primary-dark: #c82333;
            --primary-light: #f8d7da;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-900: #111827;
        }

        body {
            background: var(--gray-50);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: var(--gray-900);
        }

        .page-header {
            background: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
			margin-top: 2rem;
            border-bottom: 1px solid var(--gray-200);
        }

		div.dataTables_wrapper div.dataTables_length label {
			margin: 0;
		}

        .user-info-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .user-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            font-weight: 600;
            margin-right: 1.5rem;
        }

        .user-name {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .user-meta {
            color: var(--gray-600);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .info-item {
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .info-label {
            font-weight: 600;
            color: var(--gray-600);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--gray-900);
            font-size: 0.95rem;
            font-weight: 500;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            padding: 1.25rem;
            border-radius: 10px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border-top: 3px solid var(--primary-color);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .stat-card.highlight {
            border-top-color: var(--success-color);
            background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        }

        .stat-number {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .filter-controls {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-label {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 0.875rem;
        }

        .filter-select {
            padding: 0.625rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            background: white;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 200px;
            font-weight: 500;
        }

        .filter-select:hover {
            border-color: var(--primary-color);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .tasks-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0;
        }

        .btn-back {
            background: var(--gray-600);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
    		text-decoration: none;
        }

        .btn-back:hover {
            background: var(--gray-900);
            color: white;
            transform: translateY(-1px);
        }

        .btn-export-csv {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }

        .btn-export-csv:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }

        #tasksTable {
            width: 100% !important;
        }

        #tasksTable thead th {
            background: var(--gray-100);
            color: var(--gray-900);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border-bottom: 2px solid var(--gray-200);
            white-space: nowrap;
        }

        #tasksTable tbody td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
            font-size: 0.875rem;
            color: var(--gray-900);
            border-bottom: 1px solid var(--gray-100);
        }

        #tasksTable tbody tr:hover {
            background: var(--gray-50);
        }

        .badge {
            padding: 0.375rem 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            border-radius: 6px;
            white-space: nowrap;
        }

        .badge.bg-info {
            background-color: var(--info-color) !important;
        }

        .badge.bg-primary {
            background-color: var(--primary-color) !important;
        }

        .badge.bg-warning {
            background-color: var(--warning-color) !important;
            color: white !important;
        }

        .badge.bg-success {
            background-color: var(--success-color) !important;
        }

        .badge.bg-danger {
            background-color: var(--danger-color) !important;
        }

        .badge.bg-secondary {
            background-color: var(--gray-600) !important;
        }

        .btn-view {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 0.875rem;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
		    text-decoration: none;
        }

        .btn-view:hover {
            background: var(--primary-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .latest-status {
            font-size: 0.75rem;
            line-height: 1.4;
        }

        .status-label {
            font-weight: 700;
            color: var(--gray-900);
            display: block;
            margin-bottom: 0.125rem;
        }

        .status-date {
            color: var(--gray-600);
            font-size: 0.7rem;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            padding: 1rem 1.5rem;
        }

        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 1rem 1.5rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 2px solid var(--gray-200);
            padding: 0.5rem 0.875rem;
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: var(--primary-light) !important;
            border-color: var(--primary-color) !important;
            color: var(--primary-dark) !important;
        }

        table.dataTable {
            border-collapse: collapse !important;
        }

        @media (max-width: 768px) {
            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select {
                width: 100%;
            }

            .btn-export-csv {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="content flex-grow-1" id="mainContent">
        <div class="p-4" style="margin-top: 40px; max-width: 1600px; margin-left: auto; margin-right: auto;">

            <!-- Page Header -->
            <div class="page-header">
                <div class="container-fluid">
                    <a href="<?= site_url('web/users'); ?>" class="btn-back">
                        <i class="bi bi-arrow-left"></i>Back to Users
                    </a>
                </div>
            </div>

            <!-- User Info Card -->
            <div class="user-info-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="user-avatar">
                        <?= strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div>
                        <h1 class="user-name"><?= htmlspecialchars($user['username']); ?></h1>
                        <div class="user-meta">
                            <span>
                                <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email'] ?? 'No email'); ?>
                            </span>
                            <?php if ($user['role'] == '1'): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-primary">User</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Department</div>
                        <div class="info-value"><?= $user['department'] ? str_replace('_', ' ', ucwords(str_replace('_', ' ', $user['department']))) : '-'; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Company</div>
                        <div class="info-value"><?= htmlspecialchars($user['company'] ?? '-'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <?php if ($user['status'] == 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Quote Access</div>
                        <div class="info-value">
                            <?php if ($user['quote_access'] == '1'): ?>
                                <span class="badge bg-success">Enabled</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Disabled</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php
                $total_tasks = count($tasks);
                $status_counts = [
                    'Pending' => 0,
                    'Site Visit' => 0,
                    'Proposal' => 0,
                    'Close to Won' => 0,
                    'Close to Lost' => 0
                ];

                $closed_won_total = 0;

                // Count based on STATUS field from database
                foreach ($tasks as $task) {
                    $task_status = isset($task['status']) ? trim($task['status']) : '';
                    if (isset($status_counts[$task_status])) {
                        $status_counts[$task_status]++;

                        // Calculate closed won total
                        if ($task_status === 'Close to Won') {
                            $closed_won_total += floatval($task['service_charge'] ?? 0);
                        }
                    }
                }
                ?>

                <!-- Statistics Grid -->
                <div class="stats-grid" style="margin-top: 1.5rem;">
                    <div class="stat-card">
                        <div class="stat-number"><?= $total_tasks; ?></div>
                        <div class="stat-label">Total Jobs</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $status_counts['Pending']; ?></div>
                        <div class="stat-label">Qualification</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $status_counts['Site Visit']; ?></div>
                        <div class="stat-label">Site Visit</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $status_counts['Proposal']; ?></div>
                        <div class="stat-label">Proposal</div>
                    </div>
                    <div class="stat-card highlight">
                        <div class="stat-number"><?= number_format($closed_won_total, 0); ?><br>AED</div>
                        <div class="stat-label">Total Won Value</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?= $status_counts['Close to Won']; ?></div>
                        <div class="stat-label">Closed Won</div>
                    </div>
                    <!-- <div class="stat-card">
                        <div class="stat-number"><?= $status_counts['Close to Lost']; ?></div>
                        <div class="stat-label">Closed Lost</div>
                    </div> -->
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-controls">
                    <label for="status_filter" class="filter-label">Filter by Status:</label>
                    <select id="status_filter" class="filter-select">
                        <option value="all">All Status</option>
                        <option value="Pending">Qualification (Pending)</option>
                        <option value="Site Visit">Site Visit</option>
                        <option value="Proposal">Proposal</option>
                        <option value="Close to Won">Closed Won</option>
                        <option value="Close to Lost">Closed Lost</option>
                    </select>
                    <button class="btn-export-csv ms-auto" onclick="exportCSV()">
                        <i class="bi bi-filetype-csv"></i> Export CSV
                    </button>
                </div>
            </div>

            <!-- Tasks Table -->
            <div class="tasks-card">
                <div class="card-header-custom">
                    <h2 class="card-title">Assigned Jobs (<span id="filtered-count"><?= $total_tasks; ?></span>)</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tasksTable">
                            <thead>
                                <tr>
                                    <th>Deal Name</th>
                                    <th>Deal Number</th>
                                    <th>Account</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th>Contact</th>
                                    <th>Latest Stage</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($tasks)): ?>
                                    <?php foreach ($tasks as $task): ?>
                                        <?php
                                        // Determine latest progression stage
                                        $latest_stage = 'No progression';
                                        $latest_date = null;

                                        if (!empty($task['lost_deal_number'])) {
                                            $latest_stage = 'LOST';
                                            $latest_date = $task['lost_deal_date'];
                                        } elseif (!empty($task['job_deal_number'])) {
                                            $latest_stage = 'JOB';
                                            $latest_date = $task['job_deal_date'];
                                        } elseif (!empty($task['quote_deal_number'])) {
                                            $latest_stage = 'QUOTE';
                                            $latest_date = $task['quote_deal_date'];
                                        } elseif (!empty($task['site_deal_number'])) {
                                            $latest_stage = 'SITE VISIT';
                                            $latest_date = $task['site_deal_date'];
                                        } elseif (!empty($task['qual_deal_number'])) {
                                            $latest_stage = 'QUALIFICATION';
                                            $latest_date = $task['qual_deal_date'];
                                        } elseif (!empty($task['enq_number'])) {
                                            $latest_stage = 'ENQUIRY';
                                            $latest_date = $task['enq_deal_date'];
                                        }

                                        $task_status = isset($task['status']) ? trim($task['status']) : '-';
                                        ?>
                                        <tr data-status="<?= htmlspecialchars($task_status); ?>">
                                            <td><?= htmlspecialchars($task['deal_name'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($task['deal_number'] ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($task['account_name'] ?? '-'); ?></td>
                                            <td>
                                                <?php
                                                $status_badges = [
                                                    'Pending' => 'bg-info',
                                                    'Site Visit' => 'bg-primary',
                                                    'Proposal' => 'bg-warning',
                                                    'Close to Won' => 'bg-success',
                                                    'Close to Lost' => 'bg-danger'
                                                ];
                                                $badge_class = $status_badges[$task_status] ?? 'bg-secondary';
                                                ?>
                                                <span class="badge <?= $badge_class; ?>"><?= htmlspecialchars($task_status); ?></span>
                                            </td>
                                            <td><?= htmlspecialchars(number_format($task['service_charge'], 0) ?? '-'); ?></td>
                                            <td><?= htmlspecialchars($task['customer_contact'] ?? '-'); ?></td>
                                            <td>
                                                <div class="latest-status">
                                                    <span class="status-label"><?= $latest_stage; ?></span>
                                                    <?php if ($latest_date): ?>
                                                        <span class="status-date"><?= date('d M Y H:i', strtotime($latest_date)); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= $task['created_at'] ? date('d M Y H:i', strtotime($task['created_at'])) : '-'; ?></td>
                                            <td>
                                                <a href="<?= site_url('web/deal/details/' . $task['id']); ?>" class="btn-view">
                                                    <i class="bi bi-eye-fill"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">No tasks assigned to this user</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script>
    let dataTable;

    $(document).ready(function() {
        // Initialize DataTable
        dataTable = $('#tasksTable').DataTable({
            pageLength: 25,
            order: [[7, 'desc']],
            scrollX: false,
            autoWidth: true,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ tasks per page",
                info: "Showing _START_ to _END_ of _TOTAL_ tasks",
                infoEmpty: "No tasks found",
                infoFiltered: "(filtered from _MAX_ total tasks)"
            },
            drawCallback: function() {
                updateFilteredCount();
            }
        });

        // Filter functionality
        $('#status_filter').on('change', function() {
            const selectedStatus = $(this).val();

            if (selectedStatus === 'all') {
                dataTable.rows().nodes().to$().show();
                dataTable.search('').draw();
            } else {
                // Show all rows first
                dataTable.rows().nodes().to$().show();

                // Hide rows that don't match the selected status
                dataTable.rows().nodes().to$().each(function() {
                    const rowStatus = $(this).data('status');
                    if (rowStatus !== selectedStatus) {
                        $(this).hide();
                    }
                });

                dataTable.draw();
            }

            updateFilteredCount();
        });
    });

    function updateFilteredCount() {
        const visibleRows = $('#tasksTable tbody tr:visible').length;
        $('#filtered-count').text(visibleRows);
    }

    function exportCSV() {
        const filter = document.getElementById('status_filter').value;
        const urlParts = window.location.pathname.split('/');
        const userId = urlParts[urlParts.length - 1];

        window.location.href = '<?= base_url('web/users/export-tasks-csv/'); ?>' + userId + '?status_filter=' + filter;
    }

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
					url: '<?= site_url('web/Login/logout') ?>', // This should be the route for logging out
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
								window.location.href = '<?= site_url('web/Login/index') ?>';
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
    </script>

</body>
</html>
