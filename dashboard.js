
  const profileBtn = document.getElementById("profileBtn");
  const profilePopup = document.getElementById("profilePopup");
  const profileUpload = document.getElementById("profileUpload");
  const previewImg = document.getElementById("previewImg");
  const overlay = document.getElementById("popupOverlay");

  // --- Profile popup logic ---
  profileBtn.addEventListener("click", () => {
    const isVisible = profilePopup.style.display === "block";
    profilePopup.style.display = isVisible ? "none" : "block";
    overlay.style.display = isVisible ? "none" : "block";
  });

  overlay.addEventListener("click", () => {
    profilePopup.style.display = "none";
    overlay.style.display = "none";
  });

  document.querySelector(".upload-label").addEventListener("click", () => {
    profileUpload.click();
  });

  profileUpload.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = () => {
        previewImg.src = reader.result;
        profileBtn.querySelector("img").src = reader.result;
      };
      reader.readAsDataURL(file);
    }
  });

 // === Spending by Category Chart ===
const ctx = document.getElementById("spendingChart").getContext("2d");

if (ctx) {
  new Chart(ctx, {
    type: "pie", // ✅ pie instead of doughnut
    data: {
      labels: ["Food", "Transport", "School Expenses", "Others"],
      datasets: [
        {
          data: [48.5, 26.1, 21.7, 8.7],
          backgroundColor: ["#F5B942", "#d4a015", "#a0780c", "#6b5505"],
          borderColor: "#111",
          borderWidth: 2,
          borderRadius: 6,
        },
      ],
    },
    options: {
      plugins: {
        legend: { display: false }, // custom legend below instead
      },
      animation: {
        animateScale: true,
        animateRotate: true,
      },
      maintainAspectRatio: false,
    },
  });
}
// ===== MARK PAID BUTTON LOGIC =====
document.querySelectorAll('.reminder button').forEach(button => {
  button.addEventListener('click', () => {
    button.textContent = 'Paid';
    button.style.backgroundColor = '#d3d3d3'; // light gray
    button.style.color = '#666'; // darker text for contrast
    button.disabled = true;
    button.style.cursor = 'not-allowed';
    button.style.transition = 'all 0.3s ease';
  });
});
