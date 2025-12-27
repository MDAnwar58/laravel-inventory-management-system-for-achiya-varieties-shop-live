const linePreloader = document.getElementById("linePreloader");
const loaderLoadingOverlay = document.getElementById("loaderLoadingOverlay");

function showLoader() {
   // linePreloader.classList.remove("d-none");
   loaderLoadingOverlay.classList.remove("d-none");
}
function hideLoader() {
   // linePreloader.classList.add("d-none");
   loaderLoadingOverlay.classList.add("d-none");
}
