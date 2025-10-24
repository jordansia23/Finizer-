<?php
session_start();
include "db_connect.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['uid'])) exit(json_encode(['success'=>false, 'error'=>'Not logged in']));

$data = json_decode(file_get_contents("php://input"), true);
if (!$data) exit(json_encode(['success'=>false, 'error'=>'No data received']));

$savings_id = $data['savings_id'];
$action = $data['action'];
$uid = $_SESSION['uid'];

// Verify user owns this savings goal
$stmt = $conn->prepare("SELECT amt_svd, target_amt FROM savings_table WHERE savings_id=? AND user_id=?");
$stmt->bind_param("ii", $savings_id, $uid);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) exit(json_encode(['success'=>false, 'error'=>'Goal not found']));
$row = $res->fetch_assoc();
$amt_svd = $row['amt_svd'];
$target_amt = $row['target_amt'];
$stmt->close();

if ($action == 'deposit' || $action == 'withdraw') {
    $amount = floatval($data['amount']);
    if ($amount <= 0) exit(json_encode(['success'=>false, 'error'=>'Invalid amount']));

    $new_amt = ($action=='deposit') ? $amt_svd + $amount : $amt_svd - $amount;
    if ($new_amt < 0) $new_amt = 0;

    // Update savings
    $stmt = $conn->prepare("UPDATE savings_table SET amt_svd=?, uploaded_at=NOW() WHERE savings_id=? AND user_id=?");
    $stmt->bind_param("dii", $new_amt, $savings_id, $uid);
    $stmt->execute();
    $stmt->close();

    // Insert history
    $stmt2 = $conn->prepare("INSERT INTO savings_history (savings_id, amount, action) VALUES (?,?,?)");
    $stmt2->bind_param("ids", $savings_id, $amount, $action);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success'=>true, 'amt_svd'=>$new_amt]);
    exit();
}

if ($action=='update_name') {
    $new_name = trim($data['new_name']);
    if (!$new_name) exit(json_encode(['success'=>false, 'error'=>'Empty name']));
    $stmt = $conn->prepare("UPDATE savings_table SET goal_name=? WHERE savings_id=? AND user_id=?");
    $stmt->bind_param("sii", $new_name, $savings_id, $uid);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>true]);
    exit();
}

if ($action=='update_status') {
    $new_status = $data['new_status'];
    if (!in_array($new_status, ['active', 'inactive'])) exit(json_encode(['success'=>false, 'error'=>'Invalid status']));
    
    $stmt = $conn->prepare("UPDATE savings_table SET status=?, uploaded_at=NOW() WHERE savings_id=? AND user_id=?");
    $stmt->bind_param("sii", $new_status, $savings_id, $uid);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success'=>true]);
    exit();
}

echo json_encode(['success'=>false, 'error'=>'Invalid action']);
?>