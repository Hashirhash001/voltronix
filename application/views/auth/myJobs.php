<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobs</title>
	<link rel="icon" href="<?php echo base_url('assets/photos/logo/favicon.png'); ?>" type="image/x-icon">
    <link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
	    .nav-link:hover {
            background-color: #FF0100 !important;
            color: #fff !important;
            text-decoration: none;
        }
        
        .sidebar div img{
            height: 50px; 
            width: auto;
        }
        
        .active {
            background-color: #FF0100 !important;
            color: #fff !important;
        }
        
        .header-main{
            margin-left: 250px;
            background-color: #fff; 
            height: 70px; 
            position: fixed; 
            top: 0; left: 0; 
            z-index: 1050; 
            width: calc(100% - 250px); 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        /* Sidebar (Popup Style) */
        .sidebar {
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #fff;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 1050; /* Ensure sidebar is above other content */
        }
        
        /* Sidebar open style */
        .sidebar.open {
            transform: translateX(0); /* Show the sidebar */
        }
        
        /* Main content styles */
        #mainContent {
            transition: margin-left 0.3s ease-in-out;
            width: 100%;
            margin-left: 250px; /* Default when sidebar is visible */
        }
        
        /* Hamburger button inside header */
        #hamburgerButtonInsideHeader {
            background-color: #fff;
            border: none;
            font-size: 1.5rem;
            z-index: 1100;
        }
        
        /* Close button styling */
        .close-sidebar-button {
            display: none;
        }
        
        /* Mobile view - Hamburger button appears */
        @media (max-width: 768px) {
            #hamburgerButton {
                display: block; /* Show the hamburger menu button */
            }
        
            #hamburgerButtonInsideHeader {
                display: block;
                position: absolute;
                top: 20px;
                left: 20px;
                z-index: 1100;
            }
        
            /* When sidebar is closed, make the header full width */
            #mainContent {
                margin-left: 0; /* Make content full-width */
            }
        
            /* Sidebar hidden state */
            .sidebar.open {
                transform: translateX(0);
            }
        
            /* Adjust layout when sidebar is open */
            header {
                width: 100%;
            }
        
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1100;
            }
            
            .header-main {
                margin-left: 0;
                width: 100%;
            }
            
            /* Close button styling */
            .close-sidebar-button {
                display: block;
                font-size: 1.5rem;
                border: none;
                background: none;
                cursor: pointer;
                z-index: 1100;
            }
        }
        
        /* Large screens - Hide hamburger and adjust layout */
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0); /* Sidebar is always visible */
            }
        
            #mainContent {
                /*margin-left: 250px;*/
            }
        
            #hamburgerButton {
                display: none; /* Hide hamburger on desktop */
            }
        }
	</style>
	
	<script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.getElementById("sidebar");
            const hamburgerButton = document.getElementById("hamburgerButton");
            const closeSidebarButton = document.getElementById("closeSidebarButton");
            const mainContent = document.getElementById("mainContent");
        
            // Toggle sidebar when the hamburger button is clicked
            hamburgerButton.addEventListener("click", function () {
                sidebar.classList.add("open");
            });
        
            // Close sidebar when the close button is clicked
            closeSidebarButton.addEventListener("click", function () {
                sidebar.classList.remove("open");
            });
        });
    </script>
	
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
                    <a href="<?php echo site_url('web/deals'); ?>" class="nav-link d-flex align-items-center gap-2 active">
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
        
        <!-- Hamburger Menu Button (Visible on Mobile) -->
        <!--<button class="btn btn-outline-dark d-md-none" id="hamburgerButton" style="top: 20px; left: 20px; z-index: 1100;">-->
        <!--    <i class="bi bi-list"></i>-->
        <!--</button>-->
    
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
                <h1 class="mb-4">My Jobs</h1>
                
                <!-- Search Box -->
                <div class="input-group mb-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search by Deal Number or Deal Name">
                    <button class="btn btn-danger" type="button" id="searchButton">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
    
                <!-- Error Message -->
                <div id="errorMessage" class="alert alert-warning d-none"></div>
    
                <?php if (isset($error)): ?>
                    <div class="alert alert-warning">
                        <?= htmlspecialchars($error); ?>
                    </div>
                <?php else: ?>
                    <!-- Deals Cards -->
                    <div class="row" id="dealsContainer">
                        <?php foreach ($tasks as $task): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card shadow-sm card-hover">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($task['deal_name']); ?></h5>
                                        <h4 class="card-title" style="color: #ff0000;"><?= htmlspecialchars($task['deal_number'] ?? ''); ?></h4>
                                        <p class="card-text text-muted" style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;"><?= htmlspecialchars($task['complaint_info'] ?? ''); ?></p>
                                        <div class="d-flex justify-content-between mt-3">
                                            <button onclick="window.location.href='<?php echo site_url('web/deal/details/' . $task['id']); ?>'" class="btn btn-sm btn-danger" title="View">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
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
			
			// Trigger search on button click or input enter key
            $('#searchButton').on('click', searchDeals);
            $('#searchInput').on('keypress', function (e) {
                if (e.which === 13) { // Enter key
                    searchDeals();
                }
            });
        
            function searchDeals() {
                const query = $('#searchInput').val().trim();
        
                // Show a loading state (optional)
                $('#dealsContainer').html('<div class="text-center"><div class="spinner-border text-danger" role="status"><span class="sr-only">Loading...</span></div></div>');
        
                // Make AJAX request
                $.ajax({
                    url: '<?= site_url('web/deal/search'); ?>',
                    type: 'POST',
                    data: { query: query },
                    dataType: 'json',
                    success: function (response) {
                        console.log('Search Response:', response);
                        if (response.success) {
                            $('#errorMessage').addClass('d-none');
                            renderDeals(response.data);
                        } else {
                            $('#dealsContainer').empty();
                            $('#errorMessage').removeClass('d-none').text(response.message);
                        }
                    },
                    error: function () {
                        $('#dealsContainer').empty();
                        $('#errorMessage').removeClass('d-none').text('An error occurred while fetching tasks. Please try again.');
                    }
                });
            }
        
            function renderDeals(deals) {
                const dealsHtml = deals.map(deal => `
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm card-hover">
                            <div class="card-body">
                                <h5 class="card-title">${deal.deal_name}</h5>
                                <h4 class="card-title" style="color: #ff0000;">${deal.deal_number ?? ''}</h4>
                                <p class="card-text text-muted" style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; text-overflow: ellipsis;">${deal.complaint_info}</p>
                                <div class="d-flex justify-content-between mt-3">
                                    <button onclick="window.location.href='<?= site_url('web/deal/details/'); ?>${deal.id}'" class="btn btn-sm" title="View" style="background-color: #FF0100; color: #fff;">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
                $('#dealsContainer').html(dealsHtml);
            }
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
