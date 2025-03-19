<?php
// Default title
$page_title = "Default Title";

// Get URI segments
$segment2 = $this->uri->segment(2);
$segment3 = $this->uri->segment(3); // Third segment, if available

// Check the current page and set the title accordingly
switch ($segment2) {
	case 'deals':
		$page_title = "My Jobs";
		break;
	case 'dashboard':
		$page_title = "Dashboard";
		break;
	case 'dealsAndProposals':
		$page_title = "Deals and Proposal";
		break;
	case 'assignTask':
		// Check if there's a third segment
		if ($segment3 === 'all-tasks') {
			$page_title = "All Tasks";
		} elseif ($segment3 === 'my-tasks') {
			$page_title = "My Tasks";
		} else {
			$page_title = "Assign a Task";
		}
		break;
	default:
		$page_title = "Welcome";
		break;
}

// Default body class
$body_class = "";

// Add 'overflow-hidden' class only for 'My Jobs' page
if ($segment2 == 'deals') {
	$body_class = "overflow-hidden";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $page_title; ?></title>
	<link rel="icon" href="<?php echo base_url('assets/photos/logo/favicon.png'); ?>" type="image/x-icon">
	<link href="<?php echo base_url('assets/css/style2.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Include Bootstrap Icons if not already included -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<!-- Font Awesome Icons -->
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	<script src="https://code.highcharts.com/highcharts.js"></script>
	<script src="https://code.highcharts.com/modules/data.js"></script>
	<script src="https://code.highcharts.com/modules/drilldown.js"></script>
	<script src="https://code.highcharts.com/modules/exporting.js"></script>
	<script src="https://code.highcharts.com/modules/export-data.js"></script>
	<script src="https://code.highcharts.com/modules/accessibility.js"></script>
	<!-- FontAwesome Icons -->
	<!-- FontAwesome 6.7.2 -->
	<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha384-xxzJK+mIN2I4OeJGRkcz2M+yXDFp2g4XEkWUpa/X+O1gQaDjXnCqGfj9zRtqz7lT" crossorigin="anonymous"> -->
	<!-- <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

	<style>
		
		.nav-link:hover {
			background-color: #00000014 !important;
			color: #d10908 !important;
			text-decoration: none;
		}

		.sub-menu:hover {
			background-color: rgb(243 244 246 / 1) !important;
			color: #d10908 !important;
			text-decoration: none;
			border-radius: 5px !important;
		}

		.card-analytics {
            border-radius: 12px;
            transition: all 0.3s ease-in-out;
        }
        .card-analytics:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card-icon {
            font-size: 2rem;
            /* color: #000; */
        }
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

		/* Remove default select dropdown arrow */
		.form-select {
			-webkit-appearance: none;
			/* For Safari */
			-moz-appearance: none;
			/* For Firefox */
			appearance: none;
			/* Standard CSS */
			background-image: none;
			/* Remove background arrow */
			padding-right: 1rem;
			/* Adjust padding if needed */
		}

		/* Optionally, hide the native dropdown arrow for smaller screen sizes */
		@media (max-width: 576px) {
			.form-select {
				padding-right: 0.75rem;
				/* Fine-tune padding for small screens */
			}
		}
	</style>

	<script src="<?php echo base_url('assets/js/navbar.js'); ?>"></script>

</head>

<body class="<?php echo $body_class; ?>">

	<div class="full-wrapper d-flex">
		<!-- Header -->
		<header class="d-flex justify-content-between align-items-center px-2 py-3 header-main">
			
			<!-- Hamburger Button Inside Header (Visible on Mobile) -->
			<!-- <button class="btn btn-outline-dark d-md-none" id="hamburgerButton" style="top: 20px; left: 20px; z-index: 1100;">	
				<i class="bi bi-list"></i>
			</button> -->

			<div class="d-flex align-items-center justify-content-center gap-2">
				<!-- Sidebar Toggle Button (Desktop) -->
				<div class="hamburger-cover">
					<button class="hamburger d-none" id="toggleSidebar">
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>

				<!-- Dashboard Title -->
				<h4 class="mb-0" id="userNameDisplay" style="font-size: 20px; color: #dc3545; font-weight: 700;"><?php echo $this->session->userdata('company'); ?></h4>
			</div>

			<!-- User Dropdown -->
			<div class="dropdown d-flex align-items-center gap-2">
				<button type="button" class="btn btn-outline-light d-flex align-items-center me-4" id="logoutDropdownButton" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 20px; padding: 8px 16px; color: #000; background-color: #ffe5e5;">
					<span class="me-2">
						<svg class="h-4.5 w-4.5 shrink-0 ltr:mr-2 rtl:ml-2" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<circle cx="12" cy="6" r="4" stroke="currentColor" stroke-width="1.5"></circle>
							<path opacity="0.5" d="M20 17.5C20 19.9853 20 22 12 22C4 22 4 19.9853 4 17.5C4 15.0147 7.58172 13 12 13C16.4183 13 20 15.0147 20 17.5Z" stroke="currentColor" stroke-width="1.5"></path>
						</svg>
					</span>
					<span><?php echo $this->session->userdata('username'); ?></span>
				</button>
				<!-- <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="logoutDropdownButton" style="min-width: 150px;">
					<li>
						<a class="dropdown-item text-danger" href="#" id="logoutButton">
							<i class="bi bi-box-arrow-right me-2"></i> Logout
						</a>
					</li>
				</ul> -->
			</div>
		</header>
