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

  <title>Savings - Finizer</title>
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
<<<<<<< HEAD
        <button type="button" class="btn btn-add" id="depositBtn">Deposit</button>
        <button type="button" class="btn btn-details" id="withdrawBtn">Withdraw</button>
=======
        <button type="submit" class="btn btn-add" id="depositBtn">Increase</button>
        <button type="submit" class="btn btn-details" id="withdrawBtn">Decrease</button>
>>>>>>> 34d04d5663bb4b6a1aec171b83878a92156dbb39
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

<script src="savings.js"></script>
</body>
</html>