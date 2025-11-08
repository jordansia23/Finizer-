<?php
session_start();
ob_start();

// Check if user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: landing.php");
    exit();
}

$user_id = $_SESSION['uid'];
include("db_connect.php");

// Handle form submission
if (isset($_POST["add_bill"])) {
    $bill_name = strip_tags(trim($_POST["bill_name"]));
    $bill_type = strip_tags(trim($_POST["bill_type"]));
    $amount = strip_tags(trim($_POST["amount"]));
    $due_date = strip_tags(trim($_POST["due_date"]));

    $stmt = $conn->prepare("INSERT INTO bills_table (user_id, bill_name, bill_type, amount, due_date, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("issds", $user_id, $bill_name, $bill_type, $amount, $due_date);

    if ($stmt->execute()) {
        header("Location: bills.php");
        exit();
    } else {
        echo "<script>alert('Error adding bill: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Handle mark as paid
if (isset($_GET['mark_paid'])) {
    $bill_id = intval($_GET['mark_paid']);
    $paid_date = date('Y-m-d');
    
    $stmt = $conn->prepare("UPDATE bills_table SET status = 'paid', paid_date = ? WHERE bill_id = ? AND user_id = ?");
    $stmt->bind_param("sii", $paid_date, $bill_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: bills.php");
        exit();
    }
    $stmt->close();
}

// Fetch bills data
$pending_bills_result = $conn->query("SELECT * FROM bills_table WHERE user_id = '$user_id' AND status = 'pending' ORDER BY due_date ASC");
$paid_bills_result = $conn->query("SELECT * FROM bills_table WHERE user_id = '$user_id' AND status = 'paid' ORDER BY paid_date DESC");

// Calculate totals
$total_pending = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bills_table WHERE user_id = '$user_id' AND status = 'pending'")->fetch_assoc();
$total_paid = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bills_table WHERE user_id = '$user_id' AND status = 'paid'")->fetch_assoc();

// Get next due bill
$next_due_result = $conn->query("SELECT * FROM bills_table WHERE user_id = '$user_id' AND status = 'pending' ORDER BY due_date ASC LIMIT 1");
$next_due = $next_due_result->fetch_assoc();

// Get notifications data for the bell
$notifications_result = $conn->query("
    SELECT bill_name, due_date 
    FROM bills_table 
    WHERE user_id = '$user_id' AND status = 'pending' 
    ORDER BY due_date ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer - Bills</title>
  <link rel="icon" type="image/png" href="tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="bills.css">
</head>
<body>
   <header>
     <div class="brand">
    <img src="name.png" alt="Finizer Logo">
  </div>

  <div class="header-right">
    <input type="text" class="search-bar" placeholder="Search bills...">
  <div class="header-icons">
  <div class="notif-container">
    <i class="fas fa-bell" id="notifBell"></i>
    <div class="notif-dropdown hidden" id="notifDropdown">
      <h4>Bill Reminders</h4>
      <ul id="notifList">
        <?php
        $hasNotifications = false;
        $today = date('Y-m-d');
        
        if ($notifications_result->num_rows > 0) {
            while($bill = $notifications_result->fetch_assoc()) {
                $due_date = $bill['due_date'];
                $due_timestamp = strtotime($due_date);
                $today_timestamp = strtotime($today);
                $days_until_due = ($due_timestamp - $today_timestamp) / (60 * 60 * 24);
                
                $message = '';
                $class = '';
                
                if ($days_until_due < 0) {
                    $message = "❌ {$bill['bill_name']} is OVERDUE!";
                    $class = 'overdue';
                    $hasNotifications = true;
                } elseif ($days_until_due == 0) {
                    $message = "🕒 {$bill['bill_name']} is due TODAY!";
                    $class = 'due-today';
                    $hasNotifications = true;
                } elseif ($days_until_due <= 3) {
                    $message = "⚠️ {$bill['bill_name']} is due in " . intval($days_until_due) . " day(s)!";
                    $class = 'due-soon';
                    $hasNotifications = true;
                }
                
                if ($message) {
                    echo "<li class='$class'>" . htmlspecialchars($message) . "</li>";
                }
            }
        }
        
        if (!$hasNotifications) {
            echo "<li>No upcoming bills 🎉</li>";
        }
        ?>
      </ul>
    </div>
  </div>
  <i class="fas fa-bars"></i>
</div>
  </div>
  </header>

  <h1>Bills</h1>

  <!-- bills overview -->
  <section class="bills-overview">
  <div class="overview-card">
    <div class="overview-header">
      <h2>Total this month</h2>
      <button class="add-bill-btn">+ Add Bill</button>
    </div>
    <div class="overview-stats">
      <div class="stat">
        <h3>Paid</h3>
        <p class="amount">₱<?php echo number_format($total_paid['total'], 2); ?></p>
        <span>Total: <?php echo $paid_bills_result->num_rows; ?></span>
      </div>
      <div class="stat">
        <h3>Due</h3>
        <p class="amount">₱<?php echo number_format($total_pending['total'], 2); ?></p>
        <span>Total: <?php echo $pending_bills_result->num_rows; ?></span>
      </div>
      <div class="stat">
        <h3>Overdue</h3>
        <p class="amount">₱0.00</p>
        <span>Total: 0</span>
      </div>
    </div>
    <?php if ($next_due): ?>
    <p class="next-due">Next due: <span><?php echo htmlspecialchars($next_due['bill_name']); ?> (<?php echo date('F j, Y', strtotime($next_due['due_date'])); ?>)</span></p>
    <?php else: ?>
    <p class="next-due">No upcoming bills</p>
    <?php endif; ?>
  </div>
</section>

 <!-- bills section overview -->
<section class="bills-section">
  <div class="bills-toggle">
    <button class="tab-btn active" data-tab="pending">Pending Bills</button>
    <button class="tab-btn" data-tab="paid">Paid Bills</button>
  </div>

  <div class="bills-list" id="pending-bills">
    <?php if ($pending_bills_result->num_rows > 0): ?>
      <?php while($bill = $pending_bills_result->fetch_assoc()): ?>
      <div class="bill-card">
        <div class="bill-info">
          <h3><?php echo htmlspecialchars($bill['bill_name']); ?></h3>
          <p>Type: <?php echo $bill['bill_type']; ?></p>
          <p>Due: <?php echo date('M j, Y', strtotime($bill['due_date'])); ?></p>
        </div>
        <div class="bill-right">
          <p class="bill-amount">₱<?php echo number_format($bill['amount'], 2); ?></p>
          <form action="bills.php" method="get" style="display: inline;">
    <input type="hidden" name="mark_paid" value="<?php echo $bill['bill_id']; ?>">
    <button type="submit" class="mark-paid-btn">Mark Paid</button>
</form>
        </div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="bill-card">
        <div class="bill-info">
          <h3>No pending bills</h3>
          <p>All caught up!</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="bills-list hidden" id="paid-bills">
    <?php if ($paid_bills_result->num_rows > 0): ?>
      <?php while($bill = $paid_bills_result->fetch_assoc()): ?>
      <div class="bill-card paid">
        <div class="bill-info">
          <h3><?php echo htmlspecialchars($bill['bill_name']); ?></h3>
          <p>Paid on: <?php echo date('M j, Y', strtotime($bill['paid_date'])); ?></p>
        </div>
        <p class="bill-amount">₱<?php echo number_format($bill['amount'], 2); ?></p>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="bill-card">
        <div class="bill-info">
          <h3>No paid bills</h3>
          <p>Pay some bills to see them here</p>
        </div>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Add Bill Modal -->
<div class="modal-overlay" id="modalOverlay"></div>

<div class="add-bill-modal" id="addBillModal">
  <div class="modal-content">
    <button class="close-modal" id="closeModal">&times;</button>
    <h2>Add Bill</h2>
    <form action="bills.php" method="post" id="billForm">
      <input type="text" name="bill_name" id="billName" placeholder="Name" required>
      <select name="bill_type" id="billType" required>
        <option value="">Type</option>
        <option value="Electric">Electric</option>
        <option value="Water">Water</option>
        <option value="Internet">Internet</option>
        <option value="Other">Other</option>
      </select>
      <input type="number" name="amount" id="billAmount" placeholder="Amount" step="0.01" required>
      <input type="date" name="due_date" id="billDueDate" required>
      <button type="submit" name="add_bill" class="save-btn">Save</button>
    </form>
  </div>
</div>

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
    <a href="savings.php" class="icon" aria-label="Savings">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <path d="M20 13v-2a8 8 0 0 0-16 0v2a8 8 0 0 0 16 0z"/>
        <circle cx="12" cy="9" r="1.5" fill="#F5B942"/>
      </svg>
    </a>

    <!-- Bills -->
    <a href="bills.php" class="icon active" aria-label="Bills">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
        <path d="M14 2v6h6"/>
        <line x1="9" y1="13" x2="15" y2="13"/>
        <line x1="9" y1="17" x2="15" y2="17"/>
      </svg>
    </a>

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

<script src="bills.js"></script>
</body>
</html>
<?php 
$conn->close();
?>