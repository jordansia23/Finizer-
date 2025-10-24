// -------------------------
// DOM Elements
// -------------------------
const addGoalBtn = document.getElementById("addGoalBtn");
const popup = document.getElementById("popup");
const closePopup = document.getElementById("closePopup");
const goalsTableBody = document.getElementById("goalsTableBody");

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
const toggleStatusBtn = document.getElementById("toggleStatusBtn");

// Summary elements
const activeGoalsCount = document.getElementById("activeGoalsCount");
const totalSaved = document.getElementById("totalSaved");
const totalTarget = document.getElementById("totalTarget");
const overallProgress = document.getElementById("overallProgress");

let activeCard = null;
let activeRow = null;

// -------------------------
// Enhanced Notification System
// -------------------------
function showNotification(message, type = 'info') {
    const existingNotifications = document.querySelectorAll('.custom-notification');
    existingNotifications.forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `custom-notification ${type}`;
    
    const icon = type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ';
    
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">${icon}</span>
            <span class="notification-message">${message}</span>
        </div>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 30px;
        right: 30px;
        padding: 15px 20px;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        z-index: 10000;
        max-width: 350px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        animation: slideInRight 0.3s ease;
    `;
    
    if (type === 'success') {
        notification.style.background = 'linear-gradient(135deg, #4CAF50, #66BB6A)';
    } else if (type === 'error') {
        notification.style.background = 'linear-gradient(135deg, #f44336, #e57373)';
    } else {
        notification.style.background = 'linear-gradient(135deg, #2196F3, #42A5F5)';
    }
    
    document.body.appendChild(notification);
    
    // Add styles for notification content
    const style = document.createElement('style');
    if (!document.querySelector('#notification-styles')) {
        style.id = 'notification-styles';
        style.textContent = `
            .notification-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .notification-icon {
                font-size: 1.1em;
                font-weight: bold;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 4000);
}

// -------------------------
// Initialize Event Listeners
// -------------------------
document.addEventListener('DOMContentLoaded', function() {
    console.log("Savings page fully loaded");
    
    // Calculate initial summary
    calculateSummary();
    
    // Add Goal Button
    addGoalBtn.addEventListener('click', function() {
        popup.style.display = "flex";
    });
    
    // Close Buttons
    closePopup.addEventListener('click', function() {
        popup.style.display = "none";
    });
    
    closeUpdatePopup.addEventListener('click', function() {
        updatePopup.style.display = "none";
    });
    
    closeDetailsBtn.addEventListener('click', function() {
        detailsPopup.style.display = "none";
    });
    
    // Event delegation for table buttons
    goalsTableBody.addEventListener('click', function(e) {
        const target = e.target;
        
        if (target.classList.contains('update-btn')) {
            handleUpdateClick(target);
        } else if (target.classList.contains('details-btn')) {
            handleDetailsClick(target);
        } else if (target.classList.contains('status-toggle-btn')) {
            handleStatusToggle(target);
        }
    });
    
    // Deposit/Withdraw buttons
    depositBtn.addEventListener('click', handleDeposit);
    withdrawBtn.addEventListener('click', handleWithdraw);
    
    // Details action buttons
    saveGoalNameBtn.addEventListener('click', handleSaveGoalName);
    toggleStatusBtn.addEventListener('click', handleToggleStatus);
    
    // Form submission
    document.getElementById("savingsForm").addEventListener('submit', handleCreateGoal);
    
    // Close popups when clicking outside
    setupPopupCloseHandlers();
});

function setupPopupCloseHandlers() {
    popup.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = "none";
    });
    
    updatePopup.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = "none";
    });
    
    detailsPopup.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = "none";
    });
}

