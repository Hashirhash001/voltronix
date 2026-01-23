<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

		.user-link {
			transition: all 0.3s;
		}

		.user-link:hover {
			color: #dc3545 !important;
			text-decoration: underline !important;
		}
        
        .page-header {
            background: white;
            padding: 25px 32px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 24px;
            margin-top: 24px;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }
        
        .filters-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            margin-bottom: 24px;
        }
        
        .filter-label {
            font-weight: 600;
            font-size: 13px;
            color: #555;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-select, .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 14px;
            font-size: 14px;
        }
        
        .form-select:focus, .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }
        
        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .table-card .card-body {
            padding: 0;
        }
        
        #usersTable {
            margin: 0 !important;
        }
        
        #usersTable thead th {
            /* background: #2c3e50;
            color: white !important; */
			background-color: rgb(246 248 250 / 1) !important;
			color: #fff;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 18px 16px;
            border: none;
        }
        
        #usersTable tbody td {
            padding: 16px;
            vertical-align: middle;
            font-size: 14px;
            color: #2c3e50;
        }
        
        #usersTable tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }
        
        #usersTable tbody tr:hover {
            background: #ffffff;
        }
        
        .badge {
            padding: 6px 12px;
            font-weight: 500;
            font-size: 12px;
            border-radius: 6px;
        }
        
        .btn-edit {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .btn-edit:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
            color: white;
        }
        
        .btn-edit i {
            font-size: 14px;
        }
        
        .btn-reset {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-reset:hover {
            background: #5a6268;
            color: white;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 16px 20px;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 8px 14px;
        }
        
        .modal-header {
            background: #dc3545;
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px 24px;
        }
        
        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .modal-footer .btn-danger {
            background: #dc3545;
            border: none;
        }
        
        .modal-footer .btn-danger:hover {
            background: #c82333;
        }
        
        /* Custom Loader */
        .custom-loader {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        
        .spinner-custom {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #dc3545;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loader-text {
            margin-top: 16px;
            font-size: 15px;
            color: #dc3545;
            font-weight: 500;
        }

		.btn-view {
			background: #0d6efd;
			color: white;
			border: none;
			padding: 8px 14px;
			border-radius: 8px;
			transition: all 0.3s;
			font-size: 14px;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			justify-content: center;
		}

		.btn-view:hover {
			background: #0b5ed7;
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
			color: white;
		}

		.btn-view i {
			font-size: 14px;
		}

		.d-flex.gap-2 {
			gap: 8px;
		}

    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="content flex-grow-1" id="mainContent">
        <div class="p-4" style="margin-top: 40px; max-width: 1400px; margin-left: auto; margin-right: auto;">
            
            <!-- Page Header -->
            <div class="page-header d-flex justify-content-between align-items-center">
                <h1 class="page-title">Users Management</h1>
            </div>

            <!-- Filters Section -->
            <div class="filters-card">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="filter-label">Role</label>
                        <select class="form-select" id="roleFilter">
                            <option value="">All Roles</option>
                            <option value="1">Admin</option>
                            <option value="0">User</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">Status</label>
                        <select class="form-select" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="filter-label">Department</label>
                        <select class="form-select" id="departmentFilter">
                            <option value="">All Departments</option>
                            <option value="web_app">Web App</option>
                            <option value="web_and_mobile">Web and Mobile</option>
                            <option value="sales">Sales</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-reset w-100" onclick="resetFilters()">
                            <i class="bi bi-arrow-clockwise me-2"></i>Reset Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="table-card card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Quote Access</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editUserForm">
                        <input type="hidden" name="id" id="edit_user_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Username *</label>
                                <input type="text" class="form-control" name="username" id="edit_username" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Role *</label>
                                <select class="form-select" name="role" id="edit_role" required>
                                    <option value="0">User</option>
                                    <option value="1">Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status *</label>
                                <select class="form-select" name="status" id="edit_status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Department</label>
                                <select class="form-select" name="department" id="edit_department">
                                    <option value="">Select Department</option>
                                    <option value="web_app">Web App</option>
                                    <option value="web_and_mobile">Web and Mobile</option>
                                    <option value="sales">Sales</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Company</label>
                                <select class="form-select" name="company" id="edit_company">
                                    <option value="">Select Company</option>
                                    <option value="VOLTRONIX CONTRACTING LLC">Voltronix Contracting LLC</option>
                                    <option value="VOLTRONIX SWITCHGEAR LLC">Voltronix Switchgear LLC</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Quote Access</label>
                                <select class="form-select" name="quote_access" id="edit_quote_access">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="updateUser()">
                        <i class="bi bi-check-circle me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        var usersTable;
        var editModal;
        
        $(document).ready(function() {
            editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            
            // Initialize DataTable
            usersTable = $('#usersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?= site_url('web/users/get-ajax'); ?>',
                    type: 'POST',
                    data: function(d) {
                        d.role_filter = $('#roleFilter').val();
                        d.status_filter = $('#statusFilter').val();
                        d.department_filter = $('#departmentFilter').val();
                    }
                },
                columns: [
					{ data: 'username' },
					{ data: 'email' },
					{ data: 'role', orderable: false },
					{ data: 'department' },
					{ data: 'company' },
					{ data: 'status', orderable: false },
					{ data: 'quote_access', orderable: false },
					{ data: 'actions', orderable: false, searchable: false }
				],
				order: [[0, 'asc']],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                language: {
                    processing: '<div class="custom-loader"><div class="spinner-custom"></div><div class="loader-text">Loading users...</div></div>',
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ users",
                    infoEmpty: "Showing 0 to 0 of 0 users",
                    infoFiltered: "(filtered from _MAX_ total users)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            // Apply filters on change
            $('#roleFilter, #statusFilter, #departmentFilter').on('change', function() {
                usersTable.ajax.reload();
            });
        });

        function resetFilters() {
            $('#roleFilter').val('');
            $('#statusFilter').val('');
            $('#departmentFilter').val('');
            usersTable.ajax.reload();
        }

        function editUser(id) {
            $.ajax({
                url: '<?= site_url('web/users/edit/'); ?>' + id,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const user = response.data;
                        $('#edit_user_id').val(user.id);
                        $('#edit_username').val(user.username);
                        $('#edit_email').val(user.email);
                        $('#edit_role').val(user.role);
                        $('#edit_department').val(user.department || '');
                        $('#edit_company').val(user.company || '');
                        $('#edit_status').val(user.status);
                        $('#edit_quote_access').val(user.quote_access || '0');
                        
                        editModal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'Failed to load user data'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load user data'
                    });
                }
            });
        }

        function updateUser() {
            const formData = $('#editUserForm').serialize();
            
            Swal.fire({
                html: '<div class="custom-loader"><div class="spinner-custom"></div><div class="loader-text">Updating user in database and Zoho CRM...</div></div>',
                allowOutsideClick: false,
                showConfirmButton: false
            });
            
            $.ajax({
                url: '<?= site_url('web/users/update'); ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        editModal.hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'User updated successfully in both database and Zoho CRM',
                            timer: 2500,
                            showConfirmButton: false
                        });
                        usersTable.ajax.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error || 'Failed to update user'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update error:', xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update user. Please check console for details.'
                    });
                }
            });
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
