<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer - Bills page</title>
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
      <h4>Notifications</h4>
      <ul id="notifList"></ul>
    </div>
  </div>
  <i class="fas fa-bars"></i>
</div>
  </div>
  </header>

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
        <p class="amount">₱5,500</p>
        <span>Total: 4</span>
      </div>
      <div class="stat">
        <h3>Due</h3>
        <p class="amount">₱800</p>
        <span>Total: 1</span>
      </div>
      <div class="stat">
        <h3>Overdue</h3>
        <p class="amount">₱1,500</p>
        <span>Total: 2</span>
      </div>
    </div>
    <p class="next-due">Next due: <span>Water Bill (October 20, 2025)</span></p>
  </div>
</section>

 <!-- bills section overview -->
<section class="bills-section">
  <div class="bills-toggle">
    <button class="tab-btn active" data-tab="pending">Pending Bills</button>
    <button class="tab-btn" data-tab="paid">Paid Bills</button>
  </div>

 <div class="bills-list" id="pending-bills"></div>
<div class="bills-list hidden" id="paid-bills"></div>

</section>

<!-- Add Bill Modal -->
<div class="modal-overlay" id="modalOverlay"></div>

<div class="add-bill-modal" id="addBillModal">
  <div class="modal-content">
    <button class="close-modal" id="closeModal">&times;</button>
    <h2>Add Bill</h2>
    <form id="billForm">
      <input type="text" id="billName" placeholder="Name" required>
      <select id="billType" required>
        <option value="">Type</option>
        <option value="Electric">Electric</option>
        <option value="Water">Water</option>
        <option value="Internet">Internet</option>
        <option value="Other">Other</option>
      </select>
      <input type="number" id="billAmount" placeholder="Amount" required>
      <input type="date" id="billDueDate" required>
      <button type="submit" class="save-btn">Save</button>
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
<a href="savings.php" class="icon active" aria-label="Savings">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <path d="M20 13v-2a8 8 0 0 0-16 0v2a8 8 0 0 0 16 0z"/>
    <circle cx="12" cy="9" r="1.5" fill="#F5B942"/>
  </svg>
</a>

    <!-- Bills -->
<a href="bills.php" class="icon" aria-label="Bills">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
    <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
    <path d="M14 2v6h6"/>
    <line x1="9" y1="13" x2="15" y2="13"/>
    <line x1="9" y1="17" x2="15" y2="17"/>
  </svg>
</a>

      <!-- spending -->
    <a href="spending.php" class="icon" data-app="spending" aria-label="Daily Spending">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <line x1="2" y1="10" x2="22" y2="10"/>
        <line x1="6" y1="15" x2="10" y2="15"/>
      </svg>
    </a>

    <!-- Insight -->
    <button class="icon" data-app="insight" aria-label="Insight">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <line x1="4" y1="20" x2="4" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="20" y1="20" x2="20" y2="14"/>
      </svg>
    </button>
</div>

</body>

  <script src="bills.js"></script>
</html>
