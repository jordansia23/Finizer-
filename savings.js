// -------------------------
// DOM Elements
// -------------------------
const addGoalBtn = document.getElementById("addGoalBtn");
const popup = document.getElementById("popup");
const closePopup = document.getElementById("closePopup");
const goalsContainer = document.getElementById("goalsContainer");

const updatePopup = document.getElementById("updatePopup");
const updateAmountInput = document.getElementById("updateAmount");
const depositBtn = document.getElementById("depositBtn");
const withdrawBtn = document.getElementById("withdrawBtn");
const closeUpdatePopup = document.getElementById("closeUpdatePopup");

const detailsPopup = document.getElementById("detailsPopup");
const closeDetailsBtn = document.getElementById("closeDetailsBtn");
const goalNameDisplay = document.getElementById("goalNameDisplay");
const historyList = document.getElementById("historyList");

const goalNameEdit = document.getElementById("goalNameEdit");
const saveGoalNameBtn = document.getElementById("saveGoalNameBtn");
const deleteGoalBtn = document.getElementById("deleteGoalBtn");

let activeCard = null;

// -------------------------
// Initialize Event Listeners
// -------------------------
document.addEventListener('DOMContentLoaded', function() {
    initializeEventListeners();
});

function initializeEventListeners() {
    // Popup triggers
    addGoalBtn.addEventListener('click', () => showPopup(popup));
    closePopup.addEventListener('click', () => hidePopup(popup));
    closeUpdatePopup.addEventListener('click', () => hidePopup(updatePopup));
    closeDetailsBtn.addEventListener('click', () => hidePopup(detailsPopup));
    
    // Form handlers
    setupFormHandlers();
    
    // Delegated events for dynamic content
    setupDelegatedEvents();
    
    // Close popups when clicking outside
    setupPopupCloseHandlers();
}

function setupFormHandlers() {
    // Create goal form
    const savingsForm = document.getElementById("savingsForm");
    savingsForm.addEventListener('submit', handleCreateGoal);
    
    // Update buttons
    depositBtn.addEventListener('click', handleDeposit);
    withdrawBtn.addEventListener('click', handleWithdraw);
    
    // Details buttons
    saveGoalNameBtn.addEventListener('click', handleSaveGoalName);
    deleteGoalBtn.addEventListener('click', handleDeleteGoal);
}

function setupDelegatedEvents() {
    goalsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains("update-btn")) {
            openUpdatePopup(e.target);
        }
        if (e.target.classList.contains("details-btn")) {
            openDetailsPopup(e.target);
        }
    });
}

function setupPopupCloseHandlers() {
    window.addEventListener('click', function(e) {
        if (e.target === popup) hidePopup(popup);
        if (e.target === updatePopup) hidePopup(updatePopup);
        if (e.target === detailsPopup) hidePopup(detailsPopup);
    });
}

// -------------------------
// Popup Utilities
// -------------------------
function showPopup(popupElement) {
    popupElement.style.display = "flex";
}

function hidePopup(popupElement) {
    popupElement.style.display = "none";
}

// -------------------------
// Create Goal Handler
// -------------------------
function handleCreateGoal(e) {
    e.preventDefault();

    const goalName = document.getElementById("goalName").value.trim();
    const targetAmt = parseFloat(document.getElementById("goalTarget").value);
    const amtSvd = parseFloat(document.getElementById("goalSaved").value) || 0;

    if (!goalName || isNaN(targetAmt) || targetAmt <= 0 || amtSvd < 0) {
        alert("Please enter valid goal name and amounts.");
        return;
    }

    const formData = new FormData();
    formData.append("create_goal", "1");
    formData.append("goal_name", goalName);
    formData.append("target_amt", targetAmt);
    formData.append("amt_svd", amtSvd);

    fetch("savings.php", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            addNewGoalCard(data);
            hidePopup(popup);
            document.getElementById("savingsForm").reset();
        } else {
            alert(data.error || "Error creating goal.");
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error connecting to server.");
    });
}

function addNewGoalCard(data) {
    // Remove no-goals message if it exists
    const noGoals = document.querySelector('.no-goals');
    if (noGoals) noGoals.remove();

    const newCard = document.createElement("div");
    newCard.className = "card goal-card";
    newCard.dataset.id = data.savings_id;
    newCard.dataset.saved = data.amt_svd;
    newCard.dataset.target = data.target_amt;
    newCard.dataset.history = "[]";

    const percent = Math.min((data.amt_svd / data.target_amt) * 100, 100);

    newCard.innerHTML = `
        <h2>${data.goal_name}</h2>
        <p class="amount">Target: ₱${data.target_amt.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}</p>
        <p class="amount">Saved: <span class="saved">₱${data.amt_svd.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}</span></p>
        <div class="progress">
            <div class="progress-fill" style="width:${percent}%"></div>
        </div>
        <p class="percentage">${percent.toFixed(1)}%</p>
        <p class="amount">Created: ${new Date().toLocaleDateString()}</p>
        <p class="amount">Updated: ${new Date().toLocaleDateString()}</p>
        <div class="btn-group">
            <button class="btn btn-add update-btn">Update</button>
            <button class="btn btn-details details-btn">Details</button>
        </div>
    `;

    goalsContainer.prepend(newCard);
}

