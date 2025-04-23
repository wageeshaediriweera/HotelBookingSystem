<?php
session_start();
include("../Components/db.php");


// Retrieve the JSON data from the AJAX request
$requestData = json_decode(file_get_contents("php://input"), true);

if (!$requestData || !isset($requestData['cartItems'], $requestData['totalAmount'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid data']);
    exit;
}

// Prepare data for insertion
$order_id = uniqid('ORD_');  // Generating a unique order ID
$total_amount = $requestData['totalAmount'];
$status = 'Pending';  // You can update the status as needed

// Insert the order into the orders table
// Prepare and execute the insert query
$order_query = "INSERT INTO orders (user_session, order_id, total_amount, status) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $order_query);
mysqli_stmt_bind_param($stmt, 'ssds', $_SESSION['username'], $order_id, $total_amount, $status);
mysqli_stmt_execute($stmt);


// Get the newly inserted order ID
$order_id = mysqli_insert_id($con);

// Insert each item into the order_items table
foreach ($requestData['cartItems'] as $item) {
    $item_name = $item['name'];
    $item_price = $item['price'];
    $qty = $item['qty'];
    $total_price = $item['total'];

    $order_item_query = "INSERT INTO order_items (order_id, item_name, item_price, qty, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $order_item_query);
    mysqli_stmt_bind_param($stmt, 'ssdds', $order_id, $item_name, $item_price, $qty, $total_price);
    mysqli_stmt_execute($stmt);
}

// Return success response
echo json_encode(['message' => 'Order placed successfully']);
?>