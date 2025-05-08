function toggleNavbar() {
  var navbarNav = document.getElementById("navbarNav");
  navbarNav.classList.toggle("show");

  document.body.classList.toggle("nav-expanded");

  if (document.body.classList.contains("nav-expanded")) {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  }
}
document.addEventListener("DOMContentLoaded", function () {
  const sessionPopup = document.querySelector(".session-popup");

  if (sessionPopup) {
    setTimeout(function () {
      sessionPopup.style.transition = "opacity 0.5s ease-out";
      sessionPopup.style.opacity = "0";

      setTimeout(function () {
        sessionPopup.remove();
      }, 500);
    }, 5000);
  }
});
