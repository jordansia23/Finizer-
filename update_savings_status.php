<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['uid'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['savings_id']) && isset($_POST['status'])) {
    $savings_id = $_POST['savings_id'];
    $status = $_POST['status'];
    $user_id = $_SESSION['uid'];
    
    // Verify the savings goal belongs to the user
    $check_stmt = $conn->prepare("SELECT user_id FROM savings_table WHERE savings_id = ?");
    $check_stmt->bind_param("i", $savings_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows === 0 || $check_result->fetch_assoc()['user_id'] != $user_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid savings goal']);
        exit();
    }
    
    // Update the status
    $update_stmt = $conn->prepare("UPDATE savings_table SET status = ?, updated_at = NOW() WHERE savings_id = ?");
    $update_stmt->bind_param("si", $status, $savings_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
    
    $update_stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>