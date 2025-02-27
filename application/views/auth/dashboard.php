		<!-- Extract deal counts dynamically -->
		<?php
			$total_deals = 0;
			$deals_status_count = [
				'Site Visit' => 0,
				'Proposal' => 0,
				'Close to Won' => 0,
				'Closed Lost/Omitted' => 0  // Merged category
			];
			
			foreach ($deals as $deal) {
				$total_deals += $deal['total']; // Sum all deals to get total count
			
				switch ($deal['status']) {
					case 'Site Visit':
						$deals_status_count['Site Visit'] += $deal['total'];
						break;
					case 'Proposal':
						$deals_status_count['Proposal'] += $deal['total'];
						break;
					case 'Close to Won':
						$deals_status_count['Close to Won'] += $deal['total'];
						break;
					case 'Close to Lost':
					case 'Omitted': // Merge Close to Lost & Omitted
						$deals_status_count['Closed Lost/Omitted'] += $deal['total'];
						break;
				}
			}
		?>
		
		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">
			<div class="container p-5" style="padding-top: 100px !important;">
				<!-- <h2 class="mb-4">Dashboard</h2> -->

				<!-- Cards for deal counts -->
				<div class="row">
					<div class="col-md-3">
						<div class="card-analytics text-white p-3" 
							style="background-color: #9cb4b6;">
							<div class="d-flex align-items-center text-dark">
								<i class="bi bi-list-task card-icon me-3"></i>
								<div>
									<h5>Total Jobs</h5>
									<h3><?= $total_deals; ?></h3>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="card-analytics text-white p-3" 
							style="background-color: #99d1d3;">
							<div class="d-flex align-items-center text-dark">
								<i class="bi bi-geo-alt card-icon me-3"></i>
								<div>
									<h5>Jobs in Site Visit</h5>
									<h3><?= $deals_status_count['Site Visit']; ?></h3>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="card-analytics text-white p-3" 
							style="background-color: #fcd34d;">
							<div class="d-flex align-items-center text-dark">
								<i class="bi bi-file-earmark-text card-icon me-3"></i>
								<div>
									<h5>Jobs in Proposal</h5>
									<h3><?= $deals_status_count['Proposal']; ?></h3>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="card-analytics text-white p-3" 
							style="background-color: #93cb9d; ">
							<div class="d-flex align-items-center text-dark">
								<i class="bi bi-check2-circle card-icon me-3"></i>
								<div>
									<h5>Closed Won Jobs</h5>
									<h3><?= $deals_status_count['Close to Won']; ?></h3>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Analytics Chart -->
				<div class="row mt-4">
					<div class="col-md-6">
						<div class="chart-container">
							<h5 class="text-center">Jobs Overview</h5>
							<canvas id="dealsChart"></canvas>
						</div>
					</div>

					<div class="col-md-6">
						<div class="chart-container">
							<h5 class="text-center">Conversion Funnel</h5>
							<canvas id="funnelChart"></canvas>
						</div>
					</div>
				</div>
			</div>

		</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
	<script>
		var dealsData = <?= json_encode($deals_status_count); ?>;
		var totalDeals = <?= $total_deals; ?>;

		console.log(dealsData);

		// Deals Status Pie Chart
		var ctxDeals = document.getElementById('dealsChart').getContext('2d');
		var dealsChart = new Chart(ctxDeals, {
			type: 'pie',
			data: {
				labels: ['Site Visit', 'Proposal', 'Close to Won', 'Closed Lost/Omitted'],
				datasets: [{
					data: [
						dealsData['Site Visit'],
						dealsData['Proposal'],
						dealsData['Close to Won'],
						dealsData['Closed Lost/Omitted']
					],
					backgroundColor: ['#1a99a0', '#f7c948', '#1e7b3c', '#e74c3c']
				}]
			}
		});

		// Funnel Chart (Bar Chart)
		var ctxFunnel = document.getElementById('funnelChart').getContext('2d');
		var funnelChart = new Chart(ctxFunnel, {
			type: 'bar',
			data: {
				labels: ['Total Jobs', 'Site Visit', 'Proposal', 'Close to Won', 'Closed Lost/Omitted'],
				datasets: [{
					label: 'Number of jobs',
					data: [
						totalDeals, 
						dealsData['Site Visit'] || 0,
						dealsData['Proposal'] || 0,
						dealsData['Close to Won'] || 0,
						dealsData['Closed Lost/Omitted'] || 0
					],
					backgroundColor: ['#3d6f72', '#1a99a0', '#f7c948', '#1e7b3c', '#e74c3c']
				}]
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

