document.addEventListener("DOMContentLoaded", () => {
  const addBtn = document.querySelector(".add-btn");
  const modal = document.getElementById("modalOverlay");
  const closeModal = document.getElementById("closeModal");
  const cancelModal = document.getElementById("cancelModal");
  const form = document.getElementById("expenseForm");
  const tableBody = document.querySelector(".spending-table tbody");

  // === Function: Update Total ===
  function updateTotal() {
    const rows = tableBody.querySelectorAll("tr:not(.total-row)");
    let total = 0;

    rows.forEach((row) => {
      const amountCell = row.querySelector("td:nth-child(3)");
      if (amountCell) {
        const value = parseFloat(amountCell.textContent.replace("₱", "")) || 0;
        total += value;
      }
    });

    const totalCell = tableBody.querySelector(".total-row td:last-child");
    if (totalCell) totalCell.textContent = `₱${total.toFixed(2)}`;
  }

  // === Open Modal ===
  addBtn.addEventListener("click", () => modal.classList.add("show"));

  // === Close Modal ===
  const close = () => modal.classList.remove("show");
  closeModal.addEventListener("click", close);
  cancelModal.addEventListener("click", close);

  // === Add Expense Row ===
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const name = document.getElementById("expenseName").value;
    const category = document.getElementById("category").value;
    const amount = parseFloat(document.getElementById("amount").value).toFixed(2);
    const date = document.getElementById("date").value;
    const payment = document.getElementById("payment").value;
    const note = document.getElementById("note").value || "-";

    const totalRow = tableBody.querySelector(".total-row");
    const newRow = document.createElement("tr");

    newRow.innerHTML = `
      <td>${name}</td>
      <td>${category}</td>
      <td>₱${amount}</td>
      <td>${date}</td>
      <td>${payment}</td>
      <td>${note}</td>
      <td>
        <a href="#" class="edit" style="color:#f5b942; font-weight:600; text-decoration:none;">Edit</a> |
        <a href="#" class="delete" style="color:#ff4d4d; font-weight:600; text-decoration:none;">Delete</a>
      </td>
    `;

    tableBody.insertBefore(newRow, totalRow);
    form.reset();
    close();
    updateTotal();
  });

  // === EDIT FUNCTIONALITY ===
  document.addEventListener("click", (e) => {
    if (e.target.matches(".edit")) {
      e.preventDefault();

      const row = e.target.closest("tr");
      const cells = row.querySelectorAll("td");

      // Fill form with current row values
      document.getElementById("editName").value = cells[0].textContent;
      document.getElementById("editCategory").value = cells[1].textContent;
      document.getElementById("editAmount").value = parseFloat(cells[2].textContent.replace("₱", ""));
      document.getElementById("editDate").value = cells[3].textContent;
      document.getElementById("editPayment").value = cells[4].textContent;
      document.getElementById("editNote").value = cells[5].textContent === "-" ? "" : cells[5].textContent;

      // Show modal
      document.getElementById("editModal").classList.add("show");

      const editForm = document.getElementById("editForm");
      const cancelEdit = document.getElementById("cancelEdit");

      // Save changes
      editForm.onsubmit = (ev) => {
        ev.preventDefault();

        cells[0].textContent = document.getElementById("editName").value;
        cells[1].textContent = document.getElementById("editCategory").value;
        cells[2].textContent = "₱" + parseFloat(document.getElementById("editAmount").value).toFixed(2);
        cells[3].textContent = document.getElementById("editDate").value;
        cells[4].textContent = document.getElementById("editPayment").value;
        cells[5].textContent = document.getElementById("editNote").value || "-";

        document.getElementById("editModal").classList.remove("show");
        updateTotal();
      };

      cancelEdit.onclick = () => {
        document.getElementById("editModal").classList.remove("show");
      };
    }

    // === DELETE FUNCTIONALITY ===
    if (e.target.matches(".delete")) {
      e.preventDefault();
      if (confirm("Are you sure you want to delete this expense?")) {
        e.target.closest("tr").remove();
        updateTotal();
      }
    }
  });

  // === INITIAL TOTAL ===
  updateTotal();
});
