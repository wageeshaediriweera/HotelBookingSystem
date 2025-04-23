<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../Components/db.php");

// Ensure user is logged in, adjust this as necessary
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['error' => 'User not logged in.']));
}

// Get the booking details from the request
$room_id = isset($_POST['room_id']) ? mysqli_real_escape_string($con, $_POST['room_id']) : '';
$arrival_date = isset($_POST['arrival']) ? mysqli_real_escape_string($con, $_POST['arrival']) : '';
$departure_date = isset($_POST['departure']) ? mysqli_real_escape_string($con, $_POST['departure']) : '';
$guest_count = isset($_POST['guests']) ? mysqli_real_escape_string($con, $_POST['guests']) : '';

// Calculate total price
$room_query = "SELECT price_per_night FROM rooms WHERE id = '$room_id'";
$room_result = mysqli_query($con, $room_query);
$room = mysqli_fetch_assoc($room_result);

if ($room) {
    $price_per_night = $room['price_per_night'];
    $days = ceil((strtotime($departure_date) - strtotime($arrival_date)) / (60 * 60 * 24));
    $total_price = $price_per_night * $days;

    // Insert into bookings table
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $user_name = $_SESSION['username'];
    $user_email = $_SESSION['email']; // Assuming user_email is stored in session
    $created_at = date('Y-m-d H:i:s');
    $status = 'confirmed'; // Set status to confirmed

    $insert_query = "
        INSERT INTO room_bookings (room_id, arrival_date, departure_date, guest_count, total_price, created_at, status, user_id, user_name, user_email)
        VALUES ('$room_id', '$arrival_date', '$departure_date', '$guest_count', '$total_price', '$created_at', '$status', '$user_id', '$user_name', '$user_email')
    ";

    if (mysqli_query($con, $insert_query)) {
        echo json_encode(['success' => 'Booking confirmed successfully!', 'redirect' => 'rooms.php']);
    } else {
        echo json_encode(['error' => 'Booking failed. Please try again.']);
    }
} else {
    echo json_encode(['error' => 'Room not found.']);
}
?>