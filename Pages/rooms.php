<?php
session_start();
include("../Components/db.php");

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/ourcafe.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/rooms.css">
    <link rel="stylesheet" href="../css/ourcafehomestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>Rooms & Villa</title>
</head>

<body style="background-color: #FFF1E6;">
    <?php include('../Components/navigationbar.php') ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <img src="../Images/RoomsnVilla.png" alt="Rooms and Villa" class="hero-image">
        <div class="hero-overlay">
            <div class="hero-content">
                <div class="availability-container">
                    <form id="roomForm" action="roombooking.php" method="GET" onsubmit="return checkSession();">
                        <div class="row align-items-center">
                            <div class="col-sm-3 mb-3">
                                <label for="arrival">Arrival Date:</label>
                                <input type="text" id="arrival" name="arrival" class="form-control" required>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="departure">Departure Date:</label>
                                <input type="text" id="departure" name="departure" class="form-control" required>
                            </div>
                            <div class="col-sm-3 mb-3">
                                <label for="guest-count">Guests (Adults, Children):</label>
                                <input type="text" id="guest-count" name="guest-count" class="form-control" required>
                            </div>
                            <div class="col-sm-3">
                                <button type="submit" class="btn btn-primary w-100">Check Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize Flatpickr for arrival date
        const arrivalDatePicker = flatpickr("#arrival", {
            dateFormat: "Y-m-d",
            minDate: "today", // Cannot select a past date
            onChange: function(selectedDates, dateStr, instance) {
                // Update the departure date's minDate based on the selected arrival date
                departureDatePicker.set("minDate", dateStr);
            }
        });

        // Initialize Flatpickr for departure date
        const departureDatePicker = flatpickr("#departure", {
            dateFormat: "Y-m-d",
            minDate: "today" // Initially cannot select a past date
        });
    });
    </script>

</body>
<?php include("../Components/footer.php"); ?>

<?php
mysqli_close($con);
?>

</html>