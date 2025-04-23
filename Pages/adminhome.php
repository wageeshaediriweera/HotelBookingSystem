<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../Components/db.php");

// Today's date and tomorrow's date
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Prepared statement for today's bookings
$todayBookingsSQL = "SELECT rb.id, rb.room_id, rb.guest_count, rb.user_name, rt.room_type 
                     FROM room_bookings rb 
                     JOIN rooms rt ON rb.room_id = rt.id 
                     WHERE arrival_date = ?";
$stmtToday = $con->prepare($todayBookingsSQL);
$stmtToday->bind_param("s", $today);
$stmtToday->execute();
$todayResult = $stmtToday->get_result();

// Prepared statement for tomorrow's bookings
$tomorrowBookingsSQL = "SELECT rb.id, rb.room_id, rb.guest_count, rb.user_name, rt.room_type 
                        FROM room_bookings rb 
                        JOIN rooms rt ON rb.room_id = rt.id 
                        WHERE arrival_date = ?";
$stmtTomorrow = $con->prepare($tomorrowBookingsSQL);
$stmtTomorrow->bind_param("s", $tomorrow);
$stmtTomorrow->execute();
$tomorrowResult = $stmtTomorrow->get_result();

// Generate HTML for today's bookings
$todayBookingsHTML = '<div class="ebill-container">'; // Container for two e-bills per row
if ($todayResult->num_rows > 0) {
    while ($row = $todayResult->fetch_assoc()) {
        $todayBookingsHTML .= "
        <div class='ebill'>
            <div class='ebill-header'>
                <h2>Booking Details</h2>
                <p>Date: " . htmlspecialchars($today) . "</p>
            </div>
            <div class='ebill-body'>
                <p><strong>Guest Name:</strong> " . htmlspecialchars($row['user_name']) . "</p>
                <p><strong>Room ID:</strong> " . htmlspecialchars($row['room_id']) . "</p>
                <p><strong>Room Type:</strong> " . htmlspecialchars($row['room_type']) . "</p>
                <p><strong>Guest Count:</strong> " . htmlspecialchars($row['guest_count']) . "</p>
            </div>
            <div class='ebill-footer'>
                <p>Note: Guest Will Arrive Today!</p>
            </div>
        </div>";
    }
} else {
    $todayBookingsHTML .= "<li>No Arrivals today</li>";
}
$todayBookingsHTML .= '</div>'; // Close container div

// Generate HTML for tomorrow's bookings
$tomorrowBookingsHTML = '<div class="ebill-container">'; // Container for two e-bills per row
if ($tomorrowResult->num_rows > 0) {
    while ($row = $tomorrowResult->fetch_assoc()) {
        $tomorrowBookingsHTML .= "
        <div class='ebill'>
            <div class='ebill-header'>
                <h2>Booking Details</h2>
                <p>Date: " . htmlspecialchars($tomorrow) . "</p>
            </div>
            <div class='ebill-body'>
                <p><strong>Guest Name:</strong> " . htmlspecialchars($row['user_name']) . "</p>
                <p><strong>Room ID:</strong> " . htmlspecialchars($row['room_id']) . "</p>
                <p><strong>Room Type:</strong> " . htmlspecialchars($row['room_type']) . "</p>
                <p><strong>Guest Count:</strong> " . htmlspecialchars($row['guest_count']) . "</p>
            </div>
            <div class='ebill-footer'>
                <p>Note: Guest Will Arrive Tomorrow!</p>
            </div>
        </div>";
    }
} else {
    $tomorrowBookingsHTML .= "<li>No Arrivals today</li>";
}
$tomorrowBookingsHTML .= '</div>'; // Close container div




// Close prepared statements
$stmtToday->close();
$stmtTomorrow->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Admin Panel - Hotel Management</title>

    <style>
    body {
        margin: 0;
        padding: 20px;
        font-family: 'Lato', sans-serif;
        background-color: #f0f0f0;
    }

    /* Container for the e-bills to align them in rows */
    .ebill-container {
        display: flex;
        flex-wrap: wrap;
        /* Allows items to wrap to the next line if needed */
        justify-content: space-between;
        /* Space between e-bills */
        margin-bottom: 20px;
    }

    .ebill {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        width: calc(50% - 10px);
        /* Adjusts the width to fit two per row with a small gap */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        box-sizing: border-box;
    }

    .ebill-header {
        background-color: #b95d58;
        color: white;
        padding: 15px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
        text-align: center;
    }

    .ebill-header h2 {
        margin: 0;
        font-size: 24px;
    }

    .ebill-body {
        padding: 20px;
    }

    .ebill-body p {
        font-size: 16px;
        margin: 8px 0;
    }

    .ebill-body strong {
        color: #333;
    }

    .ebill-footer {
        text-align: center;
        padding: 10px;
        background-color: #f9f9f9;
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
        color: #666;
    }

    .ebill-footer p {
        margin: 0;
        font-size: 14px;
    }

    /* Media query to stack the e-bills vertically on smaller screens */
    @media (max-width: 768px) {
        .ebill {
            width: 100%;
            /* Full width on smaller screens */
            margin-bottom: 20px;
        }
    }
    </style>

    <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Lato', sans-serif;
    }

    .content {
        margin-left: 80px;
        padding: 20px;
        height: 100vh;
        background-color: #666;
        display: flex;
        flex-direction: column;
        background: url('../images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        position: relative;
    }

    .container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .row {
        display: flex;
        flex: 1;
    }

    .card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-body {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    li {
        text-align: center;
        font-size: 16px;
        color: #666;
    }

    .logo {
        position: absolute;
        top: 35px;
        right: 100px;
        height: 50px;
        z-index: 1000;
        width: auto;
    }

    .navbar-section {
        background-color: #000;
        color: #fff;
        padding: 20px 60px;
        height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .floating-button {
        position: fixed;
        bottom: 60px;
        right: 40px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .floating-button:hover {
        background-color: #0056b3;
    }

    .floating-button i {
        font-size: 30px;
    }

    .footer {
        background-color: black;
        color: white;
        text-align: center;
        padding: 10px 0;
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        font-size: 14px;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
    }

    .footer p {
        margin: 0;
    }
    </style>
</head>

<body>

    <?php include("../Components/adminnavbar.php"); ?>

    <!-- Logo -->
    <img src="../images/logo.png" alt="Logo" class="logo">

    <!-- Content -->
    <div class="content">
        <div class="navbar-section">
            <h1>Overview</h1>
            <p>SURF EASE - Admin Panel</p>
        </div>
        <div class="container">
            <div class="row">
                <!-- Today's Bookings Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Today's Arrivals</h3>
                        </div>
                        <div class="card-body">
                            <ul id="today-bookings">
                                <?php echo $todayBookingsHTML; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tomorrow's Bookings Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3>Tomorrow's Arrivals</h3>
                        </div>
                        <div class="card-body">
                            <ul id="tomorrow-bookings">
                                <?php echo $tomorrowBookingsHTML; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Button -->
    <button class="floating-button" onclick="location.href='calendarview.php'">
        <i class="fas fa-calendar-alt"></i>
    </button>

    <footer class="footer">
        <p>&copy; <?php echo date("Y"); ?> SurfBay Hotel. All rights reserved.</p>
    </footer>


</body>

</html>