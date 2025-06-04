document.addEventListener("DOMContentLoaded", function () {
  const tabButtons = document.querySelectorAll(".tab-button");
  const tabContents = document.querySelectorAll(".tab-content");
  const tabIds = ["upcoming", "past"];

  // Helper: activate tab
  function activateTab(tabIdx) {
    tabButtons.forEach((btn, i) => {
      if (i === tabIdx) {
        btn.classList.add("border-indigo-500", "text-indigo-600");
        btn.classList.remove(
          "border-transparent",
          "text-gray-500",
          "hover:text-gray-700",
          "hover:border-gray-300",
        );
        tabContents[i].classList.remove("hidden");
      } else {
        btn.classList.remove("border-indigo-500", "text-indigo-600");
        btn.classList.add(
          "border-transparent",
          "text-gray-500",
          "hover:text-gray-700",
          "hover:border-gray-300",
        );
        tabContents[i].classList.add("hidden");
      }
    });
    // Remember last tab
    localStorage.setItem("appts-tab", tabIdx);
  }

  // Setup: click handlers
  tabButtons.forEach((btn, idx) => {
    btn.addEventListener("click", () => activateTab(idx));
  });

  // Start: activate tab from memory or default to upcoming
  let tabIdx = parseInt(localStorage.getItem("appts-tab"), 10);
  if (isNaN(tabIdx) || tabIdx < 0 || tabIdx >= tabButtons.length) tabIdx = 0;
  activateTab(tabIdx);
});
