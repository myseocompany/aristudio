const header = document.querySelector(".site-header");

window.addEventListener("scroll", () => {
  const scrolled = window.scrollY > 24;
  header.classList.toggle("is-scrolled", scrolled);
});
