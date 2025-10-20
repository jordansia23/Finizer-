<?php
session_start();
include "db_connect.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: landing.php");
    exit();
}

$user_id = $_SESSION['uid'];

// Handle adding a new goal
if (isset($_POST['create_goal'])) {
    $goal_name = $_POST['goal_name'];
    $target_amt = $_POST['target_amt'];
    $amt_svd = $_POST['amt_svd'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO savings_table (user_id, goal_name, target_amt, amt_svd) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdd", $user_id, $goal_name, $target_amt, $amt_svd);
    $stmt->execute();

    $new_id = $stmt->insert_id;
    $stmt->close();

    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode([
            'success' => true,
            'savings_id' => $new_id,
            'goal_name' => $goal_name,
            'target_amt' => $target_amt,
            'amt_svd' => $amt_svd,
            'created_at' => date("Y-m-d H:i:s"),
            'uploaded_at' => date("Y-m-d H:i:s")
        ]);
        exit();
    }

    header("Location: savings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer - Savings</title>
  <link rel="icon" type="image/png" href="tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="savings.css">
</head>
<body>
   <header>
    <div class="brand">
      <img src="name.png" alt="Finizer Logo">
    </div>
   </header>

   <h1>Savings Goals</h1>

   <!-- Add Goal Popup -->
<div class="popup-overlay" id="popup">
  <div class="popup-content">
    <h2>Add Savings Goal</h2>
    <form id="savingsForm" method="POST">
      <input type="text" name="goal_name" id="goalName" placeholder="Goal name (e.g. Laptop)" required>
      <input type="number" step="0.01" name="target_amt" id="goalTarget" placeholder="Target Amount (₱)" required>
      <input type="number" step="0.01" name="amt_svd" id="goalSaved" placeholder="Already Saved (₱)" value="0">
      <div class="popup-buttons">
        <button type="submit" name="create_goal" class="btn btn-add">Save</button>
        <button type="button" class="btn btn-details" id="closePopup">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- Update Popup -->
<div class="popup-overlay" id="updatePopup">
  <div class="popup-content">
    <h2>Update Savings</h2>
    <form id="updateForm">
      <input type="number" id="updateAmount" placeholder="Enter Amount (₱)" required>
      <div class="popup-buttons">
        <button type="button" class="btn btn-add" id="depositBtn">Deposit</button>
        <button type="button" class="btn btn-details" id="withdrawBtn">Withdraw</button>
        <button type="button" class="btn btn-details" id="closeUpdatePopup">Cancel</button>
      </div>
    </form>
  </div>
</div>

<div class="savings-container" id="goalsContainer">
    <?php
    $query = $conn->prepare("SELECT savings_id, goal_name, target_amt, amt_svd, created_at, uploaded_at FROM savings_table WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 0) {
        echo '<div class="no-goals" id="noGoalsMessage">No savings goals yet. Click the + button to create one!</div>';
    }

    while ($row = $result->fetch_assoc()) {
        // Fetch history for this goal
        $history_stmt = $conn->prepare("SELECT amount, action, created_at FROM savings_history WHERE savings_id = ? ORDER BY created_at DESC");
        $history_stmt->bind_param("i", $row['savings_id']);
        $history_stmt->execute();
        $history_result = $history_stmt->get_result();

        $history_array = [];
        while ($hist = $history_result->fetch_assoc()) {
            $action_text = $hist['action'] == 'deposit' ? 'Deposited' : 'Withdrew';
            $history_array[] = $action_text . " ₱" . number_format($hist['amount'],2) . " on " . $hist['created_at'];
        }
        $history_json = htmlspecialchars(json_encode($history_array));

        $percent = ($row['target_amt'] > 0) ? min(($row['amt_svd'] / $row['target_amt']) * 100, 100) : 0;

        echo '<div class="card goal-card" 
                   data-id="'.$row['savings_id'].'" 
                   data-saved="'.$row['amt_svd'].'" 
                   data-target="'.$row['target_amt'].'"
                   data-history="'.$history_json.'">
                <h2>'.htmlspecialchars($row['goal_name']).'</h2>
                <p class="amount">Target: ₱'.number_format($row['target_amt'], 2).'</p>
                <p class="amount">Saved: <span class="saved">₱'.number_format($row['amt_svd'], 2).'</span></p>
                <div class="progress">
                  <div class="progress-fill" style="width: '.$percent.'%"></div>
                </div>
                <p class="percentage">'.number_format($percent, 1).'%</p>
                <p class="amount">Created: '.date('M j, Y', strtotime($row['created_at'])).'</p>
                <p class="amount">Updated: '.date('M j, Y', strtotime($row['uploaded_at'])).'</p>
                <div class="btn-group">
                    <button class="btn btn-add update-btn" type="button">Update</button>
                    <button class="btn btn-details details-btn" type="button">Details</button>
                </div>
            </div>';
        $history_stmt->close();
    }
    $query->close();
    ?>
</div>

<!-- Floating Add Button -->
<button class="floating-btn" id="addGoalBtn" type="button">＋</button>
  
<!-- Dock (navigation) -->
<div class="dock">
    <!-- Dashboard -->
    <a href="dashboard.php" class="icon" aria-label="Dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M3 11L12 3l9 8v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9z"/>
        <path d="M9 22V12h6v10"/>
      </svg>
    </a>

    <!-- Savings -->
    <button class="icon active" data-app="savings" aria-label="Savings">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <path d="M20 13v-2a8 8 0 0 0-16 0v2a8 8 0 0 0 16 0z"/>
        <circle cx="12" cy="9" r="1.5" fill="#F5B942"/>
      </svg>
    </button>

    <!-- Bills -->
    <button class="icon" data-app="bills" aria-label="Bills">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
        <path d="M14 2v6h6"/>
        <line x1="9" y1="13" x2="15" y2="13"/>
        <line x1="9" y1="17" x2="15" y2="17"/>
      </svg>
    </button>

    <!-- Spending -->
    <button class="icon" data-app="spending" aria-label="Daily Spending">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <line x1="2" y1="10" x2="22" y2="10"/>
        <line x1="6" y1="15" x2="10" y2="15"/>
      </svg>
    </button>

    <!-- Insight -->
    <button class="icon" data-app="insight" aria-label="Insight">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <line x1="4" y1="20" x2="4" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="20" y1="20" x2="20" y2="14"/>
      </svg>
    </button>
</div>

<!-- Detail Popup - UPDATED WITH INPUT ABOVE, BUTTONS BELOW -->
<div class="popup-overlay" id="detailsPopup">
  <div class="popup-content">
    <button class="close-btn" id="closeDetailsBtn" type="button">×</button>
    <h2 id="goalNameDisplay">Goal Details</h2>

    <!-- Change Goal Name and Delete Goal - INPUT ABOVE, BUTTONS BELOW -->
    <div class="edit-delete-section">
      <input type="text" id="goalNameEdit" placeholder="Enter new goal name">
      <div class="button-row">
        <button type="button" id="saveGoalNameBtn">Save Name</button>
        <button type="button" id="deleteGoalBtn" class="delete-btn">Delete Goal</button>
      </div>
    </div>

    <!-- History Section -->
    <div class="history-section">
      <h3>Transaction History</h3>
      <div class="history-list" id="historyList"></div>
    </div>
  </div>
</div>

<!-- COMPLETE JAVASCRIPT EMBEDDED -->
<script>
// All JavaScript embedded directly in the file
let activeCard = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log("Savings page fully loaded");
    
    // Add Goal Button
    document.getElementById("addGoalBtn").addEventListener('click', function() {
        document.getElementById("popup").style.display = "flex";
    });
    
    // Close Buttons
    document.getElementById("closePopup").addEventListener('click', function() {
        document.getElementById("popup").style.display = "none";
    });
    
    document.getElementById("closeUpdatePopup").addEventListener('click', function() {
        document.getElementById("updatePopup").style.display = "none";
    });
    
    document.getElementById("closeDetailsBtn").addEventListener('click', function() {
        document.getElementById("detailsPopup").style.display = "none";
    });
    
    // UPDATE BUTTONS - This will make them work
    const updateButtons = document.querySelectorAll('.update-btn');
    console.log("Found update buttons:", updateButtons.length);
    
    updateButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log("UPDATE button clicked!");
            activeCard = this.closest('.goal-card');
            document.getElementById("updateAmount").value = "";
            document.getElementById("updatePopup").style.display = "flex";
        });
    });
    
    // DETAILS BUTTONS - This will make them work
    const detailsButtons = document.querySelectorAll('.details-btn');
    console.log("Found details buttons:", detailsButtons.length);
    
    detailsButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log("DETAILS button clicked!");
            activeCard = this.closest('.goal-card');
            const goalName = activeCard.querySelector('h2').textContent;
            const history = JSON.parse(activeCard.dataset.history || "[]");
            
            document.getElementById("goalNameDisplay").textContent = goalName;
            document.getElementById("goalNameEdit").value = goalName;
            displayHistory(history);
            document.getElementById("detailsPopup").style.display = "flex";
        });
    });
    
    // Deposit/Withdraw buttons
    document.getElementById("depositBtn").addEventListener('click', handleDeposit);
    document.getElementById("withdrawBtn").addEventListener('click', handleWithdraw);
    
    // Details action buttons
    document.getElementById("saveGoalNameBtn").addEventListener('click', handleSaveGoalName);
    document.getElementById("deleteGoalBtn").addEventListener('click', handleDeleteGoal);
    
    // Form submission
    document.getElementById("savingsForm").addEventListener('submit', handleCreateGoal);
    
    // Close popups when clicking outside
    setupPopupCloseHandlers();
});

