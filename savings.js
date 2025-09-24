// Sample JS for button actions
document.querySelector(".floating-btn").addEventListener("click", () => {
  alert("Add New Savings Goal clicked!");
});

document.querySelectorAll(".btn-add").forEach(btn => {
  btn.addEventListener("click", () => {
    alert("Add money to savings!");
  });
});

document.querySelectorAll(".btn-details").forEach(btn => {
  btn.addEventListener("click", () => {
    alert("View details of this savings goal.");
  });
});
