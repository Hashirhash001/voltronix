<!-- Sidebar -->
<nav class="sidebar text-dark" id="sidebar">
	<!-- Sidebar Header: Logo & Toggle Button -->
	<div class="sidebar-header d-flex align-items-center gap-2 px-3 py-3">


		<!-- Default Logo (Visible in Expanded Mode) -->
		<img src="<?php echo base_url('assets/photos/logo/voltronix_logo.png'); ?>" id="sidebarLogo" class="sidebar-logo expanded-logo" alt="Logo" style="height: 50px; width: auto;">

		<!-- Small Logo (Visible in Collapsed Mode) -->
		<img src="<?php echo base_url('assets/photos/logo/small_logo.jpg'); ?>" id="smallSidebarLogo" class="sidebar-logo collapsed-logo d-none" alt="Small Logo" style="height: 40px; width: auto;">

		<!-- Sidebar Close Button (Mobile) -->
		<button class="hamburger d-md-none" id="closeSidebarButton">
			<span></span>
			<span></span>
			<span></span>
		</button>

		<!-- <button class="btn btn-outline-dark border-0 toggleSidebar" id="toggleSidebar" style="font-size: 1.5rem;">
			<i class="bi bi-list"></i>
		</button> -->
	</div>

	<!-- Navigation Menu -->
	<ul class="nav flex-column px-3 pt-3 justify-content-between">
		<div class="d-flex justify-content-between flex-column" style="height: 90%;">
			<div>
				<li class="nav-item mb-2">
					<a href="<?php echo site_url('web/dashboard'); ?>" class="nav-link d-flex align-items-center gap-2 <?php echo ($this->uri->segment(2) == 'dashboard') ? 'active' : ''; ?>">
						<i class="bi-bar-chart"></i>
						<span class="sidebar-text">Dashboard</span>
					</a>
				</li>
				<li class="nav-item mb-2">
					<a href="<?php echo site_url('web/deals'); ?>" class="nav-link d-flex align-items-center gap-2 <?php echo ($this->uri->segment(2) == 'deals') ? 'active' : ''; ?>">
						<i class="bi bi-briefcase"></i>
						<span class="sidebar-text">My Jobs</span>
					</a>
				</li>
				<li class="nav-item mb-2">
					<a href="<?php echo site_url('web/dealsAndProposals'); ?>" class="nav-link d-flex align-items-center gap-2 <?php echo ($this->uri->segment(2) == 'dealsAndProposals') ? 'active' : ''; ?>">
						<i class="bi bi-file-earmark-plus"></i>
						<span class="sidebar-text">Deals and Proposal</span>
					</a>
				</li>
				<!-- Task Management with Subcategories -->
				<li class="nav-item mb-2">
					<a href="#" class="nav-link d-flex align-items-center gap-2 <?php echo ($this->uri->segment(2) == 'assignTask' || $this->uri->segment(2) == 'all-tasks') ? 'active' : ''; ?>" data-bs-toggle="collapse" data-bs-target="" aria-expanded="false" id="taskToggle" style="margin-bottom: .25rem;">
						<i class="bi bi-clipboard-check"></i>
						<span class="sidebar-text">Task Management
							<svg id="taskDropdownIcon" class="size-1 text-slate-400 transition-transform ease-in-out" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
							</svg>
						</span>
					</a>
					
					<ul class="collapse list-unstyled ps-4 <?php echo ($this->uri->segment(2) == 'assignTask') ? 'show' : ''; ?>" id="taskSubmenu" style="padding: 0 !important;">
						<?php if ($this->session->userdata('role') == '1') : ?>
							<li class="nav-item submenu-text">
								<a href="<?php echo site_url('web/assignTask'); ?>" 
								class="nav-link sub-menu d-flex align-items-center gap-2 w-100 <?php echo ($this->uri->segment(2) == 'assignTask' && !$this->uri->segment(3)) ? 'active submenu-active' : ''; ?>" style="padding: .625rem 2.25rem;">
									<i class="bi bi-person-plus"></i> Assign a Task
								</a>
							</li>
							<li class="nav-item submenu-text">
								<a href="<?php echo site_url('web/assignTask/all-tasks'); ?>" 
								class="nav-link sub-menu d-flex align-items-center gap-2 w-100 <?php echo ($this->uri->segment(2) == 'assignTask' && $this->uri->segment(3) == 'all-tasks') ? 'active submenu-active' : ''; ?>" style="padding: .625rem 2.25rem;">
									<i class="bi bi-list-check"></i> All Tasks
								</a>
							</li>
						<?php endif; ?>
						<?php if ($this->session->userdata('role') != '1') : ?>
							<li class="nav-item submenu-text">
								<a href="<?php echo site_url('web/assignTask/my-tasks'); ?>" 
								class="nav-link sub-menu d-flex align-items-center gap-2 w-100 
								<?php echo ($this->uri->segment(2) == 'assignTask' && $this->uri->segment(3) == 'my-tasks') ? 'active submenu-active' : ''; ?>" 
								style="padding: .625rem 2.25rem;">
									<i class="bi bi-person-badge"></i> My Tasks
								</a>
							</li>
						<?php endif; ?>

					</ul>
					

				</li>
			</div>

			<div>
				<!-- Logout Button in Sidebar -->
				<li class="nav-item mt-4 border-top pt-3">
					<a href="#" class="nav-link d-flex align-items-center gap-2 text-danger" id="logoutButton">
						<i class="bi bi-box-arrow-right"></i>
						<span class="sidebar-text">Logout</span>
					</a>
				</li>
			</div>
		</div>
	</ul>
</nav>
