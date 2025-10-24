<?php
session_start();
ob_start();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: landing.php");
    exit();
}

$user_id = $_SESSION['uid'];

// Handle adding a new goal
if (isset($_POST['create_goal'])) {
    include "db_connect.php";
    $goal_name = $_POST['goal_name'];
    $target_amt = $_POST['target_amt'];
    $amt_svd = $_POST['amt_svd'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO savings_table (user_id, goal_name, target_amt, amt_svd) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isdd", $user_id, $goal_name, $target_amt, $amt_svd);
    $stmt->execute();

    $new_id = $stmt->insert_id;
    $stmt->close();

    // Add initial transaction to history if amount saved > 0
    if ($amt_svd > 0) {
        $history_stmt = $conn->prepare("INSERT INTO savings_history (savings_id, amount, action) VALUES (?, ?, 'deposit')");
        $history_stmt->bind_param("id", $new_id, $amt_svd);
        $history_stmt->execute();
        $history_stmt->close();
    }

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

   <!-- Summary Section -->
   <div class="savings-summary">
        <div class="summary-card">
            <h3>Active Goals</h3>
            <p class="summary-amount" id="activeGoalsCount">0</p>
        </div>
        <div class="summary-card">
            <h3>Total Saved</h3>
            <p class="summary-amount" id="totalSaved">₱0.00</p>
        </div>
        <div class="summary-card">
            <h3>Total Target</h3>
            <p class="summary-amount" id="totalTarget">₱0.00</p>
        </div>
        <div class="summary-card">
            <h3>Overall Progress</h3>
            <p class="summary-amount" id="overallProgress">0%</p>
        </div>
   </div>

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

<!-- Savings Table -->
<div class="savings-table-container">
    <table class="savings-table">
        <thead>
            <tr>
                <th>Goal Name</th>
                <th>Target Amount</th>
                <th>Amount Saved</th>
                <th>Progress</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Last Updated</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="goalsTableBody">
            <?php
            include("db_connect.php");
            $query = $conn->prepare("SELECT savings_id, goal_name, target_amt, amt_svd, status, created_at, uploaded_at FROM savings_table WHERE user_id = ? ORDER BY created_at DESC");
            $query->bind_param("i", $user_id);
            $query->execute();
            $result = $query->get_result();

            if ($result->num_rows === 0) {
                echo '<tr><td colspan="8" class="no-goals">No savings goals yet. Click the + button to create one!</td></tr>';
            }

            while ($row = $result->fetch_assoc()) {
                $percent = ($row['target_amt'] > 0) ? min(($row['amt_svd'] / $row['target_amt']) * 100, 100) : 0;
                $status_class = $row['status'] == 'active' ? 'status-active' : 'status-inactive';
                
                echo '<tr class="goal-row" data-id="'.$row['savings_id'].'" data-saved="'.$row['amt_svd'].'" data-target="'.$row['target_amt'].'" data-name="'.htmlspecialchars($row['goal_name'], ENT_QUOTES).'" data-status="'.$row['status'].'">
                        <td>'.htmlspecialchars($row['goal_name'],ENT_QUOTES).'</td>
                        <td>₱'.number_format($row['target_amt'], 2).'</td>
                        <td>₱'.number_format($row['amt_svd'], 2).'</td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: '.$percent.'%"></div>
                                <span class="progress-text">'.number_format($percent, 1).'%</span>
                            </div>
                        </td>
                        <td><span class="status-badge '.$status_class.'">'.ucfirst($row['status']).'</span></td>
                        <td>'.date('M j, Y', strtotime($row['created_at'])).'</td>
                        <td>'.date('M j, Y', strtotime($row['uploaded_at'])).'</td>
                        <td>
                            <button class="btn btn-sm btn-add update-btn">Update</button>
                            <button class="btn btn-sm btn-details details-btn">Details</button>
                            <button class="btn btn-sm status-toggle-btn" data-status="'.$row['status'].'">'.($row['status'] == 'active' ? 'Archive' : 'Activate').'</button>
                        </td>
                    </tr>';
            }
            $query->close();
            ?>
        </tbody>
    </table>
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

<!-- Detail Popup -->
<div class="popup-overlay" id="detailsPopup">
  <div class="popup-content">
    <button class="close-btn" id="closeDetailsBtn" type="button">×</button>
    <h2 id="goalNameDisplay">Goal Details</h2>

    <div class="edit-delete-section">
      <input type="text" id="goalNameEdit" placeholder="Enter new goal name">
      <div class="button-row">
        <button type="button" id="saveGoalNameBtn">Save Name</button>
        <button type="button" id="toggleStatusBtn" class="status-btn">Archive Goal</button>
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