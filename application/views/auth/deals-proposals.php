 
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
													<td style="min-width: 300px;">
													    <input type="hidden" id="product_id2" name="product_id">
														<input type="hidden" id="product_name2" name="product_name">
														<select class="form-control" id="itemName2" name="itemName2" style="width: 100%;" required>
															<option value="">-None-</option>
														</select>
														<textarea name="itemDescription" id="itemDescription2" cols="30" rows="3" style="width: 100%; border: 1px solid #ced4da;"></textarea>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 130px;">
														<select class="form-control" name="uom" id="uom2" required>
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
														<input type="number" name="quantity" class="form-control no-arrows" id="quantity2" placeholder="Quantity" required>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 100px;">
														<input type="number" name="unitPrice" class="form-control no-arrows" id="unitPrice2" placeholder="Unit Price" required>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 100px;">
														<input type="number" name="itemDiscount" class="form-control no-arrows" id="itemDiscount2" placeholder="Discount" required>
														<div class="error-message"></div>
													</td>
													<td style="min-width: 100px;">
														<input type="number" name="total" class="form-control" id="total2" placeholder="Total" readonly>
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
	
		// Calculate and update totals of item1
		// $(document).ready(function() {
		// 	function calculateTotals() {
		// 		let quantity = parseFloat($('#quantity').val()) || 0;
		// 		let unitPrice = parseFloat($('#unitPrice').val()) || 0;
		// 		let total = quantity * unitPrice;
		// 		$('#total').val(total.toFixed(2));

		// 		// Calculate Sub Total (only one item row is present in this example)
		// 		let subTotal = total;
		// 		$('#sub-total').val(subTotal.toFixed(2));

		// 		// Get the fixed discount amount (instead of percentage)
		// 		let discountAmount = parseFloat($('#discount').val()) || 0;

		// 		// Subtract discount from Sub Total before VAT calculation
        //         let subTotalAfterDiscount = subTotal - discountAmount;
        
        //         // VAT Calculation (Fixed VAT percentage of 5%)
        //         let vatPercentage = 5; // Fixed VAT percentage
        //         let vat = (vatPercentage / 100) * subTotalAfterDiscount;

		// 		// Get other values for Grand Total calculation
		// 		let adjustment = parseFloat($('#adjustment').val()) || 0;

		// 		// Grand Total Calculation: Sub Total - Discount + VAT + Adjustment
		// 		let grandTotal = subTotal - discountAmount + vat + adjustment;
		// 		$('#grand-total').val(grandTotal.toFixed(2));
		// 	}

		// 	// Attach event listeners to calculate totals when Quantity or Unit Price changes
		// 	$('#quantity, #unitPrice').on('input', calculateTotals);

		// 	// Attach event listeners for the discount and adjustment fields to update the Grand Total
		// 	$('#discount, #adjustment').on('input', calculateTotals);

		// 	// Initial calculation to ensure totals are correct on page load
		// 	calculateTotals();
		// });

		// Calculate and update totals of item2
		$(document).ready(function() {
			function calculateTotals() {
				let quantity = parseFloat($('#quantity2').val()) || 0;
				let unitPrice = parseFloat($('#unitPrice2').val()) || 0;
				let total = quantity * unitPrice;
				$('#total2').val(total.toFixed(2));

				// Calculate Sub Total (only one item row is present in this example)
				let subTotal = total;
				$('#sub-total2').val(subTotal.toFixed(2));

				// Get the fixed discount amount (instead of percentage)
				let discountAmount = parseFloat($('#discount2').val()) || 0;

				// Subtract discount from Sub Total before VAT calculation
                let subTotalAfterDiscount = subTotal - discountAmount;
        
                // VAT Calculation (Fixed VAT percentage of 5%)
                let vatPercentage = 5; // Fixed VAT percentage
                let vat = (vatPercentage / 100) * subTotalAfterDiscount;

				// Get other values for Grand Total calculation
				let adjustment = parseFloat($('#adjustment2').val()) || 0;

				// Grand Total Calculation: Sub Total - Discount + VAT + Adjustment
				let grandTotal = subTotal - discountAmount + vat + adjustment;
				$('#grand-total2').val(grandTotal.toFixed(2));
			}

			// Attach event listeners to calculate totals when Quantity or Unit Price changes
			$('#quantity2, #unitPrice2').on('input', calculateTotals);

			// Attach event listeners for the discount and adjustment fields to update the Grand Total
			$('#discount2, #adjustment2').on('input', calculateTotals);

			// Initial calculation to ensure totals are correct on page load
			calculateTotals();
		});

		// Set today's date as the minimum date
		const dateInput = document.getElementById('valid-until');
		const today = new Date().toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
		dateInput.setAttribute('min', today);
		
		// Set today's date as the minimum date
		const dateInput2 = document.getElementById('valid-until2');
		const today2 = new Date().toISOString().split('T')[0]; // Format the date as YYYY-MM-DD
		dateInput2.setAttribute('min', today2);

		// Fetch item names1 from the API
		// $(document).ready(function () {
        //     const productDetails = {}; // Store product descriptions for quick access
        //     let loading = false; // Prevent multiple concurrent requests
        //     let hasMoreRecords = true; // Indicator for more records
        //     let page = 1; // Current page for pagination
        //     let lastQuery = ''; // To track the last search query
        
        //     // Initialize Select2 with placeholder, infinite scrolling, and custom templates
        //     $('#itemName').select2({
        //         placeholder: 'Search for an item...',
        //         allowClear: true,
        //         width: '100%',
        //         ajax: {
        //             transport: function (params, success, failure) {
        //                 const query = params.data.q || ''; // Capture the current search query
        
        //                 // Reset pagination and clear results if the query changes
        //                 if (query !== lastQuery) {
        //                     page = 1; // Reset to the first page
        //                     hasMoreRecords = true; // Reset the flag for more records
        //                     lastQuery = query; // Update the last query
        //                     success({ results: [] }); // Clear previous results in the dropdown
        //                 }
        
        //                 // Prevent multiple requests or if no more records
        //                 if (loading || !hasMoreRecords) return;
        
        //                 loading = true; // Mark as loading
        
        //                 fetch(`https://app.voltronix.ae/voltronix/deal/products?page=${page}&per_page=20&q=${encodeURIComponent(query)}`)
        //                     .then((response) => response.json())
        //                     .then((data) => {
        //                         const results = [];
        //                         if (data.success && Array.isArray(data.products)) {
        //                             data.products.forEach((product) => {
        //                                 productDetails[product.id] = product.description || ''; // Cache product descriptions
        //                                 results.push({ id: product.id, text: product.name }); // Format for Select2
        //                             });
        
        //                             hasMoreRecords = data.more_records; // Update the flag for more records
        //                             page++; // Increment page for the next request
        
        //                             success({ results, pagination: { more: hasMoreRecords } }); // Send results to Select2
        //                         } else {
        //                             hasMoreRecords = false; // No more records
        //                             success({ results: [], pagination: { more: false } });
        //                         }
        //                     })
        //                     .catch((error) => {
        //                         console.error('Error fetching products:', error);
        //                         failure(error);
        //                     })
        //                     .finally(() => {
        //                         loading = false; // Reset loading state
        //                     });
        //             },
        //             processResults: function (data) {
        //                 return data; // The transport already formats the response correctly
        //             },
        //             delay: 250, // Add delay to prevent excessive requests
        //         },
        //         templateResult: formatOption,
        //         templateSelection: formatOption,
        //     });
        
        //     // Format long text in dropdown and selection
        //     function formatOption(option) {
        //         if (!option.id) return option.text; // Show placeholder
        //         return $('<span class="wrap-text"></span>').text(option.text); // Wrap long text
        //     }
        
        //     // Handle description display when a product is selected
        //     $('#itemName').on('change', function () {
        //         const selectedItemId = $(this).val();
        
        //         // Clear the description if no item is selected
        //         if (!selectedItemId) {
        //             $('#itemDescription').val('');
        //             return;
        //         }
        
        //         // Fetch the product description from the stored details
        //         const selectedDescription = productDetails[selectedItemId];
        //         $('#itemDescription').val(selectedDescription || '');
        //     });
        // });

		// Fetch item names2 from the API and quote details for editing a quote
		$(document).ready(function () {
            const productDetails = {};
            let loading = false; // Prevent multiple concurrent requests
            let hasMoreRecords = true; // Indicator for more records
            let page = 1; // Current page for pagination
            let lastQuery = ''; // To track the last search query
            let isPrePopulating = false; // Flag to handle pre-population vs user selection
        
            // Initialize Select2 for #itemName2 with AJAX and infinite scrolling
            $('#itemName2').select2({
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
            });
        
            // Handle product selection to update the description
            $('#itemName2').on('change', function () {
                if (isPrePopulating) return; // Skip handling during pre-population
        
                const selectedItemId = $(this).val();
                if (!selectedItemId) {
                    $('#itemDescription2').val('');
                    return;
                }
                const selectedDescription = productDetails[selectedItemId];
                $('#itemDescription2').val(selectedDescription || '');
            });
        
            // Fetch and pre-populate the product details when a quote number is provided
            $(document).on('blur', '#QuoteNumber', function () {
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
        
                            // Populate other fields
                            $('#subject2').val(data.subject || '');
                            $('#kind_attention2').val(data.kind_attention || '');
                            $('#project2').val(data.project_name || '');
                            $('#specification2').val(data.specification || '');
                            $('#general-exclusion2').val(data.general_exclusion || '');
                            $('#brand2').val(data.brand || '');
                            $('#warranty2').val(data.warranty || '');
                            $('#delivery2').val(data.delivery || '');
                            $('#terms-of-payment2').val(data.terms_of_payment || '');
                            $('#uom2').val(data.uom || '');
                            $('#quantity2').val(data.quantity || '');
                            $('#unitPrice2').val(data.service_charge || '');
                            $('#valid-until2').val(data.valid_until || '');
        
                            // Enable pre-population mode
                            isPrePopulating = true;
        
                            // Fetch products and include the selected product
                            fetchProducts(data.product_id, data.product_name);
        
                            // Populate the description field
                            $('#itemDescription2').val(data.product_description || '');
        
                            // Disable pre-population mode after a short delay
                            setTimeout(() => {
                                isPrePopulating = false;
                            }, 2000);
        
                            // Trigger totals calculation
                            calculateTotals();
                        } else {
                            console.error('API Error:', response.error || 'Failed to fetch quote details.');
                        }
                    },
                    error: function () {
                        console.error('AJAX Error: Error fetching quote details.');
                    }
                });
            });
        
            // Function to fetch product list and optionally pre-select a product
            function fetchProducts(selectedProductId = null, selectedProductName = null) {
                fetch('https://app.voltronix.ae/voltronix/deal/products')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && Array.isArray(data.products)) {
                            // Clear existing options
                            $('#itemName2').empty().append('<option value="">-None-</option>');
        
                            // Populate dropdown and product details
                            data.products.forEach(product => {
                                productDetails[product.id] = product.description || '';
                                $('#itemName2').append(new Option(product.name, product.id));
                            });
        
                            // Include and select the specified product
                            if (selectedProductId && !productDetails[selectedProductId]) {
                                productDetails[selectedProductId] = '';
                                $('#itemName2').append(new Option(selectedProductName, selectedProductId));
                            }
        
                            if (selectedProductId) {
                                $('#itemName2').val(selectedProductId).trigger('change');
                            }
                        } else {
                            console.error('Unexpected response format or no products found.');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching products:', error);
                    });
            }
        
            // Totals calculation
            function calculateTotals() {
                let quantity = parseFloat($('#quantity2').val()) || 0;
                let unitPrice = parseFloat($('#unitPrice2').val()) || 0;
                let total = quantity * unitPrice;
                $('#total2').val(total.toFixed(2));
        
                let subTotal = total;
                $('#sub-total2').val(subTotal.toFixed(2));
        
                let discountAmount = parseFloat($('#discount2').val()) || 0;
                let subTotalAfterDiscount = subTotal - discountAmount;
        
                let vatPercentage = 5;
                let vat = (vatPercentage / 100) * subTotalAfterDiscount;
        
                let adjustment = parseFloat($('#adjustment2').val()) || 0;
                let grandTotal = subTotal - discountAmount + vat + adjustment;
                $('#grand-total2').val(grandTotal.toFixed(2));
            }
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
			$('#itemName').on('change', function () {
				const productId = $(this).val(); // Get product_id from value
				const productName = $(this).find('option:selected').text(); // Get product_name from text

				// Store these values in hidden fields
				$('#product_id').val(productId);
				$('#product_name').val(productName);
			});

			// Handle product selection and store values in hidden fields
			$('#itemName2').on('change', function () {
				const productId = $(this).val(); // Get product_id from value
				const productName = $(this).find('option:selected').text(); // Get product_name from text

				// Store these values in hidden fields
				$('#product_id2').val(productId);
				$('#product_name2').val(productName);
			});
			
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

				// Collect data for each item row
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
						return false; // Stop submission if validation fails
					}

					formData.items.push(item);
				});

				// Show loader and disable button
				$('.buttonLoader').show();
				$('.submitButton').prop('disabled', true);

				// AJAX request for proposal creation
				$.ajax({
					url: 'dashboard/add_proposal',
					type: 'POST',
					data: JSON.stringify(formData),
					contentType: 'application/json',
					dataType: 'json',
					success: function (response) {
						// Hide loader and enable button on success
						$('.buttonLoader').hide();
						$('.submitButton').prop('disabled', false);

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
							});

							// Reset the form after showing success message
							$('#reProposalForm')[0].reset();

							// Clear all dynamically added rows except the first one
							$('#itemRows tr').not(':first').remove();

							// Reset the row count to 1
							$('#itemRows tr:first td:first').text('1');
						}
					},
					error: function (xhr) {
						// Hide loader and enable button on error
						$('.buttonLoader').hide();
						$('.submitButton').prop('disabled', false);

						// Log the response to debug
						console.log(xhr.responseJSON);

						// Show error message
						const errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
						Swal.fire({
							icon: 'error',
							title: 'Form Submission Error',
							text: errorMessage,
							showConfirmButton: true,
						});
					}
				});
			});

			$('#editQuoteForm').submit(function(e) {
                e.preventDefault();
            
                // Reset previous error messages
                $('.error-message').empty();
            
                var formData = $(this).serialize();
            
                // Show loader and disable button
                $('.buttonLoader').show();
                $('.submitButton').prop('disabled', true);
            
                // AJAX request
                $.ajax({
                    url: 'dashboard/edit_proposal',
                    type: 'POST',
                    data: formData,
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        // Log the response to verify its structure
                        console.log('Response:', response);
            
                        // Hide loader and enable button
                        $('.buttonLoader').hide();
                        $('.submitButton').prop('disabled', false);
            
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
                            });
            
                            // Reset the form
                            $('#editQuoteForm')[0].reset();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Form Submission Error',
                                text: response.message || 'An unexpected error occurred.',
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(xhr) {
                        // Hide loader and enable button on error
                        $('.buttonLoader').hide();
                        $('.submitButton').prop('disabled', false);
            
                        // Log the response for debugging
                        console.log('Error Response:', xhr);
            
                        // Show error message
                        var errorMessage = xhr.responseJSON?.error || 'An unexpected error occurred. Please try again.';
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
