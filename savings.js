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
    console.log("Initializing savings page...");
    
    // Add Goal Button
    if (addGoalBtn) {
        addGoalBtn.addEventListener('click', function() {
            popup.style.display = "flex";
        });
    }
    
    // Close Buttons
    if (closePopup) {
        closePopup.addEventListener('click', function() {
            popup.style.display = "none";
        });
    }
    
    if (closeUpdatePopup) {
        closeUpdatePopup.addEventListener('click', function() {
            updatePopup.style.display = "none";
        });
    }
    
    if (closeDetailsBtn) {
        closeDetailsBtn.addEventListener('click', function() {
            detailsPopup.style.display = "none";
        });
    }
    
    // Update Buttons
    if (depositBtn) {
        depositBtn.addEventListener('click', handleDeposit);
    }
    
    if (withdrawBtn) {
        withdrawBtn.addEventListener('click', handleWithdraw);
    }
    
    // Details Buttons
    if (saveGoalNameBtn) {
        saveGoalNameBtn.addEventListener('click', handleSaveGoalName);
    }
    
    if (deleteGoalBtn) {
        deleteGoalBtn.addEventListener('click', handleDeleteGoal);
    }
    
    // Form submission
    const savingsForm = document.getElementById("savingsForm");
    if (savingsForm) {
        savingsForm.addEventListener('submit', handleCreateGoal);
    }
    
    // ========== ADDED: Setup Update and Details buttons ==========
    setupGoalCardButtons();
    
    // Close popups when clicking outside
    setupPopupCloseHandlers();
    
    console.log("All event listeners initialized");
});

// ========== ADDED: Setup buttons for all existing goal cards ==========
function setupGoalCardButtons() {
    const updateButtons = document.querySelectorAll('.update-btn');
    const detailsButtons = document.querySelectorAll('.details-btn');
    
    console.log(`Found ${updateButtons.length} update buttons and ${detailsButtons.length} details buttons`);
    
    // Update buttons
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log("Update button clicked");
            activeCard = this.closest('.goal-card');
            updateAmountInput.value = "";
            updatePopup.style.display = "flex";
        });
    });
    
    // Details buttons
    detailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log("Details button clicked");
            activeCard = this.closest('.goal-card');
            const goalName = activeCard.querySelector('h2').textContent;
            const history = JSON.parse(activeCard.dataset.history || "[]");
            
            goalNameDisplay.textContent = goalName;
            goalNameEdit.value = goalName;
            displayHistory(history);
            detailsPopup.style.display = "flex";
        });
    });
}

function setupPopupCloseHandlers() {
    // Close when clicking outside popup
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = "none";
            }
        });
    }
    
    if (updatePopup) {
        updatePopup.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = "none";
            }
        });
    }
    
    if (detailsPopup) {
        detailsPopup.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = "none";
            }
        });
    }
}

// ========== ADDED: Update Popup Functions ==========
function handleDeposit(e) {
    e.preventDefault();
    processTransaction("deposit");
}

function handleWithdraw(e) {
    e.preventDefault();
    processTransaction("withdraw");
}

function processTransaction(action) {
    if (!activeCard) {
        alert("No active goal selected.");
        return;
    }

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
    
    updatePopup.style.display = "none";
    updateAmountInput.value = "";
}

// ========== ADDED: Details Popup Functions ==========
function displayHistory(history) {
    if (!historyList) return;
    
    historyList.innerHTML = "";
    if (history.length === 0) {
        historyList.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">No transaction history yet.</div>';
    } else {
        history.forEach(item => {
            const div = document.createElement("div");
            div.style.cssText = "margin: 8px 0; padding: 10px; background: #3d3d3d; border-radius: 8px; border-left: 3px solid #F5B942;";
            div.innerHTML = `<strong>${item.split(" on ")[0]}</strong><br><small style="color:#888;">${item.split(" on ")[1]}</small>`;
            historyList.appendChild(div);
        });
    }
}

function handleSaveGoalName() {
    if (!activeCard) return;
    
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
            detailsPopup.style.display = "none";
            
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

// ========== ADDED: History Management ==========
function addHistory(action, amount) {
    if (!activeCard) return;
    
    const now = new Date();
    const formattedAmount = amount.toLocaleString('en-PH', { 
        minimumFractionDigits: 2, 
        maximumFractionDigits: 2 
    });
    const formattedDate = now.toLocaleString();

    const actionText = action === 'deposit' ? 'Deposited' : 'Withdrew';
    const log = `${actionText} ₱${formattedAmount} on ${formattedDate}`;
    
    let history = JSON.parse(activeCard.dataset.history || "[]");
    history.unshift(log);
    activeCard.dataset.history = JSON.stringify(history);
}

// -------------------------
// Create Goal Handler (your existing code)
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
            popup.style.display = "none";
            document.getElementById("savingsForm").reset();
            
            // Re-setup buttons for the new card
            setTimeout(() => {
                setupGoalCardButtons();
            }, 100);
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