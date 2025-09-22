document.addEventListener("DOMContentLoaded", () => {
  const profileBtn = document.getElementById("profileBtn");
  const profilePopup = document.getElementById("profilePopup");
  const profileUpload = document.getElementById("profileUpload");
  const previewImg = document.getElementById("previewImg");
  const overlay = document.getElementById("popupOverlay");

  // Toggle centered popup + overlay
  profileBtn.addEventListener("click", () => {
    const isVisible = profilePopup.style.display === "block";
    profilePopup.style.display = isVisible ? "none" : "block";
    overlay.style.display = isVisible ? "none" : "block";
  });

  // Close popup kapag nag-click sa overlay
  overlay.addEventListener("click", () => {
    profilePopup.style.display = "none";
    overlay.style.display = "none";
  });

  // Open file picker
  document.querySelector(".upload-label").addEventListener("click", () => {
    profileUpload.click();
  });

  // Preview uploaded image
  profileUpload.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = () => {
        previewImg.src = reader.result;
        profileBtn.querySelector("img").src = reader.result; // update main profile icon
      };
      reader.readAsDataURL(file);
    }
  });
});
