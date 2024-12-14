document.addEventListener("DOMContentLoaded", function () {
	const sidebar = document.getElementById("sidebar");
	const hamburgerButton = document.getElementById("hamburgerButton");
	const closeSidebarButton = document.getElementById("closeSidebarButton");
	const mainContent = document.getElementById("mainContent");

	// Toggle sidebar when the hamburger button is clicked
	hamburgerButton.addEventListener("click", function () {
		sidebar.classList.add("open");
	});

	// Close sidebar when the close button is clicked
	closeSidebarButton.addEventListener("click", function () {
		sidebar.classList.remove("open");
	});
});
