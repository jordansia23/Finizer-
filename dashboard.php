<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Finizer - Smart Personal Finance Assistant</title>
  <link rel="icon" type="image/png" href="logo-circle.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>
  <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Popup Profile Menu</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <div class="brand">
      <img src="name.png" alt="Finizer Logo">
    </div>
    <nav>
      <div class="profile-menu">
        <button id="profileBtn">
          <img src="user.webp" alt="User Profile" class="profile-icon">
        </button>
        <div class="profile-popup" id="profilePopup">
        
          <!-- Upload input -->
          <img id="previewImg" src="user.webp" alt="Preview" class="preview-img">   
          
          <label for="profileUpload" class="upload-label">Change Photo</label>
          <input type="file" id="profileUpload" accept="image/*" style="display:none">

          <p><strong>Username:</strong> Jordan</p>
          <p><strong>Email:</strong> jordansia@email.com</p>

          <button class="logout-btn">Logout</button>
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
    <button class="icon" data-app="dashboard" aria-label="Dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <path d="M3 11L12 3l9 8v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9z"/>
        <path d="M9 22V12h6v10"/>
      </svg>
    </button>

    <!-- Savings -->
    <button class="icon" data-app="savings" aria-label="Savings">
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
