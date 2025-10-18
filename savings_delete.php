<?php
session_start();
include "db_connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['uid'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['uid'];
$data = json_decode(file_get_contents("php://input"), true);
$savings_id = $data['savings_id'] ?? 0;

if ($savings_id) {
    // Delete from history first
    $stmt1 = $conn->prepare("DELETE FROM savings_history WHERE savings_id = ?");
    $stmt1->bind_param("i", $savings_id);
    $stmt1->execute();
    $stmt1->close();

    // Delete the goal
    $stmt2 = $conn->prepare("DELETE FROM savings_table WHERE savings_id = ? AND user_id = ?");
    $stmt2->bind_param("ii", $savings_id, $user_id);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid goal ID']);
}
?>