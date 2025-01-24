<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Deals and Proposal</title>
	<link rel="icon" href="<?php echo base_url('assets/photos/logo/favicon.png'); ?>" type="image/x-icon">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

	<!-- Include Bootstrap Icons if not already included -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/style.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/style2.css'); ?>" rel="stylesheet">
	
	<style>
	    .nav-link:hover {
            background-color: #FF0100 !important;
            color: #fff !important;
            text-decoration: none;
        }
        
        .active {
            background-color: #FF0100 !important;
            color: #fff !important;
        }
	</style>
	
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
                    <a href="<?php echo site_url('web/dashboard'); ?>" class="nav-link d-flex align-items-center gap-2 active">
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
							<div class="card-body">
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
													<th>Total (AED)</th>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td>1</td>
													<td style="min-width: 400px;">
													    <input type="hidden" id="product_id" name="product_id">
														<input type="hidden" id="product_name" name="product_name">
														<select class="form-control" id="itemName" name="itemName" style="width: 100%;" required>
															<option value="">-None-</option>
														</select>
														<textarea name="itemDescription" id="itemDescription" cols="30" rows="3" style="width: 100%; border: 1px solid #ced4da;"></textarea>
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
										<button type="submit" id="" class="btn submitButton px-4 py-2" style="border-radius: 20px; font-weight: bold; background-color: #FF0100; color: #fff;">
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
						<div class="card-header text-white" id="headingTwo" style="background-color: #FF0100;">
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
													<td style="min-width: 400px;">
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
	</div>

	<!-- Include jQuery and Select2 JavaScript -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<!-- Optional: Include Bootstrap's JavaScript (jQuery and Popper.js) for accordion functionality -->
	<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const editQuoteIcon = document.getElementById('editQuoteIcon');
			const editQuoteCollapse = document.getElementById('collapseThree');

			console.log(editQuoteIcon, editQuoteCollapse); // Ensure both elements are found

			if (editQuoteIcon && editQuoteCollapse) {
				// When the collapse is shown
				editQuoteCollapse.addEventListener('show.bs.collapse', function () {
					console.log("The collapse is being shown!");
					editQuoteIcon.classList.remove('bi-plus-circle');
					editQuoteIcon.classList.add('bi-dash-circle');
				});

				// When the collapse is hidden
				editQuoteCollapse.addEventListener('hide.bs.collapse', function () {
					console.log("The collapse is being hidden!");

					editQuoteIcon.classList.remove('bi-dash-circle');
					editQuoteIcon.classList.add('bi-plus-circle');
				});
			} else {
				console.error("Edit Quote button or collapse target not found!");
			}
		});

		// Calculate and update totals of item1
		$(document).ready(function() {
			function calculateTotals() {
				let quantity = parseFloat($('#quantity').val()) || 0;
				let unitPrice = parseFloat($('#unitPrice').val()) || 0;
				let total = quantity * unitPrice;
				$('#total').val(total.toFixed(2));

				// Calculate Sub Total (only one item row is present in this example)
				let subTotal = total;
				$('#sub-total').val(subTotal.toFixed(2));

				// Get the fixed discount amount (instead of percentage)
				let discountAmount = parseFloat($('#discount').val()) || 0;

				// Subtract discount from Sub Total before VAT calculation
				let subTotalAfterDiscount = subTotal - discountAmount;

				// VAT Calculation (Fixed VAT percentage of 5%)
				let vatPercentage = 5; // Fixed VAT percentage
				let vat = (vatPercentage / 100) * subTotalAfterDiscount;

				// Get other values for Grand Total calculation
				let adjustment = parseFloat($('#adjustment').val()) || 0;

				// Grand Total Calculation: Sub Total - Discount + VAT + Adjustment
				let grandTotal = subTotal - discountAmount + vat + adjustment;
				$('#grand-total').val(grandTotal.toFixed(2));
			}

			// Attach event listeners to calculate totals when Quantity or Unit Price changes
			$('#quantity, #unitPrice').on('input', calculateTotals);

			// Attach event listeners for the discount and adjustment fields to update the Grand Total
			$('#discount, #adjustment').on('input', calculateTotals);

			// Initial calculation to ensure totals are correct on page load
			calculateTotals();
		});

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
		$(document).ready(function () {
			// Object to store product details by ID
			const productDetails = {};

			// Initialize Select2 with placeholder and clearing options
			$('#itemName').select2({
				placeholder: 'Search for an item...',
				allowClear: true,
				width: '100%',
				templateResult: formatOption,
				templateSelection: formatOption,
			});

			// Fetch item names from the API
			fetch('https://app.voltronix.ae/voltronix/deal/products')
				.then((response) => response.json())
				.then((data) => {
					if (data.success && Array.isArray(data.products)) {
						data.products.forEach((product) => {
							// Store product details in a map for quick access
							productDetails[product.id] = product.description || '';
							// Add product to the dropdown
							$('#itemName').append(new Option(product.name, product.id));
						});
					} else {
						console.error('Unexpected response format or no products found.');
					}
				})
				.catch((error) => {
					console.error('Error fetching item names:', error);
				});

			// Format long text in dropdown and selection
			function formatOption(option) {
				if (!option.id) return option.text; // Show placeholder
				return $('<span class="wrap-text"></span>').text(option.text); // Wrap long text
			}

			// Fetch and display product description when an item is selected
			$('#itemName').on('change', function () {
				const selectedItemId = $(this).val();

				// Clear the description if no item is selected
				if (!selectedItemId) {
					$('#itemDescription').val('');
					return;
				}

				// Get the description for the selected product from the stored details
				const selectedDescription = productDetails[selectedItemId];
				$('#itemDescription').val(selectedDescription || '');
			});

		});

		// Fetch item names2 from the API and quote details for editing a quote
		$(document).ready(function () {
			 // Object to store product details by ID
			 const productDetails = {};

			// Initialize Select2 once, keeping options dynamic
			$('#itemName2').select2({
				placeholder: 'Search for an item...',
				allowClear: true,
				width: '100%',
				templateResult: formatOption,
				templateSelection: formatOption,
			});

			// Function to fetch and populate products
			function fetchProducts(selectedProductId = null, selectedProductName = null) {
				fetch('https://app.voltronix.ae/voltronix/deal/products')
					.then((response) => response.json())
					.then((data) => {
						if (data.success && Array.isArray(data.products)) {
							// Clear existing options
							$('#itemName2').empty().append('<option value="">-None-</option>');

							// Store product details and add options
							data.products.forEach((product) => {
								productDetails[product.id] = product.description || '';
								$('#itemName2').append(new Option(product.name, product.id));
							});

							// If editing, include the selected product if not already in the list
							if (selectedProductId && !productDetails[selectedProductId]) {
								productDetails[selectedProductId] = '';
								$('#itemName2').append(new Option(selectedProductName, selectedProductId));
							}

							// Set the selected value for editing
							if (selectedProductId) {
								$('#itemName2').val(selectedProductId).trigger('change');
							}
						} else {
							console.error('Unexpected response format or no products found.');
						}
					})
					.catch((error) => {
						console.error('Error fetching item names:', error);
					});
			}

			// Fetch initial product list
			fetchProducts();

			// Format long text in dropdown and selection
			function formatOption(option) {
				if (!option.id) return option.text; // Show placeholder
				return $('<span class="wrap-text"></span>').text(option.text); // Wrap long text
			}

			// Fetch and display product description when an item is selected
			// $('#itemName2').on('change', function () {
			// 	const selectedItemId = $(this).val();
			// 	if (!selectedItemId) {
			// 		$('#itemDescription2').val('');
			// 		return;
			// 	}
			// 	const selectedDescription = productDetails[selectedItemId];
			// 	$('#itemDescription2').val(selectedDescription || '');
			// });

			// Handle QuoteNumber blur event
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

							// Populate fields with data from the response
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

							// Fetch products and include the current selected product
							fetchProducts(data.product_id, data.product_name);

							// Populate description field
							$('#itemDescription2').val(data.product_description || '');

							// Trigger the calculation function immediately
							calculateTotals();
						} else {
							console.error('API Error:', response.error || 'Failed to fetch quote details.');
							// alert(response.error || 'Failed to fetch quote details.');
						}
					},
					error: function () {
						console.error('AJAX Error: Error fetching quote details.');
						// alert('Error fetching quote details.');
					}
				});
			});

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

							// Optionally, call fetchData if needed
							// fetchData(1, $('#searchInput').val(), sort_by, sort_direction);
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

		// // Attach event listeners to calculate totals when Quantity or Unit Price changes
		// $('#quantity2, #unitPrice2').on('input', calculateTotals);

		// // Attach event listeners for discount, VAT, and adjustment fields to update Grand Total
		// $('#discount2, #vat2, #adjustment2').on('input', calculateTotals);

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
