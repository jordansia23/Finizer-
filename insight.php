<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finizer</title>
    <link rel="stylesheet" href="insight.css"> 
</head>
<body>
  <header>
     <div class="brand">
        <img src="name.png" alt="Finizer Logo">
      </div>
   </header>
   

   <!-- === Dock (navigation) === -->
<div class="dock">
    <a href="dashboard.php" class="icon" aria-label="Dashboard">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M3 11L12 3l9 8v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-9z"/>
        <path d="M9 22V12h6v10"/>
      </svg>
    </a>

    <a href="savings.php" class="icon active" aria-label="Savings">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M20 13v-2a8 8 0 0 0-16 0v2a8 8 0 0 0 16 0z"/>
        <circle cx="12" cy="9" r="1.5" fill="#F5B942"/>
      </svg>
    </a>

    <a href="bills.php" class="icon" aria-label="Bills">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path d="M6 2h9l5 5v15a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
        <path d="M14 2v6h6"/>
        <line x1="9" y1="13" x2="15" y2="13"/>
        <line x1="9" y1="17" x2="15" y2="17"/>
      </svg>
    </a>

    <a href="spending.php" class="icon" data-app="spending" aria-label="Daily Spending">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <line x1="2" y1="10" x2="22" y2="10"/>
        <line x1="6" y1="15" x2="10" y2="15"/>
      </svg>
    </a>

    <button class="icon" data-app="insight" aria-label="Insight">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox= "0 0 24 24">
        <line x1="4" y1="20" x2="4" y2="10"/>
        <line x1="12" y1="20" x2="12" y2="4"/>
        <line x1="20" y1="20" x2="20" y2="14"/>
      </svg>
    </button>
</div>

 <!-- === INSIGHTS SECTION === -->
    <main class="insight-page">
    <h1 class="page-title">INSIGHTS</h1>

    <div class="insights">
      <!-- 🏷️ Bills Section -->
      <div class="card">
        <h2>Bills</h2>
        <div class="chart-container">
          <canvas id="billsChart"></canvas>
        </div>
        <div class="footer">Bills increased by 12% compared to last month</div>
      </div>

      <!-- 🏷️ Savings Section -->
      <div class="card">
        <h2>Savings</h2>
        <div class="chart-container">
          <canvas id="savingsChart"></canvas>
        </div>
        <div class="footer">You saved ₱1,200 more than last month</div>
      </div>

      <!-- 🏷️ Daily Spending Section -->
      <div class="card">
        <h2>Daily Spending</h2>
        <div class="chart-container">
          <canvas id="spendingChart"></canvas>
        </div>
        <div class="footer">You spent 40% on food this month</div>
      </div>
    </div>
  </main>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="insight.js"></script> 
</body>
</html>
