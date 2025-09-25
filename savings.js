// savings.js (fixed)
// English comments included

// Add-goal elements
const floatingBtn = document.querySelector(".floating-btn");
const popupOverlay = document.getElementById("popup");
const closePopup = document.getElementById("closePopup");
const savingsForm = document.getElementById("savingsForm");
const savingsContainer = document.querySelector(".savings-container");

// Update popup elements
const updatePopup = document.getElementById("updatePopup");
const updateForm = document.getElementById("updateForm");
const updateAmountInput = document.getElementById("updateAmount");
const depositBtn = document.getElementById("depositBtn");
const withdrawBtn = document.getElementById("withdrawBtn");
const closeUpdatePopup = document.getElementById("closeUpdatePopup");

// Active references for the card being updated
let activeCard = null;
let activeTarget = 0;
let activeSaved = 0;

// ---------- Show / hide Add Goal popup ----------
floatingBtn.addEventListener("click", () => {
  popupOverlay.style.display = "flex";
});

closePopup.addEventListener("click", () => {
  popupOverlay.style.display = "none";
  savingsForm.reset();
});

// ---------- Handle Add Goal submit ----------
savingsForm.addEventListener("submit", (e) => {
  e.preventDefault();

  const name = document.getElementById("goalName").value.trim();
  const target = parseFloat(document.getElementById("goalTarget").value);
  const saved = parseFloat(document.getElementById("goalSaved").value);

  if (!name || isNaN(target) || isNaN(saved) || target <= 0 || saved < 0) {
    alert("Please fill out all fields correctly.");
    return;
  }

  const percentage = Math.min(Math.round((saved / target) * 100), 100);

  // Create card and store numeric values in data attributes
  const card = document.createElement("div");
  card.classList.add("card");
  card.dataset.target = target;      // store target as data attribute
  card.dataset.saved = saved;        // store saved as data attribute

  card.innerHTML = `
    <h2>${escapeHtml(name)}</h2>
    <div class="progress"><div class="progress-fill" style="width: ${percentage}%;"></div></div>
    <div class="percentage">${percentage}%</div>
    <div class="amount">₱${saved.toLocaleString()} / ₱${target.toLocaleString()}</div>
    <div class="btn-group">
      <button class="btn btn-add">Update</button>
      <button class="btn btn-details">Details</button>
    </div>
  `;

  savingsContainer.appendChild(card);

  // Attach update behavior (reads latest values from dataset inside)
  attachUpdateEvents(card);

  savingsForm.reset();
  popupOverlay.style.display = "none";
});

// ---------- Attach update handlers to a card ----------
function attachUpdateEvents(card) {
  const updateBtn = card.querySelector(".btn-add");

  // When user clicks Update on a card, prepare popup with latest values
  updateBtn.addEventListener("click", () => {
    activeCard = card;
    activeTarget = parseFloat(card.dataset.target) || 0;
    activeSaved = parseFloat(card.dataset.saved) || 0;

    // show popup
    updateAmountInput.value = ""; // clear previous input
    updatePopup.style.display = "flex";
  });
}

// ---------- Deposit action ----------
depositBtn.addEventListener("click", (e) => {
  e.preventDefault();
  if (!activeCard) {
    alert("No card selected.");
    return;
  }

  const val = parseFloat(updateAmountInput.value);
  if (isNaN(val) || val <= 0) {
    alert("Please enter a valid amount to deposit.");
    return;
  }

  activeSaved += val;
  // save back to card dataset
  activeCard.dataset.saved = activeSaved;

  // update UI of the card
  updateCardUI(activeCard, activeSaved, activeTarget);

  // reset + close popup
  updateForm.reset();
  updatePopup.style.display = "none";
});

// ---------- Withdraw action ----------
withdrawBtn.addEventListener("click", (e) => {
  e.preventDefault();
  if (!activeCard) {
    alert("No card selected.");
    return;
  }

  const val = parseFloat(updateAmountInput.value);
  if (isNaN(val) || val <= 0) {
    alert("Please enter a valid amount to withdraw.");
    return;
  }

  // if trying to withdraw more than available, show message and block
  if (val > activeSaved) {
    alert("You cannot withdraw beyond your savings.");
    return;
  }

  activeSaved -= val;
  activeCard.dataset.saved = activeSaved;

  updateCardUI(activeCard, activeSaved, activeTarget);

  updateForm.reset();
  updatePopup.style.display = "none";
});

// ---------- Close update popup ----------
closeUpdatePopup.addEventListener("click", () => {
  updatePopup.style.display = "none";
  updateForm.reset();
});

// ---------- Update the DOM for a card ----------
function updateCardUI(card, saved, target) {
  if (!card) return;
  const progressFill = card.querySelector(".progress-fill");
  const percentageText = card.querySelector(".percentage");
  const amountText = card.querySelector(".amount");

  const pct = (target > 0) ? Math.min(Math.round((saved / target) * 100), 100) : 0;

  if (progressFill) progressFill.style.width = pct + "%";
  if (percentageText) percentageText.textContent = pct + "%";
  if (amountText) amountText.textContent = `₱${saved.toLocaleString()} / ₱${target.toLocaleString()}`;
}

// ---------- Initialize existing cards on page load (if any) ----------
document.querySelectorAll(".card").forEach((card) => {
  // parse values from existing .amount text, then set data attributes so we use uniform source
  try {
    const amountText = card.querySelector(".amount").textContent.split("/");
    const savedText = amountText[0].replace(/[₱, ]/g, "");
    const targetText = amountText[1].replace(/[₱, ]/g, "");
    const saved = parseFloat(savedText) || 0;
    const target = parseFloat(targetText) || 0;
    card.dataset.saved = saved;
    card.dataset.target = target;
  } catch (err) {
    // ignore parse errors
  }
  attachUpdateEvents(card);
});

// ---------- small helper to avoid HTML injection from name ----------
function escapeHtml(str) {
  return str.replace(/[&<>"']/g, (m) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
}
