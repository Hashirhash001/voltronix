<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
	<link rel="icon" href="<?php echo base_url('assets/photos/logo/favicon.png'); ?>" type="image/x-icon">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <link href="<?php echo base_url('assets/css/style2.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

	<script src="<?php echo base_url('assets/js/navbar.js'); ?>"></script>
</head>
<body>
    <div class="full-wrapper d-flex">
        <!-- Sidebar (Popup Style) -->
        <nav class="sidebar text-dark" id="sidebar">
            <!-- Sidebar Close Button -->
            <button class="btn btn-outline-dark close-sidebar-button" id="closeSidebarButton" style="position: absolute; top: 20px; right: 20px;">
                <i class="bi bi-x-lg"></i>
            </button>
            <!-- Sidebar Logo -->
            <div class="text-center py-3 border-bottom">
                <img src="<?php echo base_url('assets/photos/logo/voltronix_logo.png'); ?>" alt="Logo" style="height: 50px; width: auto;">
            </div>
            <!-- Navigation Menu -->
            <ul class="nav flex-column px-3 pt-3">
                <li class="nav-item mb-2">
                    <a href="<?php echo site_url('web/deals'); ?>" class="nav-link d-flex align-items-center gap-2">
                        <i class="bi bi-list-task"></i> My Jobs
                    </a>
                </li>
                <li class="nav-item mb-2">
                    <a href="<?php echo site_url('web/dashboard'); ?>" class="nav-link d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle"></i> Deals and Proposal
                    </a>
                </li>
            </ul>
        </nav>
    
        <!-- Header -->
        <header class="d-flex justify-content-between align-items-center px-4 py-3 header-main">
            <!-- Hamburger Button Inside Header (Visible on Mobile) -->
            <button class="btn btn-outline-dark d-md-none" id="hamburgerButton" style="top: 20px; left: 20px; z-index: 1100;">
            <i class="bi bi-list"></i>
        </button>
    
            <!-- Dashboard Title -->
            <h4 class="mb-0" id="userNameDisplay"></h4>
    
            <!-- User Dropdown -->
            <div class="dropdown d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-light dropdown-toggle d-flex align-items-center" id="logoutDropdownButton" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 20px; padding: 8px 16px; color: #000;">
                    <span class="me-2">
                        <i class="bi bi-person-circle"></i>
                    </span>
                    <span><?php echo $this->session->userdata('username'); ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="logoutDropdownButton" style="min-width: 150px;">
                    <li>
                        <a class="dropdown-item text-danger" href="#" id="logoutButton">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </header>
    
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
						<p class="card-text">
							<i class="bi bi-info-circle"></i>
							<span class="ms-2">Status:</span>
							<span class="ms-2">
								<?php 
									if ($task['status'] == 'Pending') {
										echo '<i class="bi bi-clock"></i> ' . htmlspecialchars($task['status']);
									} elseif ($task['status'] == 'Pending') {
										echo '<i class="bi bi-gear"></i> ' . htmlspecialchars($task['status']);
									} elseif ($task['status'] == 'Omitted') {
										echo '<i class="bi bi-x-circle"></i> ' . htmlspecialchars($task['status']);
									} elseif ($task['status'] == 'Proposal-Omitted') {
										echo '<i class="bi bi-x-circle"></i> ' . htmlspecialchars($task['status']);
									} elseif ($task['status'] == 'Proposal') {
										echo '<i class="bi bi-file-earmark"></i> ' . htmlspecialchars($task['status']);
									} elseif ($task['status'] == 'Close to Won') {
										echo '<i class="bi bi-check-circle"></i> ' . htmlspecialchars($task['status']);
									} else {
										echo '<i class="bi bi-clock"></i> ' . htmlspecialchars($task['status']);
									}
								?>
							</span>
						</p>
						<p class="card-text">
							<i class="bi bi-file-earmark-check"></i>
							<span class="ms-2">Quote Number:</span>
							<?= htmlspecialchars($task['quote_number'] ?? ''); ?>
						</p>

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
        // Handle logout
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
