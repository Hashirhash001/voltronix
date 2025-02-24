

		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">
			<div class="p-5" style="margin-top: 40px; padding-left: 32px !important; padding-right: 32px !important;">
				
				<!-- Search Box -->
				<div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
					<h1 class="mb-3 mb-md-0">My Jobs</h1>
					<div class="input-group" style="max-width: 400px;">
						<input 
							type="text" 
							id="searchInput" 
							class="form-control" 
							placeholder="Search by Deal Number or Deal Name"
							aria-label="Search Input">
						<button class="btn btn-danger" type="button" id="searchButton" aria-label="Search Button">
							<i class="fas fa-search"></i>
						</button>
					</div>
				</div>

				<!-- Error Message -->
				<div id="errorMessage" class="alert alert-warning d-none"></div>

				<?php if (isset($error)): ?>
					<div class="alert alert-warning">
						<?= htmlspecialchars($error); ?>
					</div>
				<?php else: ?>
					<!-- Deals Cards -->
					<div class="row flex-nowrap" style="overflow-x: auto; white-space: nowrap;" id="dealsContainer">
						<?php
						// Define categories based on status only, including the new "Closed Lost"
						$categories = [
							'Qualification' => ['Pending'],
							'Site Visit' => ['Site Visit'],
							'Proposal/Price Quote' => ['Proposal'],
							'Closed Won' => ['Close to Won'],
							'Closed Lost' => ['Close to Lost', 'Proposal-Omitted', 'Omitted'], // Added this line
						];

						// Define header colors and before element colors for each category
						$headerColors = [
							'Qualification' => ['header' => '#daf5f7', 'before' => '#99d1d3'], // Light blue
							'Site Visit' => ['header' => '#daf5f7', 'before' => '#99d1d3'],    // Light green
							'Proposal/Price Quote' => ['header' => '#fef3c7', 'before' => '#fcd34d'], // Light yellow
							'Closed Won' => ['header' => '#d4edda', 'before' => '#93cb9d'],    // Green
							'Closed Lost' => ['header' => '#f8d7da', 'before' => '#f0aeae'],   // Red
						];

						// Group tasks by category
						$groupedTasks = [];
						foreach ($tasks as $task) {
							foreach ($categories as $key => $statuses) {
								if (in_array($task['status'], $statuses)) {
									$groupedTasks[$key][] = $task;
									break;
								}
							}
						}

						// Render categories
						foreach ($categories as $key => $statuses):
							// Use the header and before colors for this category
							$headerColor = isset($headerColors[$key]['header']) ? $headerColors[$key]['header'] : '#ffffff'; // Default to white
							$beforeColor = isset($headerColors[$key]['before']) ? $headerColors[$key]['before'] : '#99d1d3'; // Default to light blue
						?>
							<div class="col-md-3 d-inline-block" style="width: 350px; padding-right: 0px;">
								<div class="card bg-light" style="border: none; box-shadow: none; width: 100%;">
									<div class="card-header" style="background-color: <?= htmlspecialchars($headerColor); ?>; margin: 0; padding: 10px; width: 100%; box-sizing: border-box; margin-bottom: 10px; position: relative; color: #000;">
										<h5 class="mb-0" style="font-size: 17px;"><?= htmlspecialchars($key); ?></h5>
										<!-- Dynamic ::before pseudo-element styling -->
										<div style="
											content: '';
											position: absolute;
											height: 4px;
											background: <?= htmlspecialchars($beforeColor); ?>;
											border-radius: 3px 3px 0 0;
											top: -1px;
											left: 0;
											right: 0;
										"></div>
									</div>
									<!-- Scrollable wrapper for card-body only -->
									<div class="card-body-wrapper" style="position: relative; height: 72vh; overflow-y: auto; overflow-x: hidden; padding-right: 0px; box-sizing: content-box;">
										<div class="card-body" style="padding: 0; margin: 0;">
											<?php if (isset($groupedTasks[$key])): ?>
												<?php foreach ($groupedTasks[$key] as $task): ?>
													<div class="mb-3 border-bottom pb-2" style="
														max-width: 100% !important;
														padding: 7px 36px 4px 15px;
														box-sizing: border-box;
														margin-bottom: 10px;
														min-height: 70px;
														box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .1);
														background: #fff;
													">
														<h6 style="font-weight: 600; margin-bottom: 2px; text-wrap: wrap;"><?= htmlspecialchars($task['deal_name']); ?></h6>
														<h4 style="color: #ff0000; margin-bottom: 2px;"><?= htmlspecialchars($task['deal_number'] ?? ''); ?></h4>
														<p style="margin-bottom: 2px;">Marieswaran</p>
														<p style="margin-bottom: 2px; text-wrap: wrap;"><?= htmlspecialchars($task['account_name'] ?? ''); ?></p>
														<!-- <small class="text-muted" style="text-wrap: wrap;"><?= htmlspecialchars($task['complaint_info'] ?? ''); ?></small> -->
														<p style="margin-bottom: 2px;"><?= htmlspecialchars($task['service_charge'] ?? ''); ?></p>
														<div class="mt-2">
															<button onclick="window.location.href='<?php echo site_url('web/deal/details/' . $task['id']); ?>'" class="btn btn-sm btn-danger">
																<i class="fas fa-eye"></i> View
															</button>
														</div>
													</div>
												<?php endforeach; ?>
											<?php else: ?>
												<div class="d-flex justify-content-center align-items-center">
													<p class="text-muted">No Jobs found.</p>
												</div>
											<?php endif; ?>
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
		const allDeals = <?= json_encode($tasks); ?>;

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

			// Trigger search on button click or input enter key
			$('#searchButton').on('click', searchDeals);
			$('#searchInput').on('keypress', function(e) {
				if (e.which === 13) { // Enter key
					searchDeals();
				}
			});

			function searchDeals() {
				const query = $('#searchInput').val().trim();

				// Show a loading state (optional)
				$('#dealsContainer').html('<div class="text-center"><div class="spinner-border text-danger" role="status"><span class="sr-only">Loading...</span></div></div>');

				// If query is empty, don't send an AJAX request, just show all deals
				if (query === '') {
					$('#errorMessage').addClass('d-none');
					renderDeals(allDeals); // `allDeals` should hold the original list of all tasks
					return;
				}

				// Make AJAX request
				$.ajax({
					url: '<?= site_url('web/deal/search'); ?>',
					type: 'POST',
					data: {
						query: query
					},
					dataType: 'json',
					success: function(response) {
						console.log('Search Response:', response);
						if (response.success) {
							$('#errorMessage').addClass('d-none');
							renderDeals(response.data);
						} else {
							$('#dealsContainer').empty();
							$('#errorMessage').removeClass('d-none').text(response.message);
						}
					},
					error: function() {
						$('#dealsContainer').empty();
						$('#errorMessage').removeClass('d-none').text('An error occurred while fetching tasks. Please try again.');
					}
				});
			}

			function renderDeals(deals) {
				console.log('Deals Data:', deals);

				// Test loop execution
				deals.forEach(deal => {
					console.log('Rendering Deal:', deal.deal_name);
				});

				const categories = {
					'Qualification': ['Pending'],
					'Site Visit': ['Site Visit'],
					'Proposal/Price Quote': ['Proposal'],
					'Closed Won': ['Close to Won'],
					'Closed Lost': ['Close to Lost', 'Proposal-Omitted', 'Omitted'],
				};

				const headerColors = {
					'Qualification': {
						header: '#daf5f7',
						before: '#99d1d3'
					},
					'Site Visit': {
						header: '#daf5f7',
						before: '#99d1d3'
					},
					'Proposal/Price Quote': {
						header: '#fef3c7',
						before: '#fcd34d'
					},
					'Closed Won': {
						header: '#d4edda',
						before: '#93cb9d'
					},
					'Closed Lost': {
						header: '#f8d7da',
						before: '#f0aeae'
					}
				};

				const groupedTasks = {};
				deals.forEach(deal => {
					// console.log('Deal Status:', deal.status);
					let matched = false;
					for (const [category, statuses] of Object.entries(categories)) {
						if (statuses.includes(deal.status)) {
							if (!groupedTasks[category]) groupedTasks[category] = [];
							groupedTasks[category].push(deal);
							matched = true;
						}
					}
					if (!matched) {
						// Add to a default category if no match
						if (!groupedTasks['Uncategorized']) groupedTasks['Uncategorized'] = [];
						groupedTasks['Uncategorized'].push(deal);
					}
				});

				const dealsHtml = Object.entries(categories).map(([key, statuses]) => {
					const headerColor = headerColors[key]?.header || '#ffffff';
					const beforeColor = headerColors[key]?.before || '#99d1d3';

					const tasksHtml = (groupedTasks[key] || []).map(task => `
								<div class="mb-3 border-bottom pb-2" style="
								max-width: 100% !important;
								padding: 7px 36px 4px 15px;
								box-sizing: border-box;
								margin-bottom: 10px;
								min-height: 70px;
								box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .1);
								background: #fff;
							">
								<h6 style="font-weight: 600; margin-bottom: 2px; text-wrap: wrap;">${task.deal_name}</h6>
								<h4 style="color: #ff0000; margin-bottom: 2px;">${task.deal_number ?? ''}</h4>
								<p style="margin-bottom: 2px;">Marieswaran</p>
								<p style="margin-bottom: 2px; text-wrap: wrap;">${task.account_name ?? ''}</p>
								<p style="margin-bottom: 2px;">${task.service_charge ?? ''}</p>
								<div class="mt-2">
									<button onclick="window.location.href='<?= site_url('web/deal/details/'); ?>${task.id}'" class="btn btn-sm btn-danger">
										<i class="fas fa-eye"></i> View
									</button>
								</div>
							</div>
						`).join('');

					return `
							<div class="col-md-3 d-inline-block" style="width: 350px; padding-right: 0px;">
								<div class="card bg-light" style="border: none; box-shadow: none; width: 100%;">
									<div class="card-header" style="background-color: ${headerColor}; margin: 0; padding: 10px; width: 100%; box-sizing: border-box; margin-bottom: 10px; position: relative; color: #000;">
										<h5 class="mb-0" style="font-size: 17px;">${key}</h5>
										<div style="
											content: '';
											position: absolute;
											height: 4px;
											background: ${beforeColor};
											border-radius: 3px 3px 0 0;
											top: -1px;
											left: 0;
											right: 0;
										"></div>
									</div>
									<div class="card-body-wrapper" style="position: relative; height: 72vh; overflow-y: auto; overflow-x: hidden; padding-right: 0px; box-sizing: content-box;">
										<div class="card-body" style="padding: 0; margin: 0;">
											${tasksHtml || `
												<div class="d-flex justify-content-center align-items-center">
													<p class="text-muted">No Jobs found.</p>
												</div>
											`}
										</div>
									</div>
								</div>
							</div>
						`;
				}).join('');


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
