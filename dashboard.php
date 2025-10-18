<?php
session_start();
include "db_connect.php";

// Make sure the user is logged in
if (!isset($_SESSION['uid'])) {
    header("Location: landing.php");
    exit();
}

$user_id = $_SESSION['uid'];

// Handle profile picture upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    if ($_FILES["profile_pic"]["error"] == 0) {
        $imgData = file_get_contents($_FILES["profile_pic"]["tmp_name"]);

        // Set all old pictures inactive
        $conn->query("UPDATE profile_pics SET is_active = 0 WHERE user_id = $user_id");

        // Insert new picture
        $stmt = $conn->prepare("INSERT INTO profile_pics (user_id, picture, is_active) VALUES (?, ?, 1)");
        $stmt->bind_param("ib", $user_id, $null);
        $stmt->send_long_data(1, $imgData);
        $stmt->execute();
    }
}

// Fetch active profile pic
$sql = "SELECT picture FROM profile_pics WHERE user_id = ? AND is_active = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $profilePic = 'data:image/jpeg;base64,' . base64_encode($row['picture']);
} else {
    $profilePic = "uploads/default.png"; // fallback default image
}

// Fetch user info (assuming usertable has username & email)
$sql = "SELECT username, email FROM usertable WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Finizer - Dashboard</title>
  <link rel="icon" type="image/png" href="logo-circle.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

  <header>
    <div class="brand">
      <img src="name.png" alt="Finizer Logo">
    </div>
    <nav>
      <div class="profile-menu">
<button id="profileBtn" type="button" aria-label="Profile">
  <img 
    src="<?php echo $profilePic; ?>" 
    alt="User Profile" 
    class="profile-icon"
    style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;border:none;margin:0;padding:0;"
  >
</button>
        <div class="profile-popup" id="profilePopup">
        
          <!-- Upload form -->
          <form action="" method="POST" enctype="multipart/form-data">
              <img id="previewImg" src="<?php echo $profilePic; ?>" alt="Preview" class="preview-img" style="width:120px;height:120px;border-radius:50%;">   
              
              <label for="profileUpload" class="upload-label">Change Photo</label>
              <input type="file" id="profileUpload" name="profile_pic" accept="image/*" style="display:none" onchange="this.form.submit()">
          </form>

          <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
          <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

          <form action="logout.php" method="POST">
              <button type="submit" class="logout-btn">Logout</button>
          </form>
        </div>
      </div>
    </nav>
  </header>

  <div class="popup-overlay" id="popupOverlay"></div>

  <!-- ===== DASHBOARD MAIN CONTAINER ===== -->
  <main class="dashboard-container">
    <h2 class="dashboard-title">Dashboard</h2>

    <!-- ===== DASHBOARD CARDS GRID ===== -->
    <div class="dashboard-cards">
      
      <!-- Total Balance Card -->
      <div class="card">
        <h3>Total Balance</h3>
        <p class="amount">₱65,180.00</p>
      </div>

      <!-- This Month Spend Card -->
      <div class="card">
        <h3>This Month Spend</h3>
        <p class="amount">₱15,260.00</p>
      </div>

      <!-- Upcoming Bills Card -->
      <div class="card">
        <h3>Upcoming Bills</h3>
        <p class="info">4 due</p>
      </div>

      <!-- Savings Progress Card -->
      <div class="card">
        <h3>Savings Progress</h3>
        <p class="progress">54%</p>
      </div>

      <!-- Spending by Category Card -->
      <div class="card">
        <h3>Spending by Category</h3>
        <canvas id="spendingChart"></canvas>
      </div>

      <!-- Reminders Card -->
      <div class="card reminders">
        <h3>Reminders</h3>
        
        <!-- Reminder 1 -->
        <div class="reminder">
          <p><strong>Electricity</strong><br>Due Aug 30 – ₱9,245.00</p>
          <button>Mark Paid</button>
        </div>
        
        <!-- Reminder 2 -->
        <div class="reminder">
          <p><strong>Water</strong><br>Due Aug 28 – ₱856.00</p>
          <button>Mark Paid</button>
        </div>
        
        <!-- Reminder 3 -->
        <div class="reminder">
          <p><strong>Internet</strong><br>Due Aug 25 – ₱1,200.00</p>
          <button>Mark Paid</button>
        </div>
      </div>
    </div>
  </main>

  <main class="main">
    <!-- dito yung main content mo; pwede mong palitan o lagyan ng sections -->
  </main>

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
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M20 13v-2a8 8 0 0 0-16 0v2a8 8 0 0 0 16 0z"/>
        <circle cx="12" cy="9" r="1.5" fill="#F5B942"/>
      </svg>
    </a>

    <!-- Bills -->
    <button class="icon" data-app="bills" aria-label="Bills">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
        <path d="M14 2v6h6"/>
        <line x1="9" y1="13" x2="15" y2="13"/>
        <line x1="9" y1="17" x2="15" y2="17"/>
      </svg>
    </button>

    <!-- Spending -->
    <button class="icon" data-app="spending" aria-label="Daily Spending">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <line x1="2" y1="10" x2="22" y2="10"/>
        <line x1="6" y1="15" x2="10" y2="15"/>
      </svg>
    </button>

    <!-- Insight -->
    <button class="icon" data-app="insight" aria-label="Insight">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <line x1="4" y1="20" x2="4" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="20" y1="20" x2="20" y2="14"/>
      </svg>
    </button>
  </div>

  <!-- Assistant Floating Button -->
  <div class="assistant-float" id="assistant">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <rect x="3" y="7" width="18" height="12" rx="4"/>
      <circle cx="8.5" cy="13" r="1.5" fill="#F5B942"/>
      <circle cx="15.5" cy="13" r="1.5" fill="#F5B942"/>
      <line x1="12" y1="3" x2="12" y2="7"/>
    </svg>
  </div> 

  <script src="dashboard.js"></script>
</body>
</html>