// -------------------------
// Summary Calculation
// -------------------------
function calculateSummary() {
    const rows = document.querySelectorAll('.goal-row');
    let activeCount = 0;
    let totalSavedAmount = 0;
    let totalTargetAmount = 0;
    
    rows.forEach(row => {
        const saved = parseFloat(row.dataset.saved);
        const target = parseFloat(row.dataset.target);
        const status = row.dataset.status;
        
        if (status === 'active') {
            activeCount++;
            totalSavedAmount += saved;
            totalTargetAmount += target;
        }
    });
    
    const progress = totalTargetAmount > 0 ? (totalSavedAmount / totalTargetAmount * 100) : 0;
    
    activeGoalsCount.textContent = activeCount;
    totalSaved.textContent = `₱${totalSavedAmount.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
    totalTarget.textContent = `₱${totalTargetAmount.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`;
    overallProgress.textContent = `${progress.toFixed(1)}%`;
}

// -------------------------
// Table Button Handlers
// -------------------------
function handleUpdateClick(button) {
    activeRow = button.closest('.goal-row');
    const savingsId = activeRow.dataset.id;
    const currentSaved = parseFloat(activeRow.dataset.saved);
    
    activeCard = { dataset: { id: savingsId, saved: currentSaved, target: activeRow.dataset.target } };
    updateAmountInput.value = "";
    updateAmountInput.focus();
    updatePopup.style.display = "flex";
}

function handleDetailsClick(button) {
    activeRow = button.closest('.goal-row');
    const savingsId = activeRow.dataset.id;
    const goalName = activeRow.dataset.name;
    const status = activeRow.dataset.status;
    
    activeCard = { dataset: { id: savingsId } };
    
    goalNameDisplay.textContent = goalName;
    goalNameEdit.value = goalName;
    toggleStatusBtn.textContent = status === 'active' ? 'Archive Goal' : 'Activate Goal';
    toggleStatusBtn.className = status === 'active' ? 'status-btn archive' : 'status-btn activate';
    
    // Fetch and display history
    fetchGoalHistory(savingsId);
    detailsPopup.style.display = "flex";
}

function handleStatusToggle(button) {
    const row = button.closest('.goal-row');
    const savingsId = row.dataset.id;
    const currentStatus = button.dataset.status;
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    if (!confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'archive'} this goal?`)) {
        return;
    }
    
    fetch("savings_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            savings_id: savingsId, 
            action: "update_status", 
            new_status: newStatus 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const statusBadge = row.querySelector('.status-badge');
            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            statusBadge.className = `status-badge status-${newStatus}`;
            
            button.textContent = newStatus === 'active' ? 'Archive' : 'Activate';
            button.dataset.status = newStatus;
            
            // Update row dataset
            row.dataset.status = newStatus;
            
            // Update summary
            calculateSummary();
            
            showNotification(`Goal ${newStatus === 'active' ? 'activated' : 'archived'} successfully!`, 'success');
        } else {
            showNotification(data.error || "Failed to update status.", 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Error updating status.", 'error');
    });
}

// -------------------------
// Update Popup Functions
// -------------------------
function handleDeposit(e) {
    e.preventDefault();
    processTransaction("deposit");
}

function handleWithdraw(e) {
    e.preventDefault();
    processTransaction("withdraw");
}

function processTransaction(action) {
    if (!activeCard || !activeRow) {
        showNotification("No active goal selected.", 'error');
        return;
    }

    const amount = parseFloat(updateAmountInput.value);
    if (isNaN(amount) || amount <= 0) {
        showNotification("Please enter a valid amount.", 'error');
        return;
    }

    const currentSaved = parseFloat(activeCard.dataset.saved);
    
    if (action === "withdraw" && amount > currentSaved) {
        showNotification("Cannot withdraw more than current savings.", 'error');
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
            updateRowAfterTransaction(data.amt_svd, action, amount);
            showNotification(`Successfully ${action}ed ₱${amount.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}`, 'success');
        } else {
            showNotification(data.error || "Transaction failed.", 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Error processing transaction.", 'error');
    });
}

