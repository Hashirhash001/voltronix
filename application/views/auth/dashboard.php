<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Quote Form</title>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Include Bootstrap Icons if not already included -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">

</head>

<body>

	<div class="full-wrapper">
		<div class="d-flex justify-content-between align-items-center mb-4" style="background-color: #fff; height: 10vh; padding: 20px 40px;">
			<div class="d-flex align-items-center">
				<!-- Logo -->
				<img src="<?php echo base_url('assets/photos/logo/voltronix_logo.png'); ?>" alt="Logo" style="height: 40px; width: auto; margin-right: 15px;">
				
				<!-- Dashboard Title -->
				<h4 class="mb-0" id="userNameDisplay"></h4>
			</div>
			<div class="dropdown">
				<button type="button" class="btn btn-outline-light dropdown-toggle d-flex align-items-center" id="logoutDropdownButton" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 20px; padding: 8px 16px; color: #000;">
					<span class="me-2">
						<i class="bi bi-person-circle"></i>
					</span>
					<span><?php echo $this->session->userdata('username'); ?></span>
				</button>
				<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="logoutDropdownButton" style="min-width: 150px;">
					<li>
						<a class="dropdown-item text-danger" href="#" id="logoutButton">
							<i class="bi bi-box-arrow-right me-2"></i>Logout
						</a>
					</li>
				</ul>
			</div>
		</div>

		<div class="container pt-5 pb-5">
			<!-- <h2 class="dashboard-title">Dashboard</h2> -->
			<div class="accordion" id="accordionExample">
				<!-- New Customer Section -->
				<div class="card shadow-sm">
					<div class="card-header text-white" id="headingOne" style="background-color: #FF0100;">
						<h2 class="mb-0">
							<button class="btn btn-link text-white" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="font-weight: bold; text-decoration: none;">
								<i class="bi bi-plus-circle me-2"></i>Create New Deal
							</button>
						</h2>
					</div>

					<div id="accordionExample">
						<div class="card-body p-4">
							<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
								<form id="DealForm">
									<div class="form-row mb-3">
										<div class="col-md-3">
											<label for="first_name" class="form-label">First Name:</label>
											<input type="text" class="form-control shadow-sm" id="first_name" name="first_name" placeholder="Enter First Name" required>
										</div>
										<div class="col-md-3">
											<label for="last_name" class="form-label">Last Name:</label>
											<input type="text" class="form-control shadow-sm" id="last_name" name="last_name" placeholder="Enter Last Name" required>
										</div>
										<div class="col-md-3">
											<label for="VZ_app_user_id" class="form-label">VZ App User ID:</label>
											<select class="form-control shadow-sm" id="VZ_app_user_id" name="VZ_app_user_id" required>
												<option value="">Select VZ App User</option>
											</select>
										</div>
										<div class="col-md-3">
											<label for="company_name" class="form-label">Company Name:</label>
											<input type="text" class="form-control shadow-sm" id="company_name" name="company_name" placeholder="Enter Company Name" required>
										</div>
									</div>

									<div class="form-row mb-3">
										<div class="col-md-3">
											<label for="customer_email" class="form-label">Email:</label>
											<input type="email" class="form-control shadow-sm" id="customer_email" name="customer_email" placeholder="Enter Email" required>
										</div>
										<div class="col-md-3">
											<label for="mobile_num" class="form-label">Mobile Number:</label>
											<input type="text" class="form-control shadow-sm" id="mobile_num" name="mobile_num" placeholder="Enter Mobile Number" required>
										</div>
										<div class="col-md-3">
											<label for="phone" class="form-label">Phone:</label>
											<input type="text" class="form-control shadow-sm" id="phone" name="phone" placeholder="Enter Phone Number" required>
										</div>
										<div class="col-md-3">
											<label for="assign_department" class="form-label">Assign Department:</label>
											<select class="form-control shadow-sm" id="assign_department" name="assign_department" required>
												<option value="VOLTRONIX CONTRACTING LLC">VOLTRONIX CONTRACTING LLC</option>
												<option value="VOLTRONIX SWITCHGEAR LLC">VOLTRONIX SWITCHGEAR LLC</option>
											</select>
										</div>
									</div>

									<div class="form-row mb-3">
										<div class="col-md-12">
											<label for="description" class="form-label">Description:</label>
											<textarea class="form-control shadow-sm" id="description" name="complaint_info" placeholder="Enter Description" rows="3" required></textarea>
										</div>
									</div>

									<div class="text-end">
										<button type="submit" class="btn submitButton px-4 py-2" style="border-radius: 20px; font-weight: bold; background-color: #FF0100; color: #fff;">
											<span>Save</span>
											<span class="loader buttonLoader ms-2" style="display: none;"></span>
										</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Re-Proposal Section -->
				<div class="card shadow-sm">
					<div class="card-header text-white" id="headingTwo" style="background-color: #FF0100;">
						<h2 class="mb-0">
							<button class="btn btn-link text-white collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="font-weight: bold; text-decoration: none;">
							<i class="bi bi-plus-circle me-2"></i>Create a Proposal for a Deal
							</button>
						</h2>
					</div>

					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
						<div class="card-body">
							<form id="reProposalForm">
								<div class="form-row">
									<div class="col-md-3 mb-3">
										<label class="form-label" style="color: #000;" for="deal-number">Deal Number:</label>
										<input type="text" class="form-control" name="dealNumber" id="deal-number" placeholder="Enter Deal Number" required>
										<div class="error-message"></div>
									</div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="subject">Subject:</label>
									<input type="text" class="form-control" name="subject" id="subject" placeholder="Enter Subject" required>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="account-name">Account Name:</label>
									<input type="text" class="form-control" name="accountName" id="account-name" placeholder="Enter Account Name" required>
									<div class="error-message"></div>
								</div>

								<!-- Quoted Items Table -->
								<h6>Quoted Items</h6>
								<div class="table-responsive">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>S.NO</th>
												<th>Item Name</th>
												<th>U.O.M</th>
												<th>Quantity</th>
												<th>Unit Price (AED)</th>
												<th>Total (AED)</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>1</td>
												<td style="min-width: 400px;">
													<select class="form-control" id="itemName" name="itemName" style="width: 100%;" required>
														<option value="">-None-</option>
													</select>
													<div class="error-message"></div>
												</td>
												<td style="min-width: 130px;">
													<select class="form-control" name="uom" id="uom" required>
														<option value="">-None-</option>
														<option value="NOS">NOS</option>
														<option value="PCS">PCS</option>
														<option value="LS">LS</option>
														<option value="BAG">BAG</option>
														<option value="BKT">BKT</option>
														<option value="BND">BND</option>
														<option value="BOWL">BOWL</option>
														<option value="BX">BX</option>
														<option value="CRD">CRD</option>
														<option value="CM">CM</option>
														<option value="CS">CS</option>
														<option value="CTN">CTN</option>
														<option value="DZ">DZ</option>
														<option value="EA">EA</option>
														<option value="FT">FT</option>
														<option value="GAL">GAL</option>
														<option value="GROSS">GROSS</option>
														<option value="IN">IN</option>
														<option value="KIT">KIT</option>
														<option value="LOT">LOT</option>
														<option value="M">M</option>
														<option value="MM">MM</option>
														<option value="PC">PC</option>
														<option value="PK">PK</option>
														<option value="PK100">PK100</option>
														<option value="PK50">PK50</option>
														<option value="PR">PR</option>
														<option value="RACK">RACK</option>
														<option value="RL">RL</option>
														<option value="SET">SET</option>
														<option value="SET3">SET3</option>
														<option value="SET4">SET4</option>
														<option value="SET5">SET5</option>
														<option value="SGL">SGL</option>
														<option value="SHT">SHT</option>
														<option value="SQFT">SQFT</option>
														<option value="TUBE">TUBE</option>
														<option value="YD">YD</option>
														<option value="SQM">SQM</option>
													</select>
													<div class="error-message"></div>
												</td>
												<td style="min-width: 100px;">
													<input type="number" name="quantity" class="form-control no-arrows" id="quantity" placeholder="Quantity" required>
													<div class="error-message"></div>
												</td>
												<td style="min-width: 100px;">
													<input type="number" name="unitPrice" class="form-control no-arrows" id="unitPrice" placeholder="Unit Price" required>
													<div class="error-message"></div>
												</td>
												<td style="min-width: 100px;">
													<input type="number" name="total" class="form-control" id="total" placeholder="Total" readonly>
													<div class="error-message"></div>
												</td>
											</tr>
										</tbody>
									</table>
								</div>

								<!-- Totals Section -->
								<div class="row mt-4">
									<div class="col-md-5 offset-md-7">
										<div class="form-group d-flex align-items-center justify-content-between">
											<label style="color: #000; font-weight: 600;" for="sub-total" class="label-nowrap">Sub Total (AED)</label>
											<input type="number" name="subTotal" class="form-control no-arrows flex-input" id="sub-total" placeholder="0" readonly>
											<div class="error-message"></div>
										</div>
										<div class="form-group d-flex align-items-center justify-content-between">
											<label style="color: #000; font-weight: 600;" for="discount" class="label-nowrap">Discount % (AED)</label>
											<input type="number" name="discount" class="form-control no-arrows flex-input" id="discount" placeholder="0">
											<div class="error-message"></div>
										</div>
										<div class="form-group d-flex align-items-center justify-content-between">
											<label style="color: #000; font-weight: 600;" for="vat" class="label-nowrap">VAT (AED)</label>
											<input type="number" name="vat" class="form-control no-arrows flex-input" id="vat" placeholder="0" readonly>
											<div class="error-message"></div>
										</div>
										<div class="form-group d-flex align-items-center justify-content-between">
											<label style="color: #000; font-weight: 600;" for="adjustment" class="label-nowrap">Adjustment (AED)</label>
											<input type="number" name="adjustment" class="form-control no-arrows flex-input" id="adjustment" placeholder="0">
											<div class="error-message"></div>
										</div>
										<div class="form-group d-flex align-items-center justify-content-between">
											<label style="color: #000; font-weight: 600;" for="grand-total" class="label-nowrap">Grand Total (AED)</label>
											<input type="number" name="grandTotal" class="form-control no-arrows flex-input" id="grand-total" placeholder="0" readonly>
											<div class="error-message"></div>
										</div>

									</div>
								</div>

								<!-- Additional Details -->
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="project">Project:</label>
									<input type="text" name="project" class="form-control" id="project" placeholder="Enter Project Name" required>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="terms-of-payment">Terms of Payment:</label>
									<input type="text" name="termsOfPayment" class="form-control" id="terms-of-payment" placeholder="Enter Payment Terms" required>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="specification">Specification:</label>
									<textarea class="form-control" name="specification" id="specification" rows="3" placeholder="Enter Specifications" required></textarea>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="general-exclusion">General Exclusion:</label>
									<textarea class="form-control" name="generalExclusion" id="general-exclusion" rows="3" placeholder="Enter General Exclusion" required></textarea>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="brand">Brand:</label>
									<input type="text" class="form-control" name="brand" id="brand" placeholder="Enter brand" required>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="warranty">Warranty:</label>
									<input type="text" class="form-control" name="warranty" id="warranty" placeholder="Enter Warranty" required>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="delivery">Delivery:</label>
									<input type="text" class="form-control" name="delivery" id="delivery" placeholder="Enter Delivery" required>
									<div class="error-message"></div>
								</div>
								<div class="form-group">
									<label class="form-label" style="color: #000;" for="valid-until">Valid Until:</label>
									<input type="date" class="form-control" name="validUntil" id="valid-until" placeholder="Enter Valid Until" required>
									<div class="error-message"></div>
								</div>

								<div class="text-end">
									<button type="submit" id="" class="btn submitButton px-4 py-2" style="border-radius: 20px; font-weight: bold; background-color: #FF0100; color: #fff;">
										Save
										<span class="loader buttonLoader ms-2" style="display: none;"></span>
									</button>
								</div>
								
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Include jQuery and Select2 JavaScript -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Optional: Include Bootstrap's JavaScript (jQuery and Popper.js) for accordion functionality -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
	<script>
		// calculate the total price of the item
		$(document).ready(function() {
			function calculateTotals() {
				let quantity = parseFloat($('#quantity').val()) || 0;
				let unitPrice = parseFloat($('#unitPrice').val()) || 0;
				let total = quantity * unitPrice;
				$('#total').val(total.toFixed(2));

				// Calculate Sub Total (only one item row is present in this example)
				let subTotal = total;
				$('#sub-total').val(subTotal.toFixed(2));

				// Get other values for Grand Total calculation
				let discountPercentage = parseFloat($('#discount').val()) || 0;
				let discount = (discountPercentage / 100) * subTotal;
				let vat = parseFloat($('#vat').val()) || 0;
				let adjustment = parseFloat($('#adjustment').val()) || 0;

				let grandTotal = subTotal - discount + vat + adjustment;
				$('#grand-total').val(grandTotal.toFixed(2));
			}

			// Attach event listeners to calculate totals when Quantity or Unit Price changes
			$('#quantity, #unitPrice').on('input', calculateTotals);

			// Attach event listeners for discount, VAT, and adjustment fields to update Grand Total
			$('#discount, #vat, #adjustment').on('input', calculateTotals);
		});

		// Set today's date as the minimum date
		const dateInput = document.getElementById('valid-until');
		const today = new Date().toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
		dateInput.setAttribute('min', today);

		// Fetch item names from the API
		$(document).ready(function() {
			// Initialize Select2 with placeholder and clearing options
			$('#itemName').select2({
				placeholder: 'Search for an item...',
				allowClear: true,
				width: '100%',
				templateResult: formatOption,
				templateSelection: formatOption
			});

			// Fetch item names from the API
			fetch('https://app.voltronix.ae/voltronix/deal/products')
				.then(response => response.json())
				.then(data => {
					if (data.success && Array.isArray(data.products)) {
						data.products.forEach(product => {
							$('#itemName').append(new Option(product.name, product.id));
						});
					} else {
						console.error('Unexpected response format or no products found.');
					}
				})
				.catch(error => {
					console.error('Error fetching item names:', error);
				});

			// Format long text in dropdown and selection
			function formatOption(option) {
				if (!option.id) return option.text; // Show placeholder
				return $('<span class="wrap-text"></span>').text(option.text); // Wrap long text
			}
		});

		// Handle proposal creation
		// $(document).ready(function() {

		// 	$('#reProposalForm').submit(function(e) {
		// 		e.preventDefault();

		// 		// Reset any previous error messages
		// 		$('.error-message').empty();

		// 		var formData = $(this).serialize();

		// 		// Validation check for required fields (you can adjust this if needed)
		// 		var isValid = true;
		// 		$('input[required], select[required], textarea[required]').each(function() {
		// 			if ($(this).val() === '') {
		// 				$(this).addClass('is-invalid');
		// 				$(this).siblings('.error-message').text('This field is required.').addClass('text-danger');
		// 				isValid = false;
		// 			} else {
		// 				$(this).removeClass('is-invalid');
		// 				$(this).siblings('.error-message').empty();
		// 			}
		// 		});

		// 		if (!isValid) {
		// 			return;
		// 		}

		// 		// Show loader and disable button
		// 		$('.buttonLoader').show();
		// 		$('.submitButton').prop('disabled', true);

		// 		// AJAX request for proposal creation
		// 		$.ajax({
		// 			url: 'dashboard/add_proposal',
		// 			type: 'POST',
		// 			data: formData,
		// 			dataType: 'json',
		// 			success: function(response) {
		// 				// Hide loader and enable button on success
		// 				$('.buttonLoader').hide();
		//         		$('.submitButton').prop('disabled', false);

		// 				if (response.success) {
		// 					Swal.fire({
		// 						icon: 'success',
		// 						title: 'Proposal Created!',
		// 						text: 'The proposal has been created successfully.',
		// 						showConfirmButton: false,
		// 						timer: 1500
		// 					});
		// 					$('#reProposalForm')[0].reset();
		// 					fetchData(1, $('#searchInput').val(), sort_by, sort_direction);
		// 				}
		// 			},
		// 			error: function(xhr) {
		// 				// Hide loader and enable button on error
		// 				$('.buttonLoader').hide();
		//         		$('.submitButton').prop('disabled', false);

		// 				// Log the response to debug
		// 				console.log(xhr.responseJSON); // Log the entire response to inspect the error message

		// 				// Handle error response
		// 				var errorMessage = xhr.responseJSON.error || 'An unexpected error occurred. Please try again.'; // Default error message

		// 				// Show the error message in SweetAlert
		// 				Swal.fire({
		// 					icon: 'error',
		// 					title: 'Form Submission Error',
		// 					text: errorMessage, // Show the error message from the response
		// 					showConfirmButton: true,
		// 				});
		// 			}
		// 		});
		// 	});
		// });

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
							url: 'Login/logout', // This should be the route for logging out
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
										window.location.href = 'Login/index';
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

		// Fetch VZ App Users
		document.addEventListener("DOMContentLoaded", function() {
			fetchVZAppUsers();
		});

		function fetchVZAppUsers() {
			fetch('dashboard/get_vz_app_users')
				.then(response => response.json())
				.then(data => {
					const selectBox = document.getElementById("VZ_app_user_id");
					data.vz_app_users.forEach(user => {
						const option = document.createElement("option");
						option.value = user.id;
						option.text = user.name;
						selectBox.appendChild(option);
					});

					// Initialize Select2 on VZ_app_user_id
					$('#VZ_app_user_id').select2({
						placeholder: "Select VZ App User",
						allowClear: true,
						width: '100%' // Ensures the dropdown matches your form style
					});
				})
				.catch(error => console.error('Error fetching VZ App Users:', error));
		}

		$(document).ready(function() {
			$('#reProposalForm').submit(function(e) {
				e.preventDefault();

				// Reset any previous error messages
				$('.error-message').empty();

				var formData = $(this).serialize();

				// Show loader and disable button
				$('.buttonLoader').show();
				$('.submitButton').prop('disabled', true);

				// AJAX request for proposal creation
				$.ajax({
					url: 'dashboard/add_proposal',
					type: 'POST',
					data: formData,
					dataType: 'json',
					success: function(response) {
						// Hide loader and enable button on success
						$('.buttonLoader').hide();
						$('.submitButton').prop('disabled', false);

						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Proposal Created!',
								text: 'The proposal has been created successfully.',
								showConfirmButton: false,
								timer: 1500
							});

							// Reset the form after showing success message
							$('#reProposalForm')[0].reset();

							// Optionally, call fetchData if needed
							fetchData(1, $('#searchInput').val(), sort_by, sort_direction);
						}
					},
					error: function(xhr) {
						// Hide loader and enable button on error
						$('.buttonLoader').hide();
						$('.submitButton').prop('disabled', false);

						// Log the response to debug
						console.log(xhr.responseJSON);

						// Show error message
						var errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,
							showConfirmButton: true,
						});
					}
				});
			});

			$('#DealForm').on('submit', function(e) {
				e.preventDefault();

				// Reset any previous error messages
				$('.error-message').empty();

				// Collect form data
				var formData = {
					first_name: $('#first_name').val(),
					last_name: $('#last_name').val(),
					VZ_app_user_id: $('#VZ_app_user_id').val(),
					company_name: $('#company_name').val(),
					customer_email: $('#customer_email').val(),
					mobile_num: $('#mobile_num').val(),
					phone: $('#phone').val(),
					Assign_Department: $('[name="Assign_Department"]').val(),
					complaint_info: $('#description').val()
				};

				// Show loader and disable button
				$('.buttonLoader').show();
				$('.submitButton').prop('disabled', true);

				// AJAX request
				$.ajax({
					url: '<?php echo site_url('deals/create_lead_in_zoho'); ?>',
					type: 'POST',
					dataType: 'json',
					contentType: 'application/json',
					data: JSON.stringify(formData),
					success: function(response) {
						// Hide loader and enable button on success
						$('.buttonLoader').hide();
						$('.submitButton').prop('disabled', false);

						console.log('Deal Number Response:', response);

						if (response.success) {
							const dealId = response.deal_id;

							// Fetch the DealNumber
							$.ajax({
								url: '<?php echo site_url('web/dashboard/get_deal_number'); ?>',
								type: 'POST',
								dataType: 'json',
								data: JSON.stringify({
									deal_id: dealId
								}),
								contentType: 'application/json',
								success: function(dealResponse) {
									if (dealResponse.success) {
										const dealNumber = dealResponse.DealNumber;
										Swal.fire({
											title: 'Deal Created Successfully!',
											html: `<p>Deal Number: <strong id="dealNumber">${dealNumber}</strong></p> 
												<button onclick="copyDealNumber()" class="btn btn-primary">Copy Deal Number</button>`,
											icon: 'success'
										});
									} else {
										Swal.fire('Error', 'Deal created, but failed to fetch Deal Number.', 'warning');
									}
								},
								error: function() {
									Swal.fire('Error', 'Unable to retrieve Deal Number.', 'error');
								}
							});

							// Reset the Deal form after successful creation
							$('#DealForm')[0].reset();
						} else if (response.errors) {
							let errorMessages = '';
							$.each(response.errors, function(key, value) {
								errorMessages += `${value}<br>`;
							});
							Swal.fire('Validation Errors', errorMessages, 'warning');
						} else {
							Swal.fire('Error', response.error || 'An error occurred.', 'error');
						}
					},
					error: function(xhr) {
						// Hide loader and enable button on error
						$('.buttonLoader').hide();
						$('.submitButton').prop('disabled', false);

						// Log the response to debug
						console.log(xhr.responseJSON);

						// Show error message
						var errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,
							showConfirmButton: true,
						});
					}
				});
			});
		});

		// Copy Deal Number function
		function copyDealNumber() {
			const dealNumber = document.getElementById('dealNumber').textContent;
			navigator.clipboard.writeText(dealNumber).then(() => {
				Swal.fire('Copied!', 'Deal Number copied to clipboard.', 'success');
			}).catch((error) => {
				Swal.fire('Error', 'Failed to copy Deal Number.', 'error');
			});
		}
	</script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<!-- Bootstrap JS and dependencies -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
</body>

</html>
