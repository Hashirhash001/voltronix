 
		<!-- Main Content -->
        <div class="content flex-grow-1" id="mainContent">

			<div class="container p-5" style="margin-top: 70px;">
				<!-- <h2 class="dashboard-title">Dashboard</h2> -->
				<div class="accordion" id="accordionExample">
					<!-- New Customer Section -->
					<div class="card shadow-sm">
						<div class="card-header text-white" id="headingOne" style="background-color: #d10908;">
							<h2 class="mb-0">
								<button class="btn btn-link text-white" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="font-weight: bold; text-decoration: none;">
									<i id="addDealIcon" class="bi bi-plus-circle me-2"></i>Create New Deal
								</button>
							</h2>
						</div>

						<div id="accordionExample">
							<div class="card-body" style="padding-top: 8px;">
								<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample" style="padding-top: 10px;">
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
											<input type="hidden" class="form-control shadow-sm" id="VZ_app_user_id" name="VZ_app_user_id" value="<?php echo $this->session->userdata('user_id'); ?>">
											<div class="col-md-3">
												<label for="company_name" class="form-label">Company Name:</label>
												<input type="text" class="form-control shadow-sm" id="company_name" name="company_name" placeholder="Enter Company Name" required>
											</div>
											<div class="col-md-3">
												<label for="customer_email" class="form-label">Email:</label>
												<input type="email" class="form-control shadow-sm" id="customer_email" name="customer_email" placeholder="Enter Email" required>
											</div>
										</div>

										<div class="form-row mb-3">
											
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
											<button type="submit" class="btn submitButton px-4 py-2" style="border-radius: 20px; font-weight: bold; background-color: #d10908; color: #fff;">
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
						<div class="card-header text-white" id="headingTwo" style="background-color: #d10908;">
							<h2 class="mb-0">
								<button class="btn btn-link text-white collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="font-weight: bold; text-decoration: none;">
								<i id="addProposalIcon" class="bi bi-plus-circle me-2"></i>Create a Proposal for a Deal
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
									<!--<div class="form-group">-->
									<!--	<label class="form-label" style="color: #000;" for="account-name">Account Name:</label>-->
									<!--	<input type="text" class="form-control" name="accountName" id="account-name" placeholder="Enter Account Name" required>-->
									<!--	<div class="error-message"></div>-->
									<!--</div>-->

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
													<th>Discount %</th>
													<th>Total (AED)</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody id="itemRows">
												<!-- Primary Row -->
												<tr class="item-row">
													<td style="min-width: 80px;">1</td>
													<td style="min-width: 350px;">
														<input type="hidden" class="product_id" name="product_id[]">
														<input type="hidden" class="product_name" name="product_name[]">
														<select class="form-control itemName" name="itemName[]" style="width: 100%;" required>
															<option value="">-None-</option>
														</select>
														<textarea name="itemDescription[]" class="itemDescription" cols="30" rows="3" style="width: 100%; border: 1px solid #ced4da;"></textarea>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 130px;">
														<select class="form-control uom" name="uom[]" required>
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
														<input type="number" name="quantity[]" class="form-control quantity" placeholder="Quantity" required>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 100px;">
														<input type="number" name="unitPrice[]" class="form-control unitPrice" placeholder="Unit Price" required>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 100px;">
														<input type="number" name="itemDiscount[]" class="form-control no-arrows itemDiscount" placeholder="Discount" required>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 100px;">
														<input type="number" name="total[]" class="form-control total" placeholder="Total" readonly>
														<div class="error-message"></div>
													</td>
													<td>
														<!-- No Remove button for the first row -->
													</td>
												</tr>
											</tbody>
										</table>
									</div>

									<!-- Totals Section -->
									<div class="row mt-4">
										<!-- Add Item Button -->
										<div class="text-start my-3">
											<button type="button" id="addItem" class="btn" style="background-color: #d10908; color: #fff;">+ Add Item</button>
										</div>
										<div class="col-md-5 offset-md-7">
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="sub-total" class="label-nowrap">Sub Total (AED)</label>
												<input type="number" name="subTotal" class="form-control no-arrows flex-input" id="sub-total" placeholder="0" readonly>
												<div class="error-message"></div>
											</div>
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="discount" class="label-nowrap">Discount (AED)</label>
												<input type="number" name="discount" class="form-control no-arrows flex-input" id="discount" placeholder="0">
												<div class="error-message"></div>
											</div>
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="vat" class="label-nowrap">VAT % (AED)</label>
												<input type="number" name="vat" class="form-control no-arrows flex-input" id="vat" value="5" placeholder="0" readonly>
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
										<label class="form-label" style="color: #000;" for="kind_attention">Kind Attention:</label>
										<input type="text" name="kind_attention" class="form-control" id="kind_attention" placeholder="Enter Kind Attention" required>
										<div class="error-message"></div>
									</div>
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
										<button type="submit" id="" class="btn submitButton px-4 py-2" style="border-radius: 20px; font-weight: bold; background-color: #d10908; color: #fff;">
											Save
											<span class="loader buttonLoader ms-2" style="display: none;"></span>
										</button>
									</div>
									
								</form>
							</div>
						</div>
					</div>
					
					<!-- Proposal Edit Section -->
					<div class="card shadow-sm pt-4">
						<div class="card-header text-white" id="headingTwo" style="background-color: #d10908;">
							<h2 class="mb-0">
								<button class="btn btn-link text-white collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="font-weight: bold; text-decoration: none;">
									<i id="editQuoteIcon" class="bi bi-plus-circle me-2"></i>Edit Quote
								</button>
							</h2>
						</div>

						<div id="collapseThree" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
							<div class="card-body">
								<form id="editQuoteForm">
									<div class="form-row">
										<div class="col-md-3 mb-3">
											<label class="form-label" style="color: #000;" for="deal-number">Quote Number:</label>
											<input type="text" class="form-control" name="QuoteNumber" id="QuoteNumber" placeholder="Enter Quote Number" required>
											<div class="error-message"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="subject">Subject:</label>
										<input type="text" class="form-control" name="subject" id="subject2" placeholder="Enter Subject" required>
										<div class="error-message"></div>
									</div>
									<!-- <div class="form-group">
										<label class="form-label" style="color: #000;" for="account-name">Account Name:</label>
										<input type="text" class="form-control" name="accountName" id="account-name" placeholder="Enter Account Name" required>
										<div class="error-message"></div>
									</div> -->

									<!-- Quoted Items Table -->
									<h6>Quoted Items</h6>
									<div class="table-responsive">
										<table class="table table-bordered" id="itemRows2">
											<thead>
												<tr>
													<th>S.NO</th>
													<th>Item Name</th>
													<th>U.O.M</th>
													<th>Quantity</th>
													<th>Unit Price (AED)</th>
													<th>Discount (%)</th>
													<th>Total (AED)</th>
													<th>Action</th>
												</tr>
											</thead>
											<tbody>
												<!-- Rows will be dynamically added here -->
											</tbody>
										</table>
									</div>

									<!-- Totals Section -->
									<div class="row mt-4">
										<!-- Add Item Button -->
										<div class="text-start my-3">
											<button type="button" id="addItem2" class="btn" style="background-color: #d10908; color: #fff;">+ Add Item</button>
										</div>
										<div class="col-md-5 offset-md-7">
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="sub-total" class="label-nowrap">Sub Total (AED)</label>
												<input type="number" name="subTotal" class="form-control no-arrows flex-input" id="sub-total2" placeholder="0" readonly>
												<div class="error-message"></div>
											</div>
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="discount" class="label-nowrap">Discount (AED)</label>
												<input type="number" name="discount" class="form-control no-arrows flex-input" id="discount2" placeholder="0">
												<div class="error-message"></div>
											</div>
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="vat" class="label-nowrap">VAT % (AED)</label>
												<input type="number" name="vat" class="form-control no-arrows flex-input" id="vat2" value="5" placeholder="0" readonly>
												<div class="error-message"></div>
											</div>
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="adjustment" class="label-nowrap">Adjustment (AED)</label>
												<input type="number" name="adjustment" class="form-control no-arrows flex-input" id="adjustment2" placeholder="0">
												<div class="error-message"></div>
											</div>
											<div class="form-group d-flex align-items-center justify-content-between">
												<label style="color: #000; font-weight: 600;" for="grand-total" class="label-nowrap">Grand Total (AED)</label>
												<input type="number" name="grandTotal" class="form-control no-arrows flex-input" id="grand-total2" placeholder="0" readonly>
												<div class="error-message"></div>
											</div>

										</div>
									</div>

									<!-- Additional Details -->
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="kind_attention">Kind Attention:</label>
										<input type="text" name="kind_attention" class="form-control" id="kind_attention2" placeholder="Enter Kind Attention" required>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="project">Project:</label>
										<input type="text" name="project" class="form-control" id="project2" placeholder="Enter Project Name" required>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="terms-of-payment">Terms of Payment:</label>
										<input type="text" name="termsOfPayment" class="form-control" id="terms-of-payment2" placeholder="Enter Payment Terms" required>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="specification">Specification:</label>
										<textarea class="form-control" name="specification" id="specification2" rows="3" placeholder="Enter Specifications" required></textarea>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="general-exclusion">General Exclusion:</label>
										<textarea class="form-control" name="generalExclusion" id="general-exclusion2" rows="3" placeholder="Enter General Exclusion" required></textarea>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="brand">Brand:</label>
										<input type="text" class="form-control" name="brand" id="brand2" placeholder="Enter brand" required>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="warranty">Warranty:</label>
										<input type="text" class="form-control" name="warranty" id="warranty2" placeholder="Enter Warranty" required>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="delivery">Delivery:</label>
										<input type="text" class="form-control" name="delivery" id="delivery2" placeholder="Enter Delivery" required>
										<div class="error-message"></div>
									</div>
									<div class="form-group">
										<label class="form-label" style="color: #000;" for="valid-until">Valid Until:</label>
										<input type="date" class="form-control" name="validUntil" id="valid-until2" placeholder="Enter Valid Until" required>
										<div class="error-message"></div>
									</div>

									<div class="text-end">
										<button type="submit" id="" class="btn submitButton px-4 py-2" style="border-radius: 20px; font-weight: bold; background-color: #d10908; color: #fff;">
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
	</div>

	<!-- Include jQuery and Select2 JavaScript -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Optional: Include Bootstrap's JavaScript (jQuery and Popper.js) for accordion functionality -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
	<script>

 		//add a row for each product and Initialize Select2 for each row
		$(document).ready(function () {
			let rowCount = 1; // Track the number of rows

			// Function to initialize Select2 for a row
			function initializeSelect2(row) {
				const productDetails = {}; // Store product descriptions for quick access
				let loading = false; // Prevent multiple concurrent requests
				let hasMoreRecords = true; // Indicator for more records
				let page = 1; // Current page for pagination
				let lastQuery = ''; // To track the last search query

				$(row).find('.itemName').select2({
					placeholder: 'Search for an item...',
					allowClear: true,
					width: '100%',
					ajax: {
						transport: function (params, success, failure) {
							const query = params.data.q || ''; // Capture the current search query

							// Reset pagination and clear results if the query changes
							if (query !== lastQuery) {
								page = 1; // Reset to the first page
								hasMoreRecords = true; // Reset the flag for more records
								lastQuery = query; // Update the last query
								success({ results: [] }); // Clear previous results in the dropdown
							}

							// Prevent multiple requests or if no more records
							if (loading || !hasMoreRecords) return;

							loading = true; // Mark as loading

							fetch(`https://app.voltronix.ae/voltronix/deal/products?page=${page}&per_page=20&q=${encodeURIComponent(query)}`)
								.then((response) => response.json())
								.then((data) => {
									const results = [];
									if (data.success && Array.isArray(data.products)) {
										data.products.forEach((product) => {
											productDetails[product.id] = product.description || ''; // Cache product descriptions
											results.push({ id: product.id, text: product.name }); // Format for Select2
										});

										hasMoreRecords = data.more_records; // Update the flag for more records
										page++; // Increment page for the next request

										success({ results, pagination: { more: hasMoreRecords } }); // Send results to Select2
									} else {
										hasMoreRecords = false; // No more records
										success({ results: [], pagination: { more: false } });
									}
								})
								.catch((error) => {
									console.error('Error fetching products:', error);
									failure(error);
								})
								.finally(() => {
									loading = false; // Reset loading state
								});
						},
						processResults: function (data) {
							return data; // The transport already formats the response correctly
						},
						delay: 250, // Add delay to prevent excessive requests
					},
					templateResult: formatOption,
					templateSelection: formatOption,
				});

				// Handle description display when a product is selected
				$(row).find('.itemName').on('change', function () {
					const selectedItemId = $(this).val();
					const descriptionField = $(row).find('.itemDescription');

					// Clear the description if no item is selected
					if (!selectedItemId) {
						descriptionField.val('');
						return;
					}

					// Fetch the product description from the stored details
					const selectedDescription = productDetails[selectedItemId];
					descriptionField.val(selectedDescription || '');
				});
			}

			// Format long text in dropdown and selection
			function formatOption(option) {
				if (!option.id) return option.text; // Show placeholder
				return $('<span class="wrap-text"></span>').text(option.text); // Wrap long text
			}

			// Function to update row numbers
			function updateRowNumbers() {
				$('#itemRows tr').each(function (index) {
					$(this).find('td:first').text(index + 1); // Update the S.NO column
				});
			}

			// Add new row on button click
			$('#addItem').on('click', function () {
				rowCount++;
				const newRow = `
					<tr class="item-row">
						<td>${$('#itemRows tr').length + 1}</td>
						<td>
							<input type="hidden" class="product_id" name="product_id[]">
							<input type="hidden" class="product_name" name="product_name[]">
							<select class="form-control itemName" name="itemName[]" style="width: 100%;" required>
								<option value="">-None-</option>
							</select>
							<textarea name="itemDescription[]" class="itemDescription" cols="30" rows="3" style="width: 100%; border: 1px solid #ced4da;"></textarea>
							<div class="error-message"></div>
						</td>
						<td>
							<select class="form-control uom" name="uom[]" required>
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
						<td>
							<input type="number" name="quantity[]" class="form-control quantity" placeholder="Quantity" required>
							<div class="error-message"></div>
						</td>
						<td>
							<input type="number" name="unitPrice[]" class="form-control unitPrice" placeholder="Unit Price" required>
							<div class="error-message"></div>
						</td>
						<td style="min-width: 100px;">
							<input type="number" name="itemDiscount[]" class="form-control no-arrows itemDiscount" placeholder="Discount" required>
							<div class="error-message"></div>
						</td>
						<td>
							<input type="number" name="total[]" class="form-control total" placeholder="Total" readonly>
							<div class="error-message"></div>
						</td>
						<td>
							<button type="button" class="btn btn-danger btn-sm remove-row">
								<i class="bi bi-trash"></i> <!-- Bootstrap trash icon -->
							</button>
						</td>
					</tr>
				`;
				$('#itemRows').append(newRow);

				// Initialize Select2 for the new row
				initializeSelect2($('#itemRows tr:last'));

				// Update row numbers
				updateRowNumbers();
			});

			 // Remove row when the "Remove" button is clicked
			 $('#itemRows').on('click', '.remove-row', function () {
				$(this).closest('tr').remove(); // Remove the closest row
				updateRowNumbers(); // Update the row numbers
			});

			// Initialize Select2 for the primary row
			initializeSelect2($('#itemRows tr:first'));
		});

		// Separate calculateTotals function
		function calculateTotals() {
			let subTotal = 0;

			// Loop through each row and calculate totals with discount
			$('.item-row').each(function () {
				const quantity = parseFloat($(this).find('.quantity').val()) || 0;
				const unitPrice = parseFloat($(this).find('.unitPrice').val()) || 0;
				const discountPercentage = parseFloat($(this).find('.itemDiscount').val()) || 0;

				const baseTotal = quantity * unitPrice;
				const discountFactor = 1 - (discountPercentage / 100);
				const discountedTotal = baseTotal * discountFactor;

				$(this).find('.total').val(discountedTotal.toFixed(2));
				subTotal += discountedTotal;
			});

			$('#sub-total').val(subTotal.toFixed(2));

			// Calculate overall totals with safeguards
			const discountAmount = parseFloat($('#discount').val()) || 0; // Overall discount (AED)
			const subTotalAfterDiscount = Math.max(0, subTotal - discountAmount); // Prevent negative
			const vatPercentage = 5;
			const vat = (vatPercentage / 100) * subTotalAfterDiscount;
			const adjustment = parseFloat($('#adjustment').val()) || 0;
			const grandTotal = subTotalAfterDiscount + vat + adjustment;

			// Debug output
			console.log({
				subTotal: subTotal.toFixed(2),
				discountAmount: discountAmount.toFixed(2),
				subTotalAfterDiscount: subTotalAfterDiscount.toFixed(2),
				vat: vat.toFixed(2),
				adjustment: adjustment.toFixed(2),
				grandTotal: grandTotal.toFixed(2)
			});

			$('#grand-total').val(grandTotal.toFixed(2));
		}

		// Attach event listeners to recalculate totals
		$('#itemRows').on('input', '.quantity, .unitPrice, .itemDiscount', calculateTotals);
		$('#discount, #adjustment').on('input', calculateTotals);

	    // toggle icons
		$(document).ready(function () {
			// Initialize icons based on the initial state of collapses
			if ($("#collapseOne").hasClass("show")) {
				$("#addDealIcon").removeClass("bi-plus-circle").addClass("bi-dash-circle");
			} else {
				$("#addDealIcon").removeClass("bi-dash-circle").addClass("bi-plus-circle");
			}

			// Collapse Three
			$("#collapseThree").on("shown.bs.collapse", function () {
				$("#editQuoteIcon").removeClass("bi-plus-circle").addClass("bi-dash-circle");
			});

			$("#collapseThree").on("hidden.bs.collapse", function () {
				$("#editQuoteIcon").removeClass("bi-dash-circle").addClass("bi-plus-circle");
			});

			// Collapse Two
			$("#collapseTwo").on("shown.bs.collapse", function () {
				$("#addProposalIcon").removeClass("bi-plus-circle").addClass("bi-dash-circle");
			});

			$("#collapseTwo").on("hidden.bs.collapse", function () {
				$("#addProposalIcon").removeClass("bi-dash-circle").addClass("bi-plus-circle");
			});

			// Collapse One
			$("#collapseOne").on("shown.bs.collapse", function () {
				$("#addDealIcon").removeClass("bi-plus-circle").addClass("bi-dash-circle");
			});

			$("#collapseOne").on("hidden.bs.collapse", function () {
				$("#addDealIcon").removeClass("bi-dash-circle").addClass("bi-plus-circle");
			});
		});

		// // Calculate and update totals of item2
		// $(document).ready(function() {
		// 	function calculateTotals() {
		// 		let quantity = parseFloat($('#quantity2').val()) || 0;
		// 		let unitPrice = parseFloat($('#unitPrice2').val()) || 0;
		// 		let total = quantity * unitPrice;
		// 		$('#total2').val(total.toFixed(2));

		// 		// Calculate Sub Total (only one item row is present in this example)
		// 		let subTotal = total;
		// 		$('#sub-total2').val(subTotal.toFixed(2));

		// 		// Get the fixed discount amount (instead of percentage)
		// 		let discountAmount = parseFloat($('#discount2').val()) || 0;

		// 		// Subtract discount from Sub Total before VAT calculation
        //         let subTotalAfterDiscount = subTotal - discountAmount;
        
        //         // VAT Calculation (Fixed VAT percentage of 5%)
        //         let vatPercentage = 5; // Fixed VAT percentage
        //         let vat = (vatPercentage / 100) * subTotalAfterDiscount;

		// 		// Get other values for Grand Total calculation
		// 		let adjustment = parseFloat($('#adjustment2').val()) || 0;

		// 		// Grand Total Calculation: Sub Total - Discount + VAT + Adjustment
		// 		let grandTotal = subTotal - discountAmount + vat + adjustment;
		// 		$('#grand-total2').val(grandTotal.toFixed(2));
		// 	}

		// 	// Attach event listeners to calculate totals when Quantity or Unit Price changes
		// 	$('#quantity2, #unitPrice2').on('input', calculateTotals);

		// 	// Attach event listeners for the discount and adjustment fields to update the Grand Total
		// 	$('#discount2, #adjustment2').on('input', calculateTotals);

		// 	// Initial calculation to ensure totals are correct on page load
		// 	calculateTotals();
		// });

		// Set today's date as the minimum date
		const dateInput = document.getElementById('valid-until');
		const today = new Date().toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
		dateInput.setAttribute('min', today);
		
		// Set today's date as the minimum date
		const dateInput2 = document.getElementById('valid-until2');
		const today2 = new Date().toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
		dateInput2.setAttribute('min', today2);

		// Fetch item names2 from the API and quote details for editing a quote
		$(document).ready(function () {
			const productDetails = {};
			let loading = false;
			let hasMoreRecords = true;
			let page = 1;
			let lastQuery = '';

			// Function to initialize Select2 for a row
			function initializeSelect2(row) {
				$(row).find('.itemName').select2({
					placeholder: 'Search for an item...',
					allowClear: true,
					width: '100%',
					ajax: {
						transport: function (params, success, failure) {
							const query = params.data.q || '';
							if (query !== lastQuery) {
								page = 1;
								hasMoreRecords = true;
								lastQuery = query;
								success({ results: [] });
							}
							if (loading || !hasMoreRecords) return;
							loading = true;
							fetch(`https://app.voltronix.ae/voltronix/deal/products?page=${page}&per_page=20&q=${encodeURIComponent(query)}`)
								.then((response) => response.json())
								.then((data) => {
									const results = [];
									if (data.success && Array.isArray(data.products)) {
										data.products.forEach((product) => {
											productDetails[product.id] = product.description || '';
											results.push({ id: product.id, text: product.name });
										});
										hasMoreRecords = data.more_records;
										page++;
										success({ results, pagination: { more: hasMoreRecords } });
									} else {
										hasMoreRecords = false;
										success({ results: [], pagination: { more: false } });
									}
								})
								.catch((error) => {
									console.error('Error fetching products:', error);
									failure(error);
								})
								.finally(() => {
									loading = false;
								});
						},
						processResults: function (data) {
							return data;
						},
						delay: 250,
					},
				});

				$(row).find('.itemName').on('change', function () {
					const selectedItemId = $(this).val();
					const descriptionField = $(row).find('.itemDescription');
					if (!selectedItemId) {
						descriptionField.val('');
						return;
					}
					const selectedDescription = productDetails[selectedItemId];
					descriptionField.val(selectedDescription || '');
					row.find('.product_id').val(selectedItemId);
					row.find('.product_name').val($(this).find('option:selected').text());
				});
			}

			// Function to add a new row with pre-filled data
			function addItemRow(itemData = {}, includeDeleteButton = true) {
				const rowCount = $('#itemRows2 tbody tr').length + 1;
				const newRow = `
					<tr class="item-row">
						<td>${rowCount}</td>
						<td style="min-width: 300px;">
							<input type="hidden" class="product_id" name="product_id[]" value="${itemData.product_id || ''}">
							<input type="hidden" class="product_name" name="product_name[]" value="${itemData.product_name || ''}">
							<select class="form-control itemName" name="itemName[]" style="width: 100%;" required>
								<option value="">-None-</option>
							</select>
							<textarea name="itemDescription[]" class="itemDescription" cols="30" rows="3" style="width: 100%; border: 1px solid #ced4da;">${itemData.product_description || ''}</textarea>
							<div class="error-message"></div>
						</td>
						<td style="min-width: 130px;">
							<select class="form-control uom" name="uom[]" required>
								<option value="">-None-</option>
								<option value="NOS" ${itemData.uom === 'NOS' ? 'selected' : ''}>NOS</option>
								<option value="PCS" ${itemData.uom === 'PCS' ? 'selected' : ''}>PCS</option>
								<option value="LS" ${itemData.uom === 'LS' ? 'selected' : ''}>LS</option>
								<option value="BAG" ${itemData.uom === 'BAG' ? 'selected' : ''}>BAG</option>
								<option value="BKT" ${itemData.uom === 'BKT' ? 'selected' : ''}>BKT</option>
								<option value="BND" ${itemData.uom === 'BND' ? 'selected' : ''}>BND</option>
								<option value="BOWL" ${itemData.uom === 'BOWL' ? 'selected' : ''}>BOWL</option>
								<option value="BX" ${itemData.uom === 'BX' ? 'selected' : ''}>BX</option>
								<option value="CRD" ${itemData.uom === 'CRD' ? 'selected' : ''}>CRD</option>
								<option value="CM" ${itemData.uom === 'CM' ? 'selected' : ''}>CM</option>
								<option value="CS" ${itemData.uom === 'CS' ? 'selected' : ''}>CS</option>
								<option value="CTN" ${itemData.uom === 'CTN' ? 'selected' : ''}>CTN</option>
								<option value="DZ" ${itemData.uom === 'DZ' ? 'selected' : ''}>DZ</option>
								<option value="EA" ${itemData.uom === 'EA' ? 'selected' : ''}>EA</option>
								<option value="FT" ${itemData.uom === 'FT' ? 'selected' : ''}>FT</option>
								<option value="GAL" ${itemData.uom === 'GAL' ? 'selected' : ''}>GAL</option>
								<option value="GROSS" ${itemData.uom === 'GROSS' ? 'selected' : ''}>GROSS</option>
								<option value="IN" ${itemData.uom === 'IN' ? 'selected' : ''}>IN</option>
								<option value="KIT" ${itemData.uom === 'KIT' ? 'selected' : ''}>KIT</option>
								<option value="LOT" ${itemData.uom === 'LOT' ? 'selected' : ''}>LOT</option>
								<option value="M" ${itemData.uom === 'M' ? 'selected' : ''}>M</option>
								<option value="MM" ${itemData.uom === 'MM' ? 'selected' : ''}>MM</option>
								<option value="PC" ${itemData.uom === 'PC' ? 'selected' : ''}>PC</option>
								<option value="PK" ${itemData.uom === 'PK' ? 'selected' : ''}>PK</option>
								<option value="PK100" ${itemData.uom === 'PK100' ? 'selected' : ''}>PK100</option>
								<option value="PK50" ${itemData.uom === 'PK50' ? 'selected' : ''}>PK50</option>
								<option value="PR" ${itemData.uom === 'PR' ? 'selected' : ''}>PR</option>
								<option value="RACK" ${itemData.uom === 'RACK' ? 'selected' : ''}>RACK</option>
								<option value="RL" ${itemData.uom === 'RL' ? 'selected' : ''}>RL</option>
								<option value="SET" ${itemData.uom === 'SET' ? 'selected' : ''}>SET</option>
								<option value="SET3" ${itemData.uom === 'SET3' ? 'selected' : ''}>SET3</option>
								<option value="SET4" ${itemData.uom === 'SET4' ? 'selected' : ''}>SET4</option>
								<option value="SET5" ${itemData.uom === 'SET5' ? 'selected' : ''}>SET5</option>
								<option value="SGL" ${itemData.uom === 'SGL' ? 'selected' : ''}>SGL</option>
								<option value="SHT" ${itemData.uom === 'SHT' ? 'selected' : ''}>SHT</option>
								<option value="SQFT" ${itemData.uom === 'SQFT' ? 'selected' : ''}>SQFT</option>
								<option value="TUBE" ${itemData.uom === 'TUBE' ? 'selected' : ''}>TUBE</option>
								<option value="YD" ${itemData.uom === 'YD' ? 'selected' : ''}>YD</option>
								<option value="SQM" ${itemData.uom === 'SQM' ? 'selected' : ''}>SQM</option>
							</select>
							<div class="error-message"></div>
						</td>
						<td style="min-width: 100px;">
							<input type="number" name="quantity[]" class="form-control no-arrows quantity" placeholder="Quantity" value="${itemData.quantity || ''}" required>
							<div class="error-message"></div>
						</td>
						<td style="min-width: 100px;">
							<input type="number" name="unitPrice[]" class="form-control no-arrows unitPrice" placeholder="Unit Price" value="${itemData.service_charge || ''}" required>
							<div class="error-message"></div>
						</td>
						<td style="min-width: 100px;">
							<input type="number" name="itemDiscount[]" class="form-control no-arrows itemDiscount" placeholder="Discount" value="${itemData.item_discount || '0'}" required>
							<div class="error-message"></div>
						</td>
						<td style="min-width: 100px;">
							<input type="number" name="total[]" class="form-control total" placeholder="Total" value="${itemData.total || ''}" readonly>
							<div class="error-message"></div>
						</td>
						<td>
							${includeDeleteButton ? `
								<button type="button" class="btn btn-danger btn-sm remove-row">
									<i class="bi bi-trash"></i>
								</button>
							` : ''}
						</td>
					</tr>
				`;
				$('#itemRows2 tbody').append(newRow);
				initializeSelect2($('#itemRows2 tbody tr:last'));

				// Pre-select item in Select2
				if (itemData.product_id && itemData.product_name) {
					const $select = $('#itemRows2 tbody tr:last .itemName');
					$select.append(new Option(itemData.product_name, itemData.product_id, true, true)).trigger('change');
				}
			}

			// Fetch quote details on Enter key press
			$('#QuoteNumber').on('keypress', function (e) {
				if (e.which === 13) { // Enter key
					e.preventDefault();
					const quoteNumber = $(this).val();
					if (quoteNumber.trim() === '') return;

					$.ajax({
						url: '<?php echo site_url('web/dashboard/get_quote_details'); ?>',
						type: 'POST',
						dataType: 'json',
						contentType: 'application/json',
						data: JSON.stringify({ QuoteNumber: quoteNumber }),
						success: function (response) {
							if (response.success) {
								const data = response.data;
								const items = response.items || [];

								// Populate static fields
								$('#subject2').val(data.subject || '');
								$('#kind_attention2').val(data.kind_attention || '');
								$('#project2').val(data.project_name || '');
								$('#specification2').val(data.specification || '');
								$('#general-exclusion2').val(data.general_exclusion || '');
								$('#brand2').val(data.brand || '');
								$('#warranty2').val(data.warranty || '');
								$('#delivery2').val(data.delivery || '');
								$('#terms-of-payment2').val(data.terms_of_payment || '');
								$('#valid-until2').val(data.valid_until || '');
								$('#sub-total2').val(data.sub_total || '');
								$('#discount2').val(data.discount || '');
								$('#adjustment2').val(data.adjustment || '');
								$('#grand-total2').val(data.grand_total || '');

								// Clear existing rows
								$('#itemRows2 tbody').empty();

								// Populate item rows
								if (items.length > 0) {
									items.forEach((item, index) => {
										// First row (index 0) should not have a delete button
										const includeDeleteButton = index !== 0;
										addItemRow(item, includeDeleteButton);
									});
								} else {
									addItemRow(); // Add a blank row if no items, no delete button by default
								}

								calculateTotals();
							} else {
								console.error('API Error:', response.error || 'Failed to fetch quote details.');
								Swal.fire({
									icon: 'error',
									title: 'Error',
									text: response.error || 'Failed to fetch quote details.',
								});
							}
						},
						error: function () {
							console.error('AJAX Error: Error fetching quote details.');
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: 'An unexpected error occurred while fetching quote details.',
							});
						}
					});
				}
			});

			// Add new row on button click
			$('#addItem2').on('click', function () {
				addItemRow(); // Default behavior includes delete button for manually added rows
			});

			// Remove row
			$('#itemRows2').on('click', '.remove-row', function () {
				$(this).closest('tr').remove();
				updateRowNumbers();
				calculateTotals();
			});

			// Update row numbers
			function updateRowNumbers() {
				$('#itemRows2 tbody tr').each(function (index) {
					$(this).find('td:first').text(index + 1);
				});
			}

			// Calculate totals
			function calculateTotals() {
				let subTotal = 0;
				$('.item-row').each(function () {
					const quantity = parseFloat($(this).find('.quantity').val()) || 0;
					const unitPrice = parseFloat($(this).find('.unitPrice').val()) || 0;
					const discountPercentage = parseFloat($(this).find('.itemDiscount').val()) || 0;
					const baseTotal = quantity * unitPrice;
					const discountFactor = 1 - (discountPercentage / 100);
					const discountedTotal = baseTotal * discountFactor;
					$(this).find('.total').val(discountedTotal.toFixed(2));
					subTotal += discountedTotal;
				});
				$('#sub-total2').val(subTotal.toFixed(2));
				const discountAmount = parseFloat($('#discount2').val()) || 0;
				const subTotalAfterDiscount = Math.max(0, subTotal - discountAmount);
				const vatPercentage = 5;
				const vat = (vatPercentage / 100) * subTotalAfterDiscount;
				const adjustment = parseFloat($('#adjustment2').val()) || 0;
				const grandTotal = subTotalAfterDiscount + vat + adjustment;
				$('#grand-total2').val(grandTotal.toFixed(2));
			}

			// Handle form submission
			$('#editQuoteForm').submit(function (e) {
				e.preventDefault();
				$('.error-message').empty();

				const formData = {
					QuoteNumber: $('#QuoteNumber').val(),
					subject: $('#subject2').val(),
					kind_attention: $('#kind_attention2').val(),
					project: $('#project2').val(),
					termsOfPayment: $('#terms-of-payment2').val(),
					specification: $('#specification2').val(),
					generalExclusion: $('#general-exclusion2').val(),
					brand: $('#brand2').val(),
					warranty: $('#warranty2').val(),
					delivery: $('#delivery2').val(),
					validUntil: $('#valid-until2').val(),
					subTotal: $('#sub-total2').val(),
					discount: $('#discount2').val(),
					adjustment: $('#adjustment2').val(),
					grandTotal: $('#grand-total2').val(),
					items: []
				};

				let hasErrors = false;
				$('#itemRows2 tbody tr').each(function () {
					const item = {
						product_id: $(this).find('.product_id').val(),
						product_name: $(this).find('.product_name').val(),
						itemName: $(this).find('.itemName').val(),
						itemDescription: $(this).find('.itemDescription').val(),
						uom: $(this).find('.uom').val(),
						quantity: $(this).find('.quantity').val(),
						itemDiscount: $(this).find('.itemDiscount').val(),
						unitPrice: $(this).find('.unitPrice').val(),
						total: $(this).find('.total').val()
					};
					if (!item.itemName) {
						$(this).find('.error-message').text('Item Name is required.');
						hasErrors = true;
						return false; // Break the .each loop
					}
					formData.items.push(item);
				});

				if (hasErrors) {
					return; // Stop submission if there are validation errors
				}

				// Show SweetAlert loading spinner
				Swal.fire({
					title: 'Submitting...',
					text: 'Please wait while your proposal is being updated.',
					allowOutsideClick: false,
					allowEscapeKey: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				$.ajax({
					url: 'dashboard/edit_proposal',
					type: 'POST',
					data: JSON.stringify(formData),
					contentType: 'application/json',
					dataType: 'json',
					success: function (response) {
						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Proposal Edited!',
								html: `
									<p>${response.message}</p>
									<a href="<?= site_url('web/deal/download-quote/') ?>${response.id}"
									class="btn btn-success btn-sm" 
									title="Download Quote" target="_blank" 
									rel="noopener noreferrer">
										<i class="bi bi-download"></i> Download Quote
									</a>
								`,
								showConfirmButton: true
							}).then(() => {
								$('#editQuoteForm')[0].reset();
								$('#itemRows2 tbody').empty();
								addItemRow(); // Add a blank row without delete button
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: response.error || 'Failed to edit proposal.',
								showConfirmButton: true
							});
						}
					},
					error: function (xhr) {
						const errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred.';
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,
							showConfirmButton: true
						});
					}
				});
			});

			// Recalculate totals on input change
			$('#itemRows2').on('input', '.quantity, .unitPrice, .itemDiscount, #discount2, #adjustment2', function () {
				calculateTotals();
			});

			// Initial blank row (no delete button by default)
			addItemRow({}, false);
		});

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

		$(document).ready(function() {
		    // Handle product selection and store values in hidden fields
			// $('#itemName').on('change', function () {
			// 	const productId = $(this).val(); // Get product_id from value
			// 	const productName = $(this).find('option:selected').text(); // Get product_name from text

			// 	// Store these values in hidden fields
			// 	$('#product_id').val(productId);
			// 	$('#product_name').val(productName);
			// });

			// // Handle product selection and store values in hidden fields
			// $('#itemName2').on('change', function () {
			// 	const productId = $(this).val(); // Get product_id from value
			// 	const productName = $(this).find('option:selected').text(); // Get product_name from text

			// 	// Store these values in hidden fields
			// 	$('#product_id2').val(productId);
			// 	$('#product_name2').val(productName);
			// });
			
			// Handle product selection and store values in hidden fields for all rows
			$(document).on('change', '.itemName', function () {
				const row = $(this).closest('tr'); // Get the closest row
				const productId = $(this).val(); // Get product_id from value
				const productName = $(this).find('option:selected').text(); // Get product_name from text

				// Store these values in hidden fields within the same row
				row.find('.product_id').val(productId);
				row.find('.product_name').val(productName);
			});

			$('#reProposalForm').submit(function (e) {
				e.preventDefault();

				// Reset any previous error messages
				$('.error-message').empty();

				// Collect form data
				const formData = {
					dealNumber: $('#deal-number').val(),
					subject: $('#subject').val(),
					kind_attention: $('#kind_attention').val(),
					project: $('#project').val(),
					termsOfPayment: $('#terms-of-payment').val(),
					specification: $('#specification').val(),
					generalExclusion: $('#general-exclusion').val(),
					brand: $('#brand').val(),
					warranty: $('#warranty').val(),
					delivery: $('#delivery').val(),
					validUntil: $('#valid-until').val(),
					subTotal: $('#sub-total').val(),
					discount: $('#discount').val(),
					adjustment: $('#adjustment').val(),
					grandTotal: $('#grand-total').val(),
					items: []
				};

				// Collect data for each item row and validate
				let hasErrors = false;
				$('#itemRows tr').each(function () {
					const item = {
						product_id: $(this).find('.product_id').val(),
						product_name: $(this).find('.product_name').val(),
						itemName: $(this).find('.itemName').val(),
						itemDescription: $(this).find('.itemDescription').val(),
						uom: $(this).find('.uom').val(),
						quantity: $(this).find('.quantity').val(),
						itemDiscount: $(this).find('.itemDiscount').val(),
						unitPrice: $(this).find('.unitPrice').val(),
						total: $(this).find('.total').val()
					};

					// Validate itemName before adding to formData
					if (!item.itemName) {
						$(this).find('.error-message').text('Item Name is required.');
						hasErrors = true;
						return false; // Stop the .each loop
					}

					formData.items.push(item);
				});

				if (hasErrors) {
					return; // Stop submission if there are validation errors
				}

				// Show SweetAlert loading spinner
				Swal.fire({
					title: 'Creating Proposal...',
					text: 'Please wait while your proposal is being created.',
					allowOutsideClick: false,
					allowEscapeKey: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				// AJAX request for proposal creation
				$.ajax({
					url: 'dashboard/add_proposal',
					type: 'POST',
					data: JSON.stringify(formData),
					contentType: 'application/json',
					dataType: 'json',
					success: function (response) {
						if (response.success) {
							Swal.fire({
								icon: 'success',
								title: 'Proposal Created!',
								html: `
									<p>${response.message}</p>
									<a href="<?= site_url('web/deal/download-quote/') ?>${response.id}"
									class="btn btn-success btn-sm" 
									title="Download Quote" target="_blank" 
									rel="noopener noreferrer">
										<i class="bi bi-download"></i> Download Quote
									</a>
								`,
								showConfirmButton: true
							}).then(() => {
								// Reset the form after showing success message
								$('#reProposalForm')[0].reset();

								// Clear all dynamically added rows except the first one
								$('#itemRows tr').not(':first').remove();

								// Reset the row count to 1
								$('#itemRows tr:first td:first').text('1');

								// Reinitialize Select2 on the remaining row if needed
								const firstRow = $('#itemRows tr:first');
								firstRow.find('.itemName').val('').trigger('change'); // Clear selection
								firstRow.find('.itemDescription').val('');
								firstRow.find('.uom').val('');
								firstRow.find('.quantity').val('');
								firstRow.find('.itemDiscount').val('0');
								firstRow.find('.unitPrice').val('');
								firstRow.find('.total').val('');
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: response.error || 'Failed to create proposal.',
								showConfirmButton: true
							});
						}
					},
					error: function (xhr) {
						const errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,
							showConfirmButton: true
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

				// Show SweetAlert loading spinner
				Swal.fire({
					title: 'Creating Deal...',
					text: 'Please wait while your deal is being created.',
					allowOutsideClick: false,
					allowEscapeKey: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				// AJAX request to create the deal
				$.ajax({
					url: '<?php echo site_url('deals/create_lead_in_zoho'); ?>',
					type: 'POST',
					dataType: 'json',
					contentType: 'application/json',
					data: JSON.stringify(formData),
					success: function(response) {
						if (response.success) {
							const dealId = response.deal_id;

							// Fetch the DealNumber
							$.ajax({
								url: '<?php echo site_url('web/dashboard/get_deal_number'); ?>',
								type: 'POST',
								dataType: 'json',
								data: JSON.stringify({ deal_id: dealId }),
								contentType: 'application/json',
								success: function(dealResponse) {
									if (dealResponse.success) {
										const dealNumber = dealResponse.DealNumber;
										Swal.fire({
											title: 'Deal Created Successfully!',
											html: `
												<p>Deal Number: <strong id="dealNumber">${dealNumber}</strong></p>
												<button onclick="copyDealNumber()" class="btn btn-primary">Copy Deal Number</button>
											`,
											icon: 'success',
											showConfirmButton: true
										}).then(() => {
											// Reset the form after success
											$('#DealForm')[0].reset();
										});
									} else {
										Swal.fire({
											icon: 'warning',
											title: 'Warning',
											text: 'Deal created, but failed to fetch Deal Number.',
											showConfirmButton: true
										});
									}
								},
								error: function() {
									Swal.fire({
										icon: 'error',
										title: 'Error',
										text: 'Unable to retrieve Deal Number.',
										showConfirmButton: true
									});
								}
							});
						} else if (response.errors) {
							let errorMessages = '';
							$.each(response.errors, function(key, value) {
								errorMessages += `${value}<br>`;
							});
							Swal.fire({
								icon: 'warning',
								title: 'Validation Errors',
								html: errorMessages,
								showConfirmButton: true
							});
						} else {
							Swal.fire({
								icon: 'error',
								title: 'Error',
								text: response.error || 'An error occurred.',
								showConfirmButton: true
							});
						}
					},
					error: function(xhr) {
						const errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,
							showConfirmButton: true
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
