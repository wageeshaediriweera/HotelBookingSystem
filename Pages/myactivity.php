<?php
session_start();
include("../Components/db.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_name = $_SESSION['username'];

// Fetch orders related to the user, sorted by order_date in descending order
$order_query = "SELECT id, order_id, order_date, status, total_amount FROM orders WHERE user_session = ? ORDER BY order_date DESC";
$order_stmt = $con->prepare($order_query);
$order_stmt->bind_param("s", $user_name);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

// Fetch room bookings related to the user
$room_booking_query = "SELECT id, room_id, arrival_date, departure_date, guest_count, total_price, created_at, status, user_email FROM room_bookings WHERE user_name = ? ORDER BY created_at DESC";
$room_booking_stmt = $con->prepare($room_booking_query);
$room_booking_stmt->bind_param("s", $user_name);
$room_booking_stmt->execute();
$room_booking_result = $room_booking_stmt->get_result();

function getOrderItems($orderId, $con) {
    $query = "SELECT item_name, item_price, qty, (item_price * qty) as total_price FROM order_items WHERE order_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    return $stmt->get_result();
}

// Handle AJAX request to fetch order items
if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
    $items_result = getOrderItems($orderId, $con);

    $items = [];
    while ($row = $items_result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode(['success' => true, 'items' => $items]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Activities Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
    /* Styles here (consider moving to an external stylesheet) */
    .back-btn {
        position: fixed;
        top: 20px;
        left: 20px;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        z-index: 1000;
    }

    .back-btn:hover {
        background-color: #e0e0e0;
    }

    .activities {
        margin-top: 20px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .activity-cards {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .card {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin: 10px;
        flex: 1 1 calc(30% - 20px);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .card:hover {
        transform: scale(1.05);
    }

    h2 {
        color: #333;
    }

    h3 {
        margin: 0;
        color: #007bff;
    }

    p {
        margin: 5px 0;
        color: #555;
    }

    .no-records {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 200vh;
        background-color: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        font-size: 2rem;
        color: #333;
        z-index: 999;
    }

    .modal {
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none;
    }

    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
    }

    .close {
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 10px;
        border: 1px solid #ddd;
    }

    th {
        background-color: #f4f4f4;
    }

    .bill-footer {
        text-align: right;
        margin-top: 20px;
    }
    </style>
</head>

<body>
    <?php if ($order_result->num_rows > 0): ?>
    <div class="activities">
        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; margin-right:20px;">
            <a href="userprofile.php" class="back-btn">← Back</a>
            <h2 style="font-size: 1.5rem; margin: 0; flex-grow: 1; text-align: right;">My Activities</h2>
        </div>
        <div class="activity-cards">
            <?php while ($order = $order_result->fetch_assoc()): ?>
            <div class="card"
                onclick="showEBill('<?= htmlspecialchars($order['id']); ?>', '<?= htmlspecialchars($order['order_date']); ?>', '<?= htmlspecialchars($order['status']); ?>', <?= htmlspecialchars($order['total_amount']); ?>)">
                <div style="display: flex; justify-content: space-between;">
                    <h3>Order ID: <?= htmlspecialchars($order['order_id']); ?></h3>
                    <p>Date: <?= htmlspecialchars($order['order_date']); ?></p>
                </div>
                <p>Status: <?= htmlspecialchars($order['status']); ?></p>
                <h4>Total: <?= htmlspecialchars($order['total_amount']); ?> LKR</h4>
            </div>
            <?php endwhile; ?>
            <?php while ($room_booking = $room_booking_result->fetch_assoc()): ?>
            <div class="card"
                onclick="showRoomBookingBill('<?= htmlspecialchars($room_booking['id']); ?>','<?= htmlspecialchars($room_booking['user_email']); ?>','<?= htmlspecialchars($room_booking['room_id']); ?>', '<?= htmlspecialchars($room_booking['arrival_date']); ?>', '<?= htmlspecialchars($room_booking['status']); ?>','<?= htmlspecialchars($room_booking['guest_count']); ?>', '<?= htmlspecialchars($room_booking['total_price']); ?>', '<?= htmlspecialchars($room_booking['created_at']); ?>', '<?= htmlspecialchars($room_booking['departure_date']); ?>')">
                <div style="display: flex; justify-content: space-between;">
                    <h3>Booking ID: <?= htmlspecialchars($room_booking['id']); ?></h3>
                    <p>Booked Date: <?= htmlspecialchars($room_booking['created_at']); ?></p>
                </div>
                <p>Arrival: <?= htmlspecialchars($room_booking['arrival_date']); ?></p>
                <h4>Total: <?= htmlspecialchars($room_booking['total_price']); ?> LKR</h4>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="no-records">
        <p>No records found. Please place an order to see your activities.</p>
    </div>
    <?php endif; ?>

    <?php if ($order_result->num_rows > 0): ?>
    <?php include("../Components/footer.php"); ?>
    <?php endif; ?>

    <!-- Room Booking e-Bill Modal -->
    <div id="roomBookingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRoomBookingModal()">&times;</span>
            <div id="roomBookingContent"></div>
        </div>
    </div>

    <!-- Order e-Bill Modal -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeOrderModal()">&times;</span>
            <div id="orderContent"></div>
        </div>
    </div>

    <script>
    function showEBill(orderId, orderDate, status, total) {
        // Fetch order items and display in modal
        fetch(`?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let orderContent =
                        `<img src="../Images/logo.png" alt="Logo" style="height: 60px; width: auto; margin-left: auto; float: right; margin-right:20px ">
                    <h2 style='color:#b95d58'>Order ID: ${orderId}</h2>
                    <p>Date: ${orderDate}</p>
                    <p>Status: ${status}</p>
                    <table><tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total Price</th></tr>`;

                    data.items.forEach(item => {
                        orderContent +=
                            `<tr><td>${item.item_name}</td><td>${item.item_price} LKR</td><td>${item.qty}</td><td>${item.total_price} LKR</td></tr>`;
                    });

                    // Add footer with total amount
                    orderContent +=
                        `</table><div style="font-weight: bold; margin-top: 10px;">Total Amount: ${total} LKR</div>`;

                    document.getElementById("orderContent").innerHTML = orderContent;
                    document.getElementById("orderModal").style.display = "block";
                } else {
                    alert("Failed to fetch order items.");
                }
            });
    }


    function closeOrderModal() {
        document.getElementById("orderModal").style.display = "none";
    }


    function showRoomBookingBill(id, user_email, room_id, arrival_date, status, guest_count, total_price, created_at,
        departure_date) {
        // Populate room booking details
        const roomContent =
            `<img src="../Images/logo.png" alt="Logo" style="height: 60px; width: auto; margin-left: auto; float: right; margin-right:30px; margin-top:-30px;  ">
                   
        <h2 style="color: #b95d58;">Room Booking Details</h2>
        <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Detail</th>
            <th style="text-align: left; padding: 8px; border: 1px solid #ddd;">Information</th>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Booking ID</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${id}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Email</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${user_email}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Room ID</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${room_id}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Arrival Date</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${arrival_date}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Departure Date</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${departure_date}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Guest Count</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${guest_count}</td>
        </tr>
        <tr>
            <td style="padding: 8px; border: 1px solid #ddd;">Status</td>
            <td style="padding: 8px; border: 1px solid #ddd;">${status}</td>
        </tr>
    </table>
    <h3 style="text-align: right; margin-top: 20px;">Total Price: ${total_price} LKR</h3>
`;

        document.getElementById("roomBookingContent").innerHTML = roomContent;
        document.getElementById("roomBookingModal").style.display = "block";
    }

    function closeRoomBookingModal() {
        document.getElementById("roomBookingModal").style.display = "none";
    }
    </script>
</body>

</html>