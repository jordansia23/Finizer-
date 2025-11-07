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
  const pendingList = document.getElementById("pending-bills");
  const paidList = document.getElementById("paid-bills");

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
  // ADD BILL FUNCTIONALITY
  // ===============================
  if (billForm) {
    billForm.addEventListener("submit", (e) => {
      e.preventDefault();

      const name = document.getElementById("billName").value;
      const type = document.getElementById("billType").value;
      const amount = document.getElementById("billAmount").value;
      const dueDate = document.getElementById("billDueDate").value;

      if (!name || !type || !amount || !dueDate) return;

      const formattedDate = new Date(dueDate).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric"
      });

      const newBill = document.createElement("div");
      newBill.classList.add("bill-card");
      newBill.innerHTML = `
        <div class="bill-info">
          <h3>${name}</h3>
          <p>Type: ${type}</p>
          <p>Due: ${formattedDate}</p>
        </div>
        <div class="bill-right">
          <p class="bill-amount">₱${parseFloat(amount).toLocaleString()}</p>
          <button class="mark-paid">Mark Paid</button>
        </div>
      `;

      pendingList.appendChild(newBill);
      billForm.reset();
      closePopup();
      checkNotifications(); // update notif list
    });
  }

  // ===============================
  // MARK AS PAID FUNCTIONALITY
  // ===============================
  document.addEventListener("click", (e) => {
    if (e.target.classList.contains("mark-paid")) {
      const billCard = e.target.closest(".bill-card");
      const billName = billCard.querySelector("h3").textContent;
      const billAmount = billCard.querySelector(".bill-amount").textContent;

      const paidDate = new Date().toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric"
      });

      const paidBill = document.createElement("div");
      paidBill.classList.add("bill-card", "paid");
      paidBill.innerHTML = `
        <div class="bill-info">
          <h3>${billName}</h3>
          <p>Paid on: ${paidDate}</p>
        </div>
        <p class="bill-amount">${billAmount}</p>
      `;

      paidList.appendChild(paidBill);
      billCard.remove();
      checkNotifications(); // refresh notif list
    }
  });

  // ===============================
  // NOTIFICATION DROPDOWN & REMINDERS
  // ===============================
  const notifBell = document.getElementById("notifBell");
  const notifDropdown = document.getElementById("notifDropdown");
  const notifList = document.getElementById("notifList");

  notifBell.addEventListener("click", () => {
    notifDropdown.classList.toggle("hidden");
  });

  // --- Function to check and display notifications ---
  function checkNotifications() {
    notifList.innerHTML = "";
    const today = new Date();

    const bills = document.querySelectorAll("#pending-bills .bill-card");
    bills.forEach((bill) => {
      const dueText = bill.querySelector(".bill-info p:nth-child(3)")?.textContent;
      if (!dueText) return;

      const match = dueText.match(/Due: (.+)/);
      if (!match) return;

      const dueDate = new Date(match[1]);
      const diffDays = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));

      let message = "";
      if (diffDays === 0) {
        message = `🕒 ${bill.querySelector("h3").textContent} is due today!`;
      } else if (diffDays > 0 && diffDays <= 3) {
        message = `⚠️ ${bill.querySelector("h3").textContent} is due in ${diffDays} day(s)!`;
      } else if (diffDays < 0) {
        message = `❌ ${bill.querySelector("h3").textContent} is overdue!`;
      }

      if (message) {
        const li = document.createElement("li");
        li.textContent = message;
        notifList.appendChild(li);
      }
    });

    if (notifList.children.length === 0) {
      notifList.innerHTML = "<li>No upcoming bills 🎉</li>";
    }
  }

  // Run once on load
  checkNotifications();
});
