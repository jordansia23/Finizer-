<?php
session_start();
include "db_connect.php";

if (!isset($_SESSION['uid'])) {
    die("You must be logged in.");
}

$user_id = $_SESSION['uid'];

if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
    $imgData = file_get_contents($_FILES['profile_pic']['tmp_name']);

    // Option 1: Mark old pics inactive (keep history)
    $conn->query("UPDATE profile_pics SET is_active = 0 WHERE user_id = $user_id");

    // Option 2: Insert new one
    $sql = "INSERT INTO profile_pics (user_id, picture, is_active) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ib", $user_id, $null);
    $stmt->send_long_data(1, $imgData);
    $stmt->execute();

    header("Location: dashboard.php");
    exit();
} else {
    echo "No file uploaded or upload error.";
}
?>
