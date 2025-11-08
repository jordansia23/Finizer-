document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ bills.js is running!");

  // ===============================
  // TAB SWITCH FUNCTIONALITY
  // ===============================
  const tabs = document.querySelectorAll(".tab-btn");
  const lists = document.querySelectorAll(".bills-list");

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      tabs.forEach(t => t.classList.remove("active"));
      tab.classList.add("active");

      lists.forEach(list => list.classList.add("hidden"));
      document.getElementById(`${tab.dataset.tab}-bills`).classList.remove("hidden");
    });
  });

  // ===============================
  // ADD BILL POPUP FUNCTIONALITY
  // ===============================
  const addBillBtn = document.querySelector(".add-bill-btn");
  const modal = document.getElementById("addBillModal");
  const overlay = document.getElementById("modalOverlay");
  const closeModal = document.getElementById("closeModal");
  const billForm = document.getElementById("billForm");

  const openPopup = () => {
    modal.classList.add("show");
    overlay.classList.add("show");
  };

  const closePopup = () => {
    modal.classList.remove("show");
    overlay.classList.remove("show");
  };

  if (addBillBtn) addBillBtn.addEventListener("click", openPopup);
  if (closeModal) closeModal.addEventListener("click", closePopup);
  if (overlay) overlay.addEventListener("click", closePopup);

  // ===============================
  // NOTIFICATION DROPDOWN
  // ===============================
  const notifBell = document.getElementById("notifBell");
  const notifDropdown = document.getElementById("notifDropdown");

  if (notifBell) {
    notifBell.addEventListener("click", (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle("hidden");
    });
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', (e) => {
    if (!notifBell.contains(e.target) && !notifDropdown.contains(e.target)) {
      notifDropdown.classList.add('hidden');
    }
  });

  // Prevent dropdown from closing when clicking inside it
  notifDropdown.addEventListener('click', (e) => {
    e.stopPropagation();
  });

  // ===============================
  // SEARCH FUNCTIONALITY
  // ===============================
  const searchBar = document.querySelector(".search-bar");
  if (searchBar) {
    searchBar.addEventListener("input", (e) => {
      const searchTerm = e.target.value.toLowerCase();
      const activeTab = document.querySelector(".tab-btn.active").dataset.tab;
      const billsList = document.getElementById(`${activeTab}-bills`);
      const billCards = billsList.querySelectorAll(".bill-card");

      billCards.forEach(card => {
        const billName = card.querySelector("h3").textContent.toLowerCase();
        const billType = card.querySelector("p:nth-child(2)").textContent.toLowerCase();
        
        if (billName.includes(searchTerm) || billType.includes(searchTerm)) {
          card.style.display = "flex";
        } else {
          card.style.display = "none";
        }
      });
    });
  }

  // ===============================
  // FORM VALIDATION
  // ===============================
  if (billForm) {
    const dueDateInput = document.getElementById("billDueDate");
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    dueDateInput.min = today;

    billForm.addEventListener("submit", (e) => {
      const amount = document.getElementById("billAmount").value;
      const dueDate = dueDateInput.value;

      if (parseFloat(amount) <= 0) {
        e.preventDefault();
        alert("Amount must be greater than 0");
        return;
      }

      if (dueDate < today) {
        e.preventDefault();
        alert("Due date cannot be in the past");
        return;
      }
    });
  }

  // ===============================
  // AUTO-GENERATE NOTIFICATION BADGE
  // ===============================
  function updateNotificationBadge() {
    const notifList = document.getElementById("notifList");
    const notificationItems = notifList.querySelectorAll("li");
    let unreadCount = 0;

    notificationItems.forEach(item => {
      if (!item.textContent.includes("No upcoming bills")) {
        unreadCount++;
      }
    });

    // Remove existing badge
    const existingBadge = document.querySelector(".notif-badge");
    if (existingBadge) {
      existingBadge.remove();
    }

    // Add new badge if there are notifications
    if (unreadCount > 0 && notifBell) {
      const badge = document.createElement("span");
      badge.className = "notif-badge";
      badge.textContent = unreadCount;
      notifBell.style.position = "relative";
      notifBell.appendChild(badge);
    }
  }

  // Run on page load
  updateNotificationBadge();

  // ===============================
  // KEYBOARD SHORTCUTS
  // ===============================
  document.addEventListener("keydown", (e) => {
    // Ctrl + N to open add bill modal (Cmd + N on Mac)
    if ((e.ctrlKey || e.metaKey) && e.key === "n") {
      e.preventDefault();
      openPopup();
    }

    // Escape key to close modals
    if (e.key === "Escape") {
      closePopup();
      notifDropdown.classList.add("hidden");
    }
  });

  // ===============================
  // ENHANCED MARK PAID BUTTONS
  // ===============================
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("mark-paid-btn")) {
      // Add a small confirmation effect
      e.target.style.backgroundColor = "#27ae60";
      e.target.textContent = "✓ Paid";
      setTimeout(() => {
        e.target.textContent = "Mark Paid";
        e.target.style.backgroundColor = "";
      }, 1500);
    }
  });
});