function updateRowAfterTransaction(newAmount, action, amount) {
    const targetAmount = parseFloat(activeCard.dataset.target);
    
    // Update row data
    activeRow.dataset.saved = newAmount;
    
    // Update displayed values
    const percent = Math.min((newAmount / targetAmount) * 100, 100);
    
    // Update table cells
    activeRow.cells[2].textContent = `₱${newAmount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    
    const progressFill = activeRow.querySelector('.progress-fill');
    const progressText = activeRow.querySelector('.progress-text');
    progressFill.style.width = percent + "%";
    progressText.textContent = percent.toFixed(1) + "%";
    
    // Update the "Last Updated" date
    activeRow.cells[6].textContent = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    
    // Update summary
    calculateSummary();
    
    updatePopup.style.display = "none";
    updateAmountInput.value = "";
}

// -------------------------
// Details Popup Functions
// -------------------------
function fetchGoalHistory(savingsId) {
    historyList.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">Loading history...</div>';
    
    fetch(`get_savings_history.php?savings_id=${savingsId}`)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            displayHistory(data.history);
        } else {
            displayHistory([]);
        }
    })
    .catch(err => {
        console.error(err);
        displayHistory([]);
    });
}

function displayHistory(history) {
    historyList.innerHTML = "";
    
    if (history.length === 0) {
        historyList.innerHTML = '<div style="padding: 20px; text-align: center; color: #666;">No transaction history yet.</div>';
    } else {
        history.forEach(item => {
            const div = document.createElement("div");
            div.style.cssText = "margin: 8px 0; padding: 10px; background: #3d3d3d; border-radius: 8px; border-left: 3px solid #F5B942;";
            
            const actionColor = item.action === 'deposit' ? '#4CAF50' : '#F44336';
            const actionText = item.action === 'deposit' ? 'Deposited' : 'Withdrew';
            
            div.innerHTML = `
                <strong style="color: ${actionColor};">${actionText} ₱${parseFloat(item.amount).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}</strong>
                <br>
                <small style="color:#888;">${new Date(item.date).toLocaleString()}</small>
            `;
            historyList.appendChild(div);
        });
    }
}

function handleSaveGoalName() {
    if (!activeCard || !activeRow) return;
    
    const newName = goalNameEdit.value.trim();
    if (!newName) {
        showNotification("Goal name cannot be empty.", 'error');
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
            activeRow.cells[0].textContent = newName;
            activeRow.dataset.name = newName;
            goalNameDisplay.textContent = newName;
            showNotification("Goal name updated successfully!", 'success');
        } else {
            showNotification(data.error || "Failed to update goal name.", 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Error updating goal name.", 'error');
    });
}

function handleToggleStatus() {
    if (!activeCard || !activeRow) return;
    
    const currentStatus = toggleStatusBtn.classList.contains('archive') ? 'active' : 'inactive';
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    if (!confirm(`Are you sure you want to ${newStatus === 'active' ? 'activate' : 'archive'} this goal?`)) {
        return;
    }
    
    fetch("savings_update.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ 
            savings_id: activeCard.dataset.id, 
            action: "update_status", 
            new_status: newStatus 
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update the status button in the table
            const statusBtn = activeRow.querySelector('.status-toggle-btn');
            const statusBadge = activeRow.querySelector('.status-badge');
            
            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            statusBadge.className = `status-badge status-${newStatus}`;
            statusBtn.textContent = newStatus === 'active' ? 'Archive' : 'Activate';
            statusBtn.dataset.status = newStatus;
            
            // Update row dataset
            activeRow.dataset.status = newStatus;
            
            // Update the details popup button
            toggleStatusBtn.textContent = newStatus === 'active' ? 'Archive Goal' : 'Activate Goal';
            toggleStatusBtn.className = newStatus === 'active' ? 'status-btn archive' : 'status-btn activate';
            
            // Update summary
            calculateSummary();
            
            showNotification(`Goal ${newStatus === 'active' ? 'activated' : 'archived'} successfully!`, 'success');
        } else {
            showNotification(data.error || "Failed to update status.", 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Error updating status.", 'error');
    });
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
        showNotification("Please enter valid goal name and amounts.", 'error');
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
            addNewGoalRow(data);
            popup.style.display = "none";
            document.getElementById("savingsForm").reset();
            calculateSummary();
            showNotification("Goal created successfully!", 'success');
        } else {
            showNotification(data.error || "Error creating goal.", 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showNotification("Error connecting to server.", 'error');
    });
}

function addNewGoalRow(data) {
    const noGoals = document.querySelector('.no-goals');
    if (noGoals) noGoals.remove();

    const percent = Math.min((data.amt_svd / data.target_amt) * 100, 100);
    
    const newRow = document.createElement("tr");
    newRow.className = "goal-row";
    newRow.dataset.id = data.savings_id;
    newRow.dataset.saved = data.amt_svd;
    newRow.dataset.target = data.target_amt;
    newRow.dataset.name = data.goal_name;
    newRow.dataset.status = 'active';

    newRow.innerHTML = `
        <td>${data.goal_name}</td>
        <td>₱${data.target_amt.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
        <td>₱${data.amt_svd.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})}</td>
        <td>
            <div class="progress-bar">
                <div class="progress-fill" style="width:${percent}%"></div>
                <span class="progress-text">${percent.toFixed(1)}%</span>
            </div>
        </td>
        <td><span class="status-badge status-active">Active</span></td>
        <td>${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
        <td>${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
        <td>
            <button class="btn btn-sm btn-add update-btn">Update</button>
            <button class="btn btn-sm btn-details details-btn">Details</button>
            <button class="btn btn-sm status-toggle-btn" data-status="active">Archive</button>
        </td>
    `;

    goalsTableBody.prepend(newRow);
}