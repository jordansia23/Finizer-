// -------------------------
// DOM Elements
// -------------------------
const addGoalBtn = document.getElementById("addGoalBtn");
const savingsForm = document.getElementById("savingsForm");
const popup = document.getElementById("popup");
const closePopup = document.getElementById("closePopup");
const goalsContainer = document.getElementById("goalsContainer");

const updatePopup = document.getElementById("updatePopup");
const updateForm = document.getElementById("updateForm");
const updateAmountInput = document.getElementById("updateAmount");
const depositBtn = document.getElementById("depositBtn");
const withdrawBtn = document.getElementById("withdrawBtn");
const closeUpdatePopup = document.getElementById("closeUpdatePopup");

const goalDetailsPopup = document.getElementById("detailsPopup");
const closeGoalDetails = document.getElementById("closeDetailsBtn");
const goalDetailsTitle = document.getElementById("goalNameDisplay");
const goalHistory = document.getElementById("historyList");

const goalNameEdit = document.getElementById("goalNameEdit");
const saveGoalNameBtn = document.getElementById("saveGoalNameBtn");
const deleteGoalBtn = document.getElementById("deleteGoalBtn");

let activeCard = null;
let activeSaved = 0;
let activeTarget = 0;

// -------------------------
// Add Goal Popup
// -------------------------
addGoalBtn.onclick = () => popup.style.display = "flex";
closePopup.onclick = () => popup.style.display = "none";

// -------------------------
// Add Goal
// -------------------------
savingsForm.onsubmit = (e) => {
  e.preventDefault();
  const name = document.getElementById("goalName").value.trim();
  const target = parseFloat(document.getElementById("goalTarget").value);
  const saved = parseFloat(document.getElementById("goalSaved").value) || 0;

  if (!name || isNaN(target) || target <= 0) {
    alert("Please enter valid inputs.");
    return;
  }

  const card = document.createElement("div");
  card.className = "card";
  card.dataset.saved = saved;
  card.dataset.target = target;
  card.dataset.history = JSON.stringify([]);

  const percent = Math.min((saved / target) * 100, 100);

  card.innerHTML = `
    <h2>${name}</h2>
    <div class="progress"><div class="progress-fill" style="width:${percent}%"></div></div>
    <p class="percentage">${percent.toFixed(1)}%</p>
    <p class="amount">₱<span class="saved">${saved.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span> / ₱${target.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
    <div class="btn-group">
      <button class="btn btn-add update-btn">Update</button>
      <button class="btn btn-detail details-btn">Details</button>
    </div>
  `;

  goalsContainer.appendChild(card);
  savingsForm.reset();
  popup.style.display = "none";
};

// -------------------------
// Open Update Popup
// -------------------------
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("update-btn")) {
    activeCard = e.target.closest(".card");
    activeSaved = parseFloat(activeCard.dataset.saved);
    activeTarget = parseFloat(activeCard.dataset.target);
    updatePopup.style.display = "flex";
  }
});

closeUpdatePopup.onclick = () => updatePopup.style.display = "none";

// -------------------------
// Deposit
// -------------------------
depositBtn.onclick = (e) => {
  e.preventDefault();
  const val = parseFloat(updateAmountInput.value);
  if (isNaN(val) || val <= 0) {
    alert("Enter valid amount.");
    return;
  }
  activeSaved += val;
  addHistory("Deposited", val);
  updateUI();
};

// -------------------------
// Withdraw
// -------------------------
withdrawBtn.onclick = (e) => {
  e.preventDefault();
  const val = parseFloat(updateAmountInput.value);
  if (isNaN(val) || val <= 0) {
    alert("Enter valid amount.");
    return;
  }
  if (val > activeSaved) {
    alert("You cannot withdraw beyond your savings.");
    return;
  }
  activeSaved -= val;
  addHistory("Withdrew", val);
  updateUI();
};

// -------------------------
// Update Card UI
// -------------------------
function updateUI() {
  activeCard.dataset.saved = activeSaved;
  const percent = Math.min((activeSaved / activeTarget) * 100, 100);
  activeCard.querySelector(".saved").textContent = activeSaved.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  activeCard.querySelector(".progress-fill").style.width = percent + "%";
  activeCard.querySelector(".percentage").textContent = percent.toFixed(1) + "%";
  updateForm.reset();
  updatePopup.style.display = "none";
}

// -------------------------
// Add to History
// -------------------------
function addHistory(action, amount) {
  const now = new Date();
  const formattedAmount = amount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const formattedDate = now.toLocaleString();

  const log = `${action} ₱${formattedAmount} on ${formattedDate}`;
  let history = JSON.parse(activeCard.dataset.history || "[]");
  history.push(log);
  activeCard.dataset.history = JSON.stringify(history);
}

// -------------------------
// Open Details Popup
// -------------------------
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("details-btn")) {
    activeCard = e.target.closest(".card");
    const goalName = activeCard.querySelector("h2").textContent;
    const history = JSON.parse(activeCard.dataset.history || "[]");

    goalDetailsTitle.textContent = goalName;
    goalNameEdit.value = goalName; // auto-fill input

    goalHistory.innerHTML = "";

if (history.length === 0) {
  const li = document.createElement("li");
  li.textContent = "No history yet.";
  goalHistory.appendChild(li);
} else {
  history.forEach(item => {
    const li = document.createElement("li");
    const parts = item.split(" on "); 
    li.innerHTML = `<strong>${parts[0]}</strong><br><span style="font-size:0.85rem; color:#555;">${parts[1]}</span>`;
    goalHistory.appendChild(li);
  });
}

    goalDetailsPopup.style.display = "flex";
  }
});

// -------------------------
// Close Details Popup
// -------------------------
closeGoalDetails.addEventListener("click", () => {
  goalDetailsPopup.style.display = "none";
});

// -------------------------
// Save Goal Name
// -------------------------
saveGoalNameBtn.onclick = () => {
  const newName = goalNameEdit.value.trim();
  if (!newName) {
    alert("Goal name cannot be empty.");
    return;
  }

  activeCard.querySelector("h2").textContent = newName;
  goalDetailsTitle.textContent = newName;
  alert("Goal name updated!");
};

// -------------------------
// Delete Goal
// -------------------------
deleteGoalBtn.onclick = () => {
  if (activeCard) {
    activeCard.remove();
    goalDetailsPopup.style.display = "none";
    alert("Goal deleted!");
  }
};