function setupPopupCloseHandlers() {
    document.getElementById("popup").addEventListener('click', function(e) {
        if (e.target === this) this.style.display = "none";
    });
    
    document.getElementById("updatePopup").addEventListener('click', function(e) {
        if (e.target === this) this.style.display = "none";
    });
    
    document.getElementById("detailsPopup").addEventListener('click', function(e) {
        if (e.target === this) this.style.display = "none";
    });
}

// Update Functions
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

    const amount = parseFloat(document.getElementById("updateAmount").value);
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
    
    document.getElementById("updatePopup").style.display = "none";
    document.getElementById("updateAmount").value = "";
}

// Details Functions
function displayHistory(history) {
    const historyList = document.getElementById("historyList");
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
    
    const newName = document.getElementById("goalNameEdit").value.trim();
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
            document.getElementById("goalNameDisplay").textContent = newName;
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
            document.getElementById("detailsPopup").style.display = "none";
            
            // Show no goals message if no cards left
            if (document.querySelectorAll('.goal-card').length === 0) {
                document.getElementById("goalsContainer").innerHTML = '<div class="no-goals">No savings goals yet. Click the + button to create one!</div>';
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

// History Management
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

// Create Goal Handler
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
            document.getElementById("popup").style.display = "none";
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

    document.getElementById("goalsContainer").prepend(newCard);
}
</script>
</body>
</html>