<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Form</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
	<style>
		/* Table styling */
        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
            vertical-align: middle;
            text-align: center;
        }
        .table th, .table td {
            padding: 10px;
            font-weight: normal;
            font-size: 14px;
        }
        /* Align item name select and input elements */
        .table select, .table input {
            width: 100%;
            box-sizing: border-box;
        }
		/* Style to wrap long text in Select2 */
		.wrap-text {
			white-space: normal !important;
			display: block;
			line-height: 1.2; /* Adjust line height for better readability */
		}

		.select2-container .select2-selection--single{
			min-height: 60px;
		}

		/* Ensure dropdown width and styling */
		.select2-container .select2-selection--single .select2-selection__rendered {
			text-align: left;
			white-space: normal; /* Allow text wrap in the selected option */
			line-height: 1.2;
			min-height: 60px;
		}

		.select2-dropdown {
			word-wrap: break-word; /* Wrap long text in dropdown */
		}

		.loader {
			border: 2px solid #f3f3f3;
			border-radius: 50%;
			border-top: 2px solid #3498db;
			width: 16px;
			height: 16px;
			animation: spin 1s linear infinite;
			display: inline-block;
			vertical-align: middle;
			margin-left: 8px;
		}

		@keyframes spin {
			0% { transform: rotate(0deg); }
			100% { transform: rotate(360deg); }
		}
	</style>
    <div class="container mt-5">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<div>
				<h4 class="mb-0">
					Welcome, <span id="userNameDisplay"><?php echo $this->session->userdata('username'); ?></span>
				</h4>
			</div>
			<div>
				<button type="button" class="btn btn-danger" id="logoutButton">
					Logout
				</button>
			</div>
		</div>
        <!-- New Customer Section -->
        <div class="card mb-4">
            <div class="card-body">
                <h4 class="card-title">New Customer (Walk-In)</h4>
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" placeholder="Enter Name">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="company-name">Company Name:</label>
                        <input type="text" class="form-control" id="company-name" placeholder="Enter Company Name">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="email">Mail:</label>
                        <input type="email" class="form-control" id="email" placeholder="Enter Email">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="mobile">Mobile:</label>
                        <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile Number">
                    </div>
                </div>
            </div>
        </div>

        <!-- Quote Section -->
        <div class="card">
            <div class="card-body">
                <h5>Re-Proposal</h5>
                <form id="reProposalForm">
					<div class="form-row">
						<div class="col-md-3 mb-3">
							<label for="deal-number">Deal Number:</label>
							<input type="text" class="form-control" name="dealNumber" id="deal-number" placeholder="Enter Deal Number">
							<div class="error-message"></div>
						</div>
					</div> 
					<div class="form-group">
						<label for="subject">Subject:</label>
						<input type="text" class="form-control" name="subject" id="subject" placeholder="Enter Subject">
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="account-name">Account Name:</label>
						<input type="text" class="form-control" name="accountName" id="account-name" placeholder="Enter Account Name">
						<div class="error-message"></div>
					</div>

					<!-- Quoted Items Table -->
					<h6>Quoted Items</h6>
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
									<select class="form-control" id="itemName" name="itemName" style="width: 100%;">
										<option value="">-None-</option>
									</select>
									<div class="error-message"></div>
								</td>
								<td style="min-width: 130px;">
									<select class="form-control" name="uom" id="uom">
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
								<td><input type="number" name="quantity" class="form-control" id="quantity" placeholder="Quantity">
								<div class="error-message"></div></td>
								<td><input type="number" name="unitPrice" class="form-control" id="unitPrice" placeholder="Unit Price">
								<div class="error-message"></div></td>
								<td><input type="number" name="total" class="form-control" id="total" placeholder="Total" readonly>
								<div class="error-message"></div></td>
							</tr>
						</tbody>
					</table>

					<!-- Totals Section -->
					<div class="row mt-4">
						<div class="col-md-3 offset-md-9">
							<div class="form-group">
								<label for="sub-total">Sub Total (AED):</label>
								<input type="number" name="subTotal" class="form-control" id="sub-total" placeholder="0" readonly>
								<div class="error-message"></div>
							</div>
							<div class="form-group">
								<label for="discount">Discount (AED):</label>
								<input type="number" name="discount" class="form-control" id="discount" placeholder="0">
								<div class="error-message"></div>
							</div>
							<div class="form-group">
								<label for="vat">VAT (AED):</label>
								<input type="number" name="vat" class="form-control" id="vat" placeholder="0">
								<div class="error-message"></div>
							</div>
							<div class="form-group">
								<label for="adjustment">Adjustment (AED):</label>
								<input type="number" name="adjustment" class="form-control" id="adjustment" placeholder="0">
								<div class="error-message"></div>
							</div>
							<div class="form-group">
								<label for="grand-total">Grand Total (AED):</label>
								<input type="number" name="grandTotal" class="form-control" id="grand-total" placeholder="0" readonly>
								<div class="error-message"></div>
							</div>
						</div>
					</div>

					<!-- Additional Details -->
					<div class="form-group">
						<label for="project">Project:</label>
						<input type="text" name="project" class="form-control" id="project" placeholder="Enter Project Name">
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="terms-of-payment">Terms of Payment:</label>
						<input type="text" name="termsOfPayment" class="form-control" id="terms-of-payment" placeholder="Enter Payment Terms">
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="specification">Specification:</label>
						<textarea class="form-control" name="specification" id="specification" rows="3" placeholder="Enter Specifications"></textarea>
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="general-exclusion">General Exclusion:</label>
						<textarea class="form-control" name="generalExclusion" id="general-exclusion" rows="3" placeholder="Enter General Exclusion"></textarea>
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="brand">Brand:</label>
						<input type="text" class="form-control" name="brand" id="brand" placeholder="Enter brand">
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="warranty">Warranty:</label>
						<input type="text" class="form-control" name="warranty" id="warranty" placeholder="Enter Warranty">
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="delivery">Delivery:</label>
						<input type="text" class="form-control" name="delivery" id="delivery" placeholder="Enter Delivery">
						<div class="error-message"></div>
					</div>
					<div class="form-group">
						<label for="valid-until">Valid Until:</label>
						<input type="date" class="form-control" name="validUntil" id="valid-until" placeholder="Enter Valid Until">
						<div class="error-message"></div>
					</div>

					<button type="submit" id="submitButton" class="btn btn-primary">
						Save
						<span class="loader" id="buttonLoader" style="display: none;"></span>
					</button>
				</form>
            </div>
        </div>
    </div>

	<!-- Include jQuery and Select2 JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
				let discount = parseFloat($('#discount').val()) || 0;
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

		// Handle form submission
		$(document).ready(function () {

			$('#reProposalForm').submit(function (e) {
				e.preventDefault();

				// Reset any previous error messages
				$('.error-message').empty();

				var formData = $(this).serialize();

				// Validation check for required fields (you can adjust this if needed)
				var isValid = true;
				$('input, select, textarea').each(function () {
					if ($(this).val() === '') {
						$(this).addClass('is-invalid');
						$(this).siblings('.error-message').text('This field is required.').addClass('text-danger');
						isValid = false;
					} else {
						$(this).removeClass('is-invalid');
						$(this).siblings('.error-message').empty();
					}
				});

				if (!isValid) {
					return; // Prevent form submission if validation fails
				}

				// Show loader
				$('#buttonLoader').show();
        		$('#submitButton').prop('disabled', true);

				// AJAX request for proposal creation
				$.ajax({
					url: 'dashboard/add_proposal',
					type: 'POST',
					data: formData,
					dataType: 'json',
					success: function (response) {
						// Hide loader on success
						$('#buttonLoader').hide();
                		$('#submitButton').prop('disabled', false);

						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Proposal Created!',
								text: 'The proposal has been created successfully.',
								showConfirmButton: false,
								timer: 1500
							});
							$('#reProposalForm')[0].reset();
							fetchData(1, $('#searchInput').val(), sort_by, sort_direction);
						}
					},
					error: function (xhr) {
						// Hide loader on error
						$('#buttonLoader').hide();
						$('#submitButton').prop('disabled', false);

						// Log the response to debug
						console.log(xhr.responseJSON);  // Log the entire response to inspect the error message

						// Handle error response
						var errorMessage = xhr.responseJSON.error || 'An unexpected error occurred. Please try again.';  // Default error message

						// Show the error message in SweetAlert
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,  // Show the error message from the response
							showConfirmButton: true,
						});
					}
				});
			});
		});

		// Handle logout
		$(document).ready(function () {
			// Handle logout button click
			$('#logoutButton').click(function (e) {
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
							success: function (response) {
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
							error: function (xhr) {
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
</body>
</html>
