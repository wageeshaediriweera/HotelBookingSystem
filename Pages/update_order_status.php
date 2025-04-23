<?php
session_start();
include('../Components/db.php');

$order_id = $_GET['order_id'];
$status = $_GET['status'];

// Update order status
$query = "UPDATE orders SET status = ? WHERE order_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ss', $status, $order_id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
mysqli_close($con);
?>