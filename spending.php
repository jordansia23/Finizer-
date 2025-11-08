<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finizer - Spending page</title>
  <link rel="icon" type="image/png" href="tabicon.ico">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="spending.css">
</head>
<body>
    <header>
     <div class="brand">
        <img src="name.png" alt="Finizer Logo">
      </div>
   </header>

<!-- === MAIN CONTENT === -->
  <main class="spending-container">

    <!-- Header title + button side by side -->
    <div class="spending-header">
      <h1 class="title">Daily Spending</h1>
      <button class="add-btn">+ Add Bill</button>
    </div>

    <div class="summary-cards">
      <div class="card">
        <p>Total Today</p>
        <h2>₱525.00</h2>
      </div>
      <div class="card">
        <p>Total This Week</p>
        <h2>₱3,507.00</h2>
      </div>
      <div class="card">
        <p>Total This Month</p>
        <h2>₱12,603.00</h2>
      </div>
      <div class="card">
        <p>Average Daily Spending</p>
        <h2>₱420.01/day</h2>
      </div>
    </div>

    <table class="spending-table">
  <thead>
    <tr>
      <th>Name</th>
      <th>Category</th>
      <th>Amount</th>
      <th>Date</th>
      <th>Payment Method</th>
      <th>Notes</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <tr class="total-row">
      <td colspan="2"><strong>Total</strong></td>
      <td colspan="5"><strong>₱0.00</strong></td>
    </tr>
  </tbody>
</table>

  </main>

  <!-- === Edit Bill Modal === -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <h2>Edit Bill</h2>
    <form id="editForm">
      <label for="editName">Name:</label>
      <input type="text" id="editName" required>

      <label for="editCategory">Category:</label>
      <input type="text" id="editCategory" required>

      <label for="editAmount">Amount:</label>
      <input type="number" id="editAmount" required step="0.01">

      <label for="editDate">Date:</label>
      <input type="date" id="editDate" required>

      <label for="editPayment">Payment Method:</label>
      <select id="editPayment" required>
        <option value="Cash">Cash</option>
        <option value="Gcash">Gcash</option>
        <option value="Bank">Bank</option>
        <option value="Credit Card">Credit Card</option>
        <option value="Other">Other</option>
      </select>

      <label for="editNote">Notes:</label>
      <textarea id="editNote" rows="3"></textarea>

      <div class="modal-actions">
        <button type="submit" class="save-btn">Save Changes</button>
        <button type="button" id="cancelEdit" class="cancel-btn">Cancel</button>
      </div>
    </form>
  </div>
</div>


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

<!-- === Add Expense Modal === -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal">
    <button class="close-modal" id="closeModal">&times;</button>
    <h2>Add Daily Expense</h2>

    <form id="expenseForm">
      <label>Expense Name</label>
      <input type="text" id="expenseName" placeholder="Expense Name" required>

      <div class="form-row">
        <div>
          <label>Category</label>
          <select id="category" required>
            <option value="Food">Food</option>
            <option value="Transportation">Transportation</option>
            <option value="Bills">Bills</option>
            <option value="Shopping">Shopping</option>
            <option value="Other">Other</option>
          </select>
        </div>
        <div>
          <label>Amount</label>
          <input type="number" id="amount" placeholder="₱" required>
        </div>
      </div>

      <div class="form-row">
        <div>
          <label>Date</label>
          <input type="date" id="date" required>
        </div>
        <div>
          <label>Payment Method</label>
          <select id="payment" required>
            <option value="Cash">Cash</option>
            <option value="Gcash">Gcash</option>
            <option value="Bank">Bank</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Other">Other</option>
          </select>
        </div>
      </div>

      <div>
        <label>Note</label>
        <input type="text" id="note" placeholder="Optional">
      </div>

      <div class="form-buttons">
        <button type="submit" class="save-btn">Save</button>
        <button type="button" class="cancel-btn" id="cancelModal">Cancel</button>
      </div>
    </form>
  </div>
</div>

</body>
 <script src="spending.js"></script>
</html>
