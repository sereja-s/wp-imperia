/**
 * Main Javascript.
 * This file is for who want to make this theme as a new parent theme and you are ready to code your js here.
 */
document.addEventListener("DOMContentLoaded", function () {
  const menu = document.getElementById("menu-menu-tovar");

  if (!menu) {
    return;
  }

  const parents = menu.querySelectorAll(".menu-item-has-children");
  parents.forEach(function (item) {
    const subMenu = item.querySelector(".sub-menu");
    const link = item.querySelector(":scope > a");

    if (!subMenu || !link) {
      return;
    }

    link.addEventListener("click", function (e) {
      const rect = link.getBoundingClientRect();
      const clickArea = 40;

      // Клик только по зоне иконки справа
      if (e.clientX < rect.right - clickArea) {
        return;
      }

      e.preventDefault();
      e.stopPropagation();

      item.classList.toggle("open");
    });
  });
});
