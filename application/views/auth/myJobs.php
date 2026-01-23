

		<!-- Main Content -->
		<div class="content flex-grow-1" id="mainContent">
			<div class="p-5" style="margin-top: 40px; padding-left: 32px !important; padding-right: 32px !important;">
				
				<!-- Search Box -->
				<div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
					<h1 class="mb-3 mb-md-0" style="font-size: 31px;">My Jobs</h1>
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
						// Define categories based on status
						$categories = [
							'Qualification' => ['Pending'],
							'Site Visit' => ['Site Visit'],
							'Proposal/Price Quote' => ['Proposal'],
							'Closed Won' => ['Close to Won'],
							'Closed Lost' => ['Close to Lost', 'Proposal-Omitted', 'Omitted'],
						];

						$headerColors = [
							'Qualification' => ['header' => '#daf5f7', 'before' => '#99d1d3'],
							'Site Visit' => ['header' => '#daf5f7', 'before' => '#99d1d3'],
							'Proposal/Price Quote' => ['header' => '#fef3c7', 'before' => '#fcd34d'],
							'Closed Won' => ['header' => '#d4edda', 'before' => '#93cb9d'],
							'Closed Lost' => ['header' => '#f8d7da', 'before' => '#f0aeae'],
						];

						// Group tasks by category (initial load)
						$groupedTasks = [];
						foreach ($tasks as $task) {
							foreach ($categories as $key => $statuses) {
								if (in_array($task['status'], $statuses)) {
									$groupedTasks[$key][] = $task;
									break;
								}
							}
						}

						foreach ($categories as $key => $statuses):
							$headerColor = $headerColors[$key]['header'] ?? '#ffffff';
							$beforeColor = $headerColors[$key]['before'] ?? '#99d1d3';
						?>
							<div class="col-md-3 d-inline-block" style="width: 350px; padding-right: 0px;">
								<div class="card bg-light" style="border: none; box-shadow: none; width: 100%;">
									<div class="card-header" style="background-color: <?= htmlspecialchars($headerColor); ?>; margin: 0; padding: 10px; width: 100%; box-sizing: border-box; margin-bottom: 10px; position: relative; color: #000;">
										<h5 class="mb-0" style="font-size: 17px;"><?= htmlspecialchars($key); ?></h5>
										<div style="content: ''; position: absolute; height: 4px; background: <?= htmlspecialchars($beforeColor); ?>; border-radius: 3px 3px 0 0; top: -1px; left: 0; right: 0;"></div>
									</div>
									<div class="card-body-wrapper" style="position: relative; height: 72vh; overflow-y: auto; overflow-x: hidden; padding-right: 0px; box-sizing: content-box;">
										<div class="card-body" style="padding: 0; margin: 0;">
                                            <?php if (isset($groupedTasks[$key]) && !empty($groupedTasks[$key])): ?>
                                                <?php foreach ($groupedTasks[$key] as $task): ?>
                                                    <div class="mb-3 border-bottom pb-2" style="max-width: 100% !important; padding: 7px 36px 4px 15px; box-sizing: border-box; margin-bottom: 10px; min-height: 70px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .1); background: #fff;">
                                                        <h6 style="font-weight: 600; margin-bottom: 2px; text-wrap: wrap;"><?= htmlspecialchars($task['deal_name']); ?></h6>
                                                        
                                                        <h4 style="color: #ff0000; margin-bottom: 2px; font-size: 1.2rem;">
                                                            <?php
                                                            $stageDealNumber = '';
                                                            $stageDealDate = '';
                                                            switch ($task['status']) {
                                                                case 'Pending':
                                                                    $stageDealNumber = $task['qual_deal_number'] ?? '';
                                                                    $stageDealDate = $task['qual_deal_date'] ?? '';
                                                                    break;
                                                                case 'Site Visit':
                                                                    $stageDealNumber = $task['site_deal_number'] ?? '';
                                                                    $stageDealDate = $task['site_deal_date'] ?? '';
                                                                    break;
                                                                case 'Proposal':
                                                                    $stageDealNumber = $task['quote_deal_number'] ?? '';
                                                                    $stageDealDate = $task['quote_deal_date'] ?? '';
                                                                    break;
                                                                case 'Close to Won':
                                                                    $stageDealNumber = $task['job_deal_number'] ?? '';
                                                                    $stageDealDate = $task['job_deal_date'] ?? '';
                                                                    break;
                                                                case 'Close to Lost':
                                                                case 'Proposal-Omitted':
                                                                case 'Omitted':
                                                                    $stageDealNumber = $task['lost_deal_number'] ?? '';
                                                                    $stageDealDate = $task['lost_deal_date'] ?? '';
                                                                    break;
                                                            }
                                                            echo htmlspecialchars($stageDealNumber);
                                                            ?>
                                                        </h4>
                                                        <?php if ($stageDealDate): ?>
                                                            <p style="margin-bottom: 2px; color: #555; font-size: 0.9rem;">
                                                                Updated: <?= htmlspecialchars(date('Y-m-d H:i:s', strtotime($stageDealDate))); ?>
                                                            </p>
                                                        <?php endif; ?>
                                                        <p style="margin-bottom: 2px; text-wrap: wrap;"><?= htmlspecialchars($task['assign_notes'] ?? ''); ?></p>
                                                        <p style="margin-bottom: 2px; text-wrap: wrap;"><?= htmlspecialchars($task['account_name'] ?? ''); ?></p>
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

					<!-- Error Message -->
					<div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert"></div>


				<?php endif; ?>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		const allDeals = <?= json_encode($tasks); ?>;

		$(document).ready(function() {
		
			// Track pagination state for each category separately
			const categoryState = {
				'Qualification': { page: 1, isLoading: false, hasMore: true }, // Will load page 2 next
				'Site Visit': { page: 1, isLoading: false, hasMore: true },
				'Proposal/Price Quote': { page: 1, isLoading: false, hasMore: true },
				'Closed Won': { page: 1, isLoading: false, hasMore: true },
				'Closed Lost': { page: 1, isLoading: false, hasMore: true }
			};

			// Attach scroll event to each category column
			$('.card-body-wrapper').each(function(index) {
				const categoryNames = ['Qualification', 'Site Visit', 'Proposal/Price Quote', 'Closed Won', 'Closed Lost'];
				const categoryName = categoryNames[index];
				
				$(this).on('scroll', function() {
					const scrollableDiv = $(this);
					const scrollTop = scrollableDiv.scrollTop();
					const scrollHeight = scrollableDiv[0].scrollHeight;
					const clientHeight = scrollableDiv[0].clientHeight;

					// Check if scrolled to bottom (with 50px threshold)
					if (scrollTop + clientHeight >= scrollHeight - 50) {
						loadMoreDealsForCategory(categoryName, scrollableDiv);
					}
				});
			});

			function loadMoreDealsForCategory(category, scrollableDiv) {
				const state = categoryState[category];
				
				if (state.isLoading || !state.hasMore) {
					return;
				}

				state.isLoading = true;
				state.page++;

				// Show loading indicator only in this column
				const cardBody = scrollableDiv.find('.card-body');
				cardBody.append(`
					<div class="text-center py-3 loading-indicator">
						<div class="spinner-border spinner-border-sm text-danger" role="status">
							<span class="sr-only">Loading...</span>
						</div>
						<p class="small text-muted mt-2">Loading more jobs...</p>
					</div>
				`);

				$.ajax({
					url: '<?= site_url('web/deals/paginated'); ?>',
					type: 'GET',
					data: { 
						page: state.page,
						category: category 
					},
					dataType: 'json',
					success: function(response) {
						console.log('Loaded page:', state.page, 'for category:', category, response);
						
						if (response.success && response.data && response.data.length > 0) {
							appendDealsToCategory(category, response.data, cardBody);
							state.hasMore = response.has_more;
						} else {
							state.hasMore = false;
							
							// Show "No more jobs" message
							if (response.data && response.data.length === 0 && state.page > 1) {
								cardBody.find('.loading-indicator').replaceWith(`
									<div class="text-center py-2">
										<p class="small text-muted">No more jobs to load</p>
									</div>
								`);
								setTimeout(() => {
									cardBody.find('.text-muted:contains("No more jobs")').parent().fadeOut(2000, function() {
										$(this).remove();
									});
								}, 1000);
							}
						}

						cardBody.find('.loading-indicator').remove();
						state.isLoading = false;
					},
					error: function(xhr, status, error) {
						console.error('Error loading more deals for', category, ':', error);
						cardBody.find('.loading-indicator').remove();
						state.isLoading = false;
						
						// Revert page number on error
						state.page--;
						
						// Show error message
						Swal.fire({
							icon: 'error',
							title: 'Error',
							text: 'Failed to load more jobs. Please try again.',
							toast: true,
							position: 'top-end',
							showConfirmButton: false,
							timer: 3000
						});
					}
				});
			}

			function appendDealsToCategory(category, deals, cardBody) {
				// Remove "No Jobs found" message if it exists
				cardBody.find('.text-muted:contains("No Jobs found")').parent().remove();

				deals.forEach(task => {
					const taskHtml = createTaskHtml(task, category);
					cardBody.append(taskHtml);
				});
			}

			function createTaskHtml(task, category) {
				let stageDealNumber = '';
				let stageDealDate = '';
				
				// Determine which deal number and date to show based on category
				switch (category) {
					case 'Qualification':
						stageDealNumber = task.qual_deal_number || '';
						stageDealDate = task.qual_deal_date || '';
						break;
					case 'Site Visit':
						stageDealNumber = task.site_deal_number || '';
						stageDealDate = task.site_deal_date || '';
						break;
					case 'Proposal/Price Quote':
						stageDealNumber = task.quote_deal_number || '';
						stageDealDate = task.quote_deal_date || '';
						break;
					case 'Closed Won':
						stageDealNumber = task.job_deal_number || '';
						stageDealDate = task.job_deal_date || '';
						break;
					case 'Closed Lost':
						stageDealNumber = task.lost_deal_number || '';
						stageDealDate = task.lost_deal_date || '';
						break;
				}

				const formattedDate = stageDealDate ? new Date(stageDealDate).toLocaleString('en-GB', {
					year: 'numeric',
					month: '2-digit',
					day: '2-digit',
					hour: '2-digit',
					minute: '2-digit',
					second: '2-digit',
					hour12: false
				}).replace(',', '') : '';

				return `
					<div class="mb-3 border-bottom pb-2" style="max-width: 100% !important; padding: 7px 36px 4px 15px; box-sizing: border-box; margin-bottom: 10px; min-height: 70px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .1); background: #fff;">
						<h6 style="font-weight: 600; margin-bottom: 2px; text-wrap: wrap;">${task.deal_name || ''}</h6>
						<h4 style="color: #ff0000; margin-bottom: 2px; font-size: 1.2rem;">${stageDealNumber}</h4>
						${formattedDate ? `<p style="margin-bottom: 2px; color: #555; font-size: 0.9rem;">Updated: ${formattedDate}</p>` : ''}
						<p style="margin-bottom: 2px; text-wrap: wrap;">${task.assign_notes || ''}</p>
						<p style="margin-bottom: 2px; text-wrap: wrap;">${task.account_name || ''}</p>
						<p style="margin-bottom: 2px;">${parseFloat(task.service_charge || 0).toFixed(2)}</p>
						<div class="mt-2">
							<button onclick="window.location.href='<?= site_url('web/deal/details/'); ?>${task.id}'" class="btn btn-sm btn-danger">
								<i class="fas fa-eye"></i> View
							</button>
						</div>
					</div>
				`;
			}

			// Search functionality
			$('#searchButton').on('click', searchDeals);
			$('#searchInput').on('keypress', function(e) {
				if (e.which === 13) {
					searchDeals();
				}
			});

			function searchDeals() {
				const query = $('#searchInput').val().trim();
				$('#dealsContainer').html('<div class="text-center"><div class="spinner-border text-danger" role="status"><span class="sr-only">Loading...</span></div></div>');

				if (query === '') {
					$('#errorMessage').addClass('d-none');
					location.reload();
					return;
				}

				// Disable pagination during search
				Object.keys(categoryState).forEach(key => {
					categoryState[key].hasMore = false;
				});

				$.ajax({
					url: '<?= site_url('web/deal/search'); ?>',
					type: 'POST',
					data: { query: query },
					dataType: 'json',
					success: function(response) {
						if (response.success && response.data) {
							$('#errorMessage').addClass('d-none');
							renderDeals(response.data);
						} else {
							$('#dealsContainer').html('<div class="text-center"><p class="text-muted">No Jobs found.</p></div>');
							$('#errorMessage').removeClass('d-none').text(response.message || 'No matching deals found.');
						}
					},
					error: function(xhr, status, error) {
						console.error('AJAX Error:', status, error);
						$('#dealsContainer').html('<div class="text-center"><p class="text-muted">Error loading deals.</p></div>');
						$('#errorMessage').removeClass('d-none').text('An error occurred while fetching deals. Please try again.');
					}
				});
			}

			function renderDeals(deals) {
				console.log('Rendering Deals:', deals);

				const categories = {
					'Qualification': ['Pending'],
					'Site Visit': ['Site Visit'],
					'Proposal/Price Quote': ['Proposal'],
					'Closed Won': ['Close to Won'],
					'Closed Lost': ['Close to Lost', 'Proposal-Omitted', 'Omitted']
				};

				const headerColors = {
					'Qualification': { header: '#daf5f7', before: '#99d1d3' },
					'Site Visit': { header: '#daf5f7', before: '#99d1d3' },
					'Proposal/Price Quote': { header: '#fef3c7', before: '#fcd34d' },
					'Closed Won': { header: '#d4edda', before: '#93cb9d' },
					'Closed Lost': { header: '#f8d7da', before: '#f0aeae' }
				};

				const groupedTasks = {};
				deals.forEach(deal => {
					let matched = false;
					for (const [category, statuses] of Object.entries(categories)) {
						if (statuses.includes(deal.status)) {
							if (!groupedTasks[category]) groupedTasks[category] = [];
							groupedTasks[category].push(deal);
							matched = true;
							break;
						}
					}
					if (!matched) {
						console.warn('Unmatched status:', deal.status, 'for deal:', deal.deal_name);
					}
				});

				const dealsHtml = Object.entries(categories).map(([key, statuses]) => {
                    const headerColor = headerColors[key]?.header || '#ffffff';
                    const beforeColor = headerColors[key]?.before || '#99d1d3';
                    const tasksHtml = (groupedTasks[key] || []).map(task => {
                        let stageDealNumber = '';
                        let stageDealDate = '';
                        switch (task.status) {
                            case 'Pending':
                                stageDealNumber = task.qual_deal_number || '';
                                stageDealDate = task.qual_deal_date || '';
                                break;
                            case 'Site Visit':
                                stageDealNumber = task.site_deal_number || '';
                                stageDealDate = task.site_deal_date || '';
                                break;
                            case 'Proposal':
                                stageDealNumber = task.quote_deal_number || '';
                                stageDealDate = task.quote_deal_date || '';
                                break;
                            case 'Close to Won':
                                stageDealNumber = task.job_deal_number || '';
                                stageDealDate = task.job_deal_date || '';
                                break;
                            case 'Close to Lost':
                            case 'Proposal-Omitted':
                            case 'Omitted':
                                stageDealNumber = task.lost_deal_number || '';
                                stageDealDate = task.lost_deal_date || '';
                                break;
                            default:
                                stageDealNumber = '';
                                stageDealDate = '';
                        }
                
                        // Format datetime if present
                        const formattedDate = stageDealDate ? new Date(stageDealDate).toLocaleString('en-GB', {
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        }).replace(',', '') : '';
                
                        return `
                            <div class="mb-3 border-bottom pb-2" style="max-width: 100% !important; padding: 7px 36px 4px 15px; box-sizing: border-box; margin-bottom: 10px; min-height: 70px; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, .1); background: #fff;">
                                <h6 style="font-weight: 600; margin-bottom: 2px; text-wrap: wrap;">${task.deal_name || ''}</h6>
                                <h4 style="color: #ff0000; margin-bottom: 2px; font-size: 1.2rem;">${stageDealNumber}</h4>
                                ${formattedDate ? `<p style="margin-bottom: 2px; color: #555; font-size: 0.9rem;">Updated: ${formattedDate}</p>` : ''}
                                <p style="margin-bottom: 2px; text-wrap: wrap;">${task.assign_notes || ''}</p>
                                <p style="margin-bottom: 2px; text-wrap: wrap;">${task.account_name || ''}</p>
                                <p style="margin-bottom: 2px;">${task.service_charge || ''}</p>
                                <div class="mt-2">
                                    <button onclick="window.location.href='<?= site_url('web/deal/details/'); ?>${task.id}'" class="btn btn-sm btn-danger">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('');
                
                    return `
                        <div class="col-md-3 d-inline-block" style="width: 350px; padding-right: 0px;">
                            <div class="card bg-light" style="border: none; box-shadow: none; width: 100%;">
                                <div class="card-header" style="background-color: ${headerColor}; margin: 0; padding: 10px; width: 100%; box-sizing: border-box; margin-bottom: 10px; position: relative; color: #000;">
                                    <h5 class="mb-0" style="font-size: 17px;">${key}</h5>
                                    <div style="content: ''; position: absolute; height: 4px; background: ${beforeColor}; border-radius: 3px 3px 0 0; top: -1px; left: 0; right: 0;"></div>
                                </div>
                                <div class="card-body-wrapper" style="position: relative; height: 72vh; overflow-y: auto; overflow-x: hidden; padding-right: 0px; box-sizing: content-box;">
                                    <div class="card-body" style="padding: 0; margin: 0;">
                                        ${tasksHtml || '<div class="d-flex justify-content-center align-items-center"><p class="text-muted">No Jobs found.</p></div>'}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
                
                $('#dealsContainer').html(dealsHtml);

				$('#dealsContainer').html(dealsHtml);
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
