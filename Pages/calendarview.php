<?php
session_start();

// Include the database connection file
include("../Components/db.php");

// Default timezone for the application
date_default_timezone_set('Asia/Colombo');

// Fetch current year and month from query parameters or use today's date
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Calculate first day of the month and total days in the month
$firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = date('t', $firstDayOfMonth);

// Get the name of the month and the weekday of the first day
$monthName = date('F', $firstDayOfMonth);
$firstWeekday = date('w', $firstDayOfMonth);

// Fetch bookings for the selected month and year
$bookingsSQL = "SELECT rb.arrival_date, rb.departure_date, rb.user_name, rb.room_id, rt.room_type, rt.room_name, rb.status
                FROM room_bookings rb 
                JOIN rooms rt ON rb.room_id = rt.id 
                WHERE MONTH(arrival_date) = ? AND YEAR(arrival_date) = ?";


$stmtBookings = $con->prepare($bookingsSQL);
$stmtBookings->bind_param("ii", $month, $year);
$stmtBookings->execute();
$bookingsResult = $stmtBookings->get_result();

// Organize bookings by date range (arrival to day before departure)
$bookings = [];
while ($row = $bookingsResult->fetch_assoc()) {
    $arrivalDay = date('j', strtotime($row['arrival_date']));
    $departureDay = date('j', strtotime($row['departure_date'])) - 1; // Departure day not included

    // Loop through all the days of the booking and mark them
    for ($day = $arrivalDay; $day <= $departureDay; $day++) {
        if (!isset($bookings[$day])) {
            $bookings[$day] = [];
        }
        $bookings[$day][] = $row; // Group bookings by day
    }
}

// Close statement and database connection
$stmtBookings->close();
$con->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <title>Booking Calendar</title>

    <style>
    body {
        margin: 0;
        padding: 20px;
        font-family: 'Lato', sans-serif;
        background-color: #f0f0f0;
    }

    .calendar {
        display: table;
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        margin-bottom: 100px;
    }

    .calendar th,
    .calendar td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: center;
        vertical-align: top;
    }

    .calendar th {
        background-color: #b95d58;
        color: white;
        font-weight: bold;
        font-size: 16px;
    }

    .calendar .day {
        height: 120px;
        width: 120px;
    }

    .calendar .booking {
        background-color: #ffc107;
        padding: 5px;
        margin-top: 10px;
        border-radius: 5px;
        font-size: 15px;
    }

    .calendar-nav {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .calendar-title {
        font-size: 38px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 20px;
    }

    .nav-btn {
        background-color: #b95d58;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
    }

    .nav-btn:hover {
        background-color: #a54a47;
    }

    .calendar th {
        background-color: #6c757d;
        color: white;
    }


    .booking.confirmed {
        background-color: #b95d58;
        color: white;
        border: 2px solid #b95d58;

    }

    .booking.pending {
        background-color: white;
        color: #28a745;
        /* Green for pending text */
        border: 2px solid #28a745;
        /* Green outline */
    }

    .booking.cancelled {
        background-color: white;
        color: #dc3545;
        /* Red for cancelled text */
        border: 2px solid #dc3545;
        /* Red outline */
    }

    .booking.completed {
        background-color: white;
        color: #007bff;
        /* Blue for completed text */
        border: 2px solid #007bff;
        /* Blue outline */
    }


    .footer {
        background-color: black;
        color: white;
        text-align: center;
        padding: 10px 0;
        position: fixed;
        bottom: 0;
        width: 100%;
        font-size: 14px;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        left: 0;
    }

    .footer p {
        margin: 0;
    }
    </style>
</head>

<body>

    <!-- Calendar Navigation -->
    <div class="calendar-nav">
        <form action="calendarview.php" method="GET">
            <input type="hidden" name="month" value="<?php echo $month == 1 ? 12 : $month - 1; ?>">
            <input type="hidden" name="year" value="<?php echo $month == 1 ? $year - 1 : $year; ?>">
            <button type="submit" class="nav-btn">Previous</button>
        </form>

        <div class="calendar-title">
            <?php echo htmlspecialchars($monthName) . ' ' . htmlspecialchars($year); ?>
        </div>

        <form action="calendarview.php" method="GET">
            <input type="hidden" name="month" value="<?php echo $month == 12 ? 1 : $month + 1; ?>">
            <input type="hidden" name="year" value="<?php echo $month == 12 ? $year + 1 : $year; ?>">
            <button type="submit" class="nav-btn">Next</button>
        </form>
    </div>

    <!-- Calendar Table -->
    <table class="calendar">
        <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </tr>
        <tr>
            <?php
            // Loop through each day of the month
for ($day = 1; $day <= $daysInMonth; $day++) {
    // New row every Sunday
    if (($firstWeekday + $day - 1) % 7 == 0 && $day != 1) {
        echo '</tr><tr>';
    }

    // Start cell for the day
    $bookingCount = isset($bookings[$day]) ? count($bookings[$day]) : 0;
    $extraClass = $bookingCount > 1 ? ' multiple-bookings' : ''; 
    echo '<td class="day' . $extraClass . '">';
    echo '<strong>' . htmlspecialchars($day) . '</strong>';
    
    if (isset($bookings[$day])) {
        foreach ($bookings[$day] as $booking) {
            // Determine the status class
            $statusClass = '';
$status = strtolower($booking['status']); // Convert status to lowercase
switch ($status) {
    case 'confirmed':
        $statusClass = 'confirmed'; // Yellow
        break;
    case 'pending':
        $statusClass = 'pending'; // Green
        break;
    case 'cancelled':
        $statusClass = 'cancelled'; // Red
        break;
    case 'completed':
        $statusClass = 'completed'; // Blue
        break;
}

            // Output booking information with status
            echo '<div class="booking ' . $statusClass . '">';
            echo ' # ' . htmlspecialchars($booking['room_id']);
            echo ' - ' . htmlspecialchars($booking['room_name']);
            echo '</div>';
        }
    }

    echo '</td>';
}

            ?>
        </tr>
    </table>

</body>

<footer class="footer">
    <p>&copy; <?php echo date("Y"); ?> SurfBay Hotel. All rights reserved.</p>
</footer>

</html>