// -------------------------
// Update Popup Handlers
// -------------------------
function openUpdatePopup(button) {
    activeCard = button.closest(".goal-card");
    updateAmountInput.value = "";
    showPopup(updatePopup);
}

function handleDeposit(e) {
    e.preventDefault();
    processTransaction("deposit");
}

function handleWithdraw(e) {
    e.preventDefault();
    processTransaction("withdraw");
}

function processTransaction(action) {
    const amount = parseFloat(updateAmountInput.value);
    if (isNaN(amount) || amount <= 0) {
        alert("Please enter a valid amount.");
        return;
    }

    const currentSaved = parseFloat(activeCard.dataset.saved);
    
    if (action === "withdraw" && amount > currentSaved) {
        alert("Cannot withdraw more than current savings.");
        return;
    }

    fetch("savings_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            savings_id: activeCard.dataset.id, 
            action: action, 
            amount: amount 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCardAfterTransaction(data.amt_svd, action, amount);
        } else {
            alert(data.error || "Transaction failed.");
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error processing transaction.");
    });
}

function updateCardAfterTransaction(newAmount, action, amount) {
    const targetAmount = parseFloat(activeCard.dataset.target);
    
    // Update card data
    activeCard.dataset.saved = newAmount;
    
    // Update displayed values
    const percent = Math.min((newAmount / targetAmount) * 100, 100);
    activeCard.querySelector(".saved").textContent = 
        `₱${newAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    activeCard.querySelector(".progress-fill").style.width = percent + "%";
    activeCard.querySelector(".percentage").textContent = percent.toFixed(1) + "%";
    
    // Update the "Updated" date
    const amountElements = activeCard.querySelectorAll('.amount');
    amountElements[amountElements.length - 1].textContent = "Updated: " + new Date().toLocaleDateString();
    
    // Add to history
    addHistory(action, amount);
    
    hidePopup(updatePopup);
    updateAmountInput.value = "";
}

// -------------------------
// Details Popup Handlers
// -------------------------
function openDetailsPopup(button) {
    activeCard = button.closest(".goal-card");
    const goalName = activeCard.querySelector("h2").textContent;
    const history = JSON.parse(activeCard.dataset.history || "[]");

    goalNameDisplay.textContent = goalName;
    goalNameEdit.value = goalName;
    displayHistory(history);
    showPopup(detailsPopup);
}

function displayHistory(history) {
    historyList.innerHTML = "";
    if (history.length === 0) {
        const li = document.createElement("li");
        li.textContent = "No transaction history yet.";
        li.style.padding = "20px";
        li.style.textAlign = "center";
        li.style.color = "#666";
        historyList.appendChild(li);
    } else {
        history.forEach(item => {
            const li = document.createElement("li");
            const parts = item.split(" on ");
            li.innerHTML = `<div style="margin: 5px 0;">
                <strong>${parts[0]}</strong><br>
                <span style="font-size:0.85rem; color:#555;">${parts[1]}</span>
            </div>`;
            historyList.appendChild(li);
        });
    }
}

function handleSaveGoalName() {
    const newName = goalNameEdit.value.trim();
    if (!newName) {
        alert("Goal name cannot be empty.");
        return;
    }

    fetch("savings_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            savings_id: activeCard.dataset.id, 
            action: "update_name", 
            new_name: newName 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            activeCard.querySelector("h2").textContent = newName;
            goalNameDisplay.textContent = newName;
            alert("Goal name updated successfully!");
        } else {
            alert(data.error || "Failed to update goal name.");
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error updating goal name.");
    });
}

function handleDeleteGoal() {
    if (!activeCard) return;
    
    if (!confirm("Are you sure you want to delete this goal? This action cannot be undone.")) {
        return;
    }

    fetch("savings_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            savings_id: activeCard.dataset.id, 
            action: "delete" 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            activeCard.remove();
            hidePopup(detailsPopup);
            
            // Show no goals message if no cards left
            if (document.querySelectorAll('.goal-card').length === 0) {
                goalsContainer.innerHTML = '<div class="no-goals">No savings goals yet. Click the + button to create one!</div>';
            }
            
            alert("Goal deleted successfully!");
        } else {
            alert(data.error || "Failed to delete goal.");
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error deleting goal.");
    });
}

// -------------------------
// History Management
// -------------------------
function addHistory(action, amount) {
    const now = new Date();
    const formattedAmount = amount.toLocaleString('en-PH', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
    const formattedDate = now.toLocaleString();

    const actionText = action === 'deposit' ? 'Deposited' : 'Withdrew';
    const log = `${actionText} ₱${formattedAmount} on ${formattedDate}`;
    
    let history = JSON.parse(activeCard.dataset.history || "[]");
    history.unshift(log); // Add to beginning
    activeCard.dataset.history = JSON.stringify(history);
}