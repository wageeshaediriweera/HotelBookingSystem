<?php
session_start();
include('../Components/db.php');

$order_id = $_GET['order_id'];

$query = "
    SELECT od.item_name, od.item_price, od.qty, od.total_price
    FROM order_items od
    WHERE od.order_id = (SELECT id FROM orders WHERE order_id = ?)
";

$stmt = $con->prepare($query);
$stmt->bind_param('s', $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode(['success' => true, 'items' => $items]);
?>