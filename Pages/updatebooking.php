<?php
// update_booking.php
session_start();
include("../Components/db.php");

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the incoming data
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id']) && isset($data['status'])) {
        $bookingId = $data['id'];
        $newStatus = $data['status'];

        // Update the status in the database
        $query = "UPDATE room_bookings SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, 'si', $newStatus, $bookingId);

        if (mysqli_stmt_execute($stmt)) {
            // Return a success response
            echo json_encode(['status' => 'success']);
        } else {
            // Return an error response
            echo json_encode(['status' => 'error', 'message' => 'Failed to update booking status.']);
        }

        mysqli_stmt_close($stmt);
    } else {
        // Return an error if required data is missing
        echo json_encode(['status' => 'error', 'message' => 'Invalid request data.']);
    }
} else {
    // Return an error for invalid request methods
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

mysqli_close($con);
?>