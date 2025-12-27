document.addEventListener("DOMContentLoaded", () => {
    const jsSidebarToggle = document.querySelector(".js-sidebar-toggle");
    const sidebarCloseBtn = document.querySelector(".sidebar-close-btn");
    const jsSidebar = document.querySelector(".js-sidebar");
    const sidebarCustomOverlay = document.querySelector(
        ".sidebar-custom-overlay"
    );

    jsSidebarToggle.addEventListener("click", () => {
        sidebarCloseBtn.classList.add("show");
        sidebarCustomOverlay.classList.add("show");
        // Stop scrolling
        document.body.style.overflow = "hidden";
    });
    sidebarCloseBtn.addEventListener("click", () => {
        setTimeout(() => {
            sidebarCloseBtn.classList.remove("show");
            jsSidebar.classList.remove("collapsed");
            sidebarCustomOverlay.classList.remove("show");
            // Restore scrolling
            document.body.style.overflow = "";
        }, 210);
    });
    // Optional: also close sidebar if clicking on overlay
    sidebarCustomOverlay.addEventListener("click", () => {
        sidebarCloseBtn.click();
    });
});
