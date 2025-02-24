document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const toggleSidebarButton = document.getElementById("toggleSidebar");
    const closeButton = document.getElementById("closeSidebarButton");
    const headerMain = document.querySelector(".header-main");
    const expandedLogo = document.getElementById("sidebarLogo");
    const collapsedLogo = document.getElementById("smallSidebarLogo");
    const mainContent = document.getElementById("mainContent");
    const hamburgerIcon = document.querySelector(".hamburger");
    const taskToggle = document.getElementById("taskToggle");
    const taskDropdownIcon = document.getElementById("taskDropdownIcon");
    const taskSubmenu = document.getElementById("taskSubmenu");

    function isMobile() {
        return window.innerWidth <= 768;
    }

    function applySidebarState(isCollapsed) {
        const submenuTexts = document.querySelectorAll(".submenu-text");

        if (isMobile()) {
            sidebar.style.width = isCollapsed ? "0" : "100%";
            mainContent.style.paddingLeft = "0";
            headerMain.style.marginLeft = "0";
            headerMain.style.width = "100%";

            if (isCollapsed) {
                document.body.classList.remove("sidebar-open");
                closeButton.classList.add("d-none");
                toggleSidebarButton.classList.remove("d-none");
                hamburgerIcon.classList.remove("active");
            } else {
                document.body.classList.add("sidebar-open");
                closeButton.classList.remove("d-none");
                toggleSidebarButton.classList.add("d-none");
                hamburgerIcon.classList.add("active");
            }
        } else {
            sidebar.style.width = isCollapsed ? "95px" : "250px";
            mainContent.style.paddingLeft = isCollapsed ? "95px" : "250px";
            headerMain.style.marginLeft = isCollapsed ? "95px" : "250px";
            headerMain.style.width = isCollapsed ? "calc(100% - 95px)" : "calc(100% - 250px)";

            toggleSidebarButton.classList.remove("d-none");
            closeButton.classList.add("d-none");
            hamburgerIcon.classList.remove("active");
        }

        submenuTexts.forEach(text => {
            text.style.display = isCollapsed ? "none" : "inline-block";
        });

        expandedLogo.classList.toggle("d-none", isCollapsed);
        collapsedLogo.classList.toggle("d-none", !isCollapsed);
    }

    function toggleSidebar() {
        const isCollapsed = sidebar.classList.toggle("collapsed");

        // Only save sidebar state for desktop
        if (!isMobile()) {
            localStorage.setItem("sidebarCollapsed", isCollapsed);
        }

        applySidebarState(isCollapsed);
        hamburgerIcon.classList.add("click-effect");
        setTimeout(() => hamburgerIcon.classList.remove("click-effect"), 200);
    }

    // Check sidebar state on load
    let shouldCollapse = isMobile() || localStorage.getItem("sidebarCollapsed") === "true";
    sidebar.classList.toggle("collapsed", shouldCollapse);
    applySidebarState(shouldCollapse);

    toggleSidebarButton.addEventListener("click", function () {
        if (isMobile()) {
            hamburgerIcon.classList.toggle("active");
        }
        toggleSidebar();
    });

    closeButton.addEventListener("click", function () {
        toggleSidebar();
        hamburgerIcon.classList.remove("active");
    });

    taskToggle.addEventListener("click", function (event) {
        event.preventDefault();

        // Expand sidebar if collapsed
        if (sidebar.classList.contains("collapsed")) {
            sidebar.classList.remove("collapsed");
            applySidebarState(false);
        }

        // Toggle submenu
        const isSubmenuOpen = taskSubmenu.classList.contains("show");
        taskSubmenu.classList.toggle("show", !isSubmenuOpen);
        taskDropdownIcon.classList.toggle("rotate-90", !isSubmenuOpen);

        // Save submenu state only when it's opened
        if (!isSubmenuOpen) {
            localStorage.setItem("taskSubmenuOpen", "true");
        } else {
            localStorage.removeItem("taskSubmenuOpen");
        }
    });

    // Restore submenu state only if the current page is related to tasks
    if (window.location.href.includes("assignTask")) {
        if (localStorage.getItem("taskSubmenuOpen") === "true") {
            taskSubmenu.classList.add("show");
            taskDropdownIcon.classList.add("rotate-90");
        }
    } else {
        localStorage.removeItem("taskSubmenuOpen"); // Reset submenu state on other pages
    }
});
