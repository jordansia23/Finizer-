<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['uid'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$savings_id = $_GET['savings_id'] ?? 0;
$user_id = $_SESSION['uid'];

// Verify the savings goal belongs to the user
$check_stmt = $conn->prepare("SELECT s.savings_id FROM savings_table s WHERE s.savings_id = ? AND s.user_id = ?");
$check_stmt->bind_param("ii", $savings_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid savings goal']);
    exit();
}

// Get transaction history
$history_stmt = $conn->prepare("
    SELECT amount, action, created_at 
    FROM savings_history 
    WHERE savings_id = ? 
    ORDER BY created_at DESC
");
$history_stmt->bind_param("i", $savings_id);
$history_stmt->execute();
$history_result = $history_stmt->get_result();

$history = [];
while ($row = $history_result->fetch_assoc()) {
    $history[] = [
        'amount' => $row['amount'],
        'action' => $row['action'],
        'date' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'history' => $history]);
?>