<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer Savings</title>
  <link rel="stylesheet" href="savings.css">
</head>
<body>
   <header>
    <div class="brand">
      <img src="name.png" alt="Finizer Logo">
    </div>
   </header>

  <h1>Savings</h1>
  <div class="savings-container">
    <div class="card">
      <h2>Emergency Fund</h2>
      <div class="progress"><div class="progress-fill" style="width: 41%;"></div></div>
      <div class="percentage">41%</div>
      <div class="amount">₱10,250.00 / ₱25,000.00</div>
      <div class="btn-group">
        <button class="btn btn-add">Add</button>
        <button class="btn btn-details">Details</button>
      </div>
    </div>

    <div class="card">
      <h2>New PC</h2>
      <div class="progress"><div class="progress-fill" style="width: 31%;"></div></div>
      <div class="percentage">31%</div>
      <div class="amount">₱17,671.00 / ₱57,078.00</div>
      <div class="btn-group">
        <button class="btn btn-add">Add</button>
        <button class="btn btn-details">Details</button>
      </div>
    </div>

    <div class="card">
      <h2>Travel Fund</h2>
      <div class="progress"><div class="progress-fill" style="width: 41%;"></div></div>
      <div class="percentage">41%</div>
      <div class="amount">₱6,432.00 / ₱15,000.00</div>
      <div class="btn-group">
        <button class="btn btn-add">Add</button>
        <button class="btn btn-details">Details</button>
      </div>
    </div>
  </div>

  <!-- Floating Add Button -->
  <button class="floating-btn">＋</button>
  

   <div class="dock">
    <!-- Dashboard -->
    <a href="dashboard.php" class="icon" aria-label="Dashboard">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
      <path d="M3 11L12 3l9 8v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9z"/>
      <path d="M9 22V12h6v10"/>
    </svg>
  </a>

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



  <script src="savings.js"></script>
</body>
</html>