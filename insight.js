// === FINIZER INSIGHTS CHARTS ===

// Color Palette
const gold = '#F5B942';

// === 1️⃣ Bills Bar Chart ===
new Chart(document.getElementById('billsChart'), {
  type: 'bar',
  data: {
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [{
      label: 'Amount (₱)',
      data: [5500, 7200, 9500],
      backgroundColor: gold,
      borderRadius: 8,
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      x: { ticks: { color: 'white' }, grid: { display: false } },
      y: { ticks: { color: 'white' }, grid: { color: 'rgba(255,255,255,0.05)' } }
    }
  }
});

// === 2️⃣ Savings Line Chart ===
new Chart(document.getElementById('savingsChart'), {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar'],
    datasets: [{
      label: 'Savings (₱)',
      data: [6000, 9800, 14300],
      borderColor: gold,
      backgroundColor: 'rgba(245,185,66,0.2)',
      borderWidth: 3,
      pointBackgroundColor: gold,
      tension: 0.4,
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      x: { ticks: { color: 'white' }, grid: { display: false } },
      y: { ticks: { color: 'white' }, grid: { color: 'rgba(255,255,255,0.05)' } }
    }
  }
});

// === 3️⃣ Daily Spending Pie Chart ===
new Chart(document.getElementById('spendingChart'), {
  type: 'pie',
  data: {
    labels: ['Food', 'Transportation', 'Entertainment', 'Shopping'],
    datasets: [{
      data: [40, 20, 20, 20],
      backgroundColor: [gold, '#A67C2B', '#CFA84E', '#2F2F2F'],
      borderColor: "#111",
      borderWidth: 2,
          borderRadius: 6,
    }]
  },
  options: {
    plugins: {
      legend: {
        position: 'bottom',
        labels: { 
          color: 'white', 
          padding: 15,
          boxWidth: 20,
        },
        align: 'start', // align left
      }
    }
  }
});