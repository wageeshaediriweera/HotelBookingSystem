<?php
session_start();
include("../Components/db.php");


error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to the user
ini_set('log_errors', 1);     // Enable logging of errors
ini_set('error_log', '/path/to/your/error.log'); // Set the path to the error log file


// Handle AJAX request for available rooms
if (isset($_POST['ajax']) && $_POST['ajax'] == 'check_rooms') {
    $arrival = mysqli_real_escape_string($con, $_POST['arrival']);
    $departure = mysqli_real_escape_string($con, $_POST['departure']);
    $guest_count = mysqli_real_escape_string($con, $_POST['guest_count']);

    // SQL query to get rooms that are not booked within the selected date range
    $sql = "
        SELECT * FROM rooms 
        WHERE id NOT IN (
            SELECT room_id FROM room_bookings 
            WHERE (arrival_date BETWEEN '$arrival' AND '$departure') 
               OR (departure_date BETWEEN '$arrival' AND '$departure')
               OR ('$arrival' BETWEEN arrival_date AND departure_date)
               OR ('$departure' BETWEEN arrival_date AND departure_date)
        )";

    $available_rooms = mysqli_query($con, $sql);
    
    if (mysqli_num_rows($available_rooms) > 0) {
        $rooms = [];
        while ($room = mysqli_fetch_assoc($available_rooms)) {
            $rooms[] = [
                'room_name' => $room['room_name'],
                'room_type' => $room['room_type'],
                'description' => $room['description'],
                'price_per_night' => $room['price_per_night'],
                'image_url' => $room['image_url'],
                'room_id' => $room['id']
            ];
        }
        echo json_encode(['status' => 'success', 'rooms' => $rooms]);
    } else {
        echo json_encode(['status' => 'no_rooms']);
    }
    exit();  // Added exit here to stop execution after this block
}

// Handle AJAX request for booking confirmation
if (isset($_POST['ajax']) && $_POST['ajax'] == 'confirm_booking') {
    // Get the JSON data from the request
    $data = json_decode(file_get_contents("php://input"), true);

    // Assuming user details are stored in session
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    $user_email = $_SESSION['user_email'];

    if (isset($data['room_id'], $data['arrival_date'], $data['departure_date'], $data['guest_count'], $data['total_price'])) {
        $room_id = mysqli_real_escape_string($con, $data['room_id']);
        $arrival_date = mysqli_real_escape_string($con, $data['arrival_date']);
        $departure_date = mysqli_real_escape_string($con, $data['departure_date']);
        $guest_count = mysqli_real_escape_string($con, $data['guest_count']);
        $total_price = mysqli_real_escape_string($con, $data['total_price']);

        // Insert booking into the database
        $sql = "INSERT INTO room_bookings (room_id, arrival_date, departure_date, guest_count, total_price, status, user_id, user_name, user_email) 
                VALUES ('$room_id', '$arrival_date', '$departure_date', '$guest_count', '$total_price', 'Pending', '$user_id', '$user_name', '$user_email')";

        if (mysqli_query($con, $sql)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => mysqli_error($con)]);
        }

    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
    
    exit();  // Ensure the script ends after handling this block
}

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
                    <form id="roomForm" action="roombooking.php" method="GET">
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

    <!-- Available Rooms Section -->
    <!-- <section id="availableRooms" class="rooms-container"
        style="display: none; padding: 50px; background-color: #f7f7f7;">
        <h2 style="text-align: center; margin-bottom: 30px;">Available Rooms</h2>
        <div id="roomsGrid" class="rooms-grid"></div>
    </section> -->


    <div id="bookingModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-receipt"></i> Booking Summary</h2>
                <span class="btn-close" style="cursor: pointer;">&times;</span> <!-- Close button -->
            </div>
            <div class="modal-body">
                <div class="row">
                    <p><strong>Room Name:</strong> <span id="roomName"></span></p>
                    <p><strong>Room Type:</strong> <span id="roomType"></span></p>
                </div>
                <p><strong>Description:</strong> <span id="roomDescription"></span></p>
                <hr>

                <div class="row">
                    <p><strong>Arrival Date:</strong> <span id="arrivalDate"></span></p>
                    <p><strong>Departure Date:</strong> <span id="departureDate"></span></p>
                </div>

                <p><strong>Guest Count:</strong> <span id="guestCount"></span></p>
                <p><strong>Nights:</strong> <span id="nights"></span></p>
                <hr>
                <p><strong>Price per Night:</strong> LKR <span id="pricePerNight"></span></p>
                <div class="row total-price">
                    <p><strong>Total Price:</strong> LKR <span id="totalPrice"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button id="confirmBooking" class="btn btn-primary">Confirm Booking</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    // Initialize flatpickr for arrival and departure date pickers
    flatpickr("#arrival", {
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: function(selectedDates, dateStr, instance) {
            const departureInput = document.querySelector("#departure");
            departureInput._flatpickr.set("minDate", dateStr); // Lock dates before arrival
        }
    });

    flatpickr("#departure", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    document.getElementById('roomForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('ajax', 'check_rooms');

        fetch('rooms.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const roomsGrid = document.getElementById('roomsGrid');
                roomsGrid.innerHTML = '';

                if (data.status === 'success') {
                    // Display available rooms
                    data.rooms.forEach(room => {
                        const roomElement = `
                    <div class="room" style="padding: 20px; margin: 10px; text-align: center;">
                        <img src="../Images/${room.image_url}" alt="${room.room_name}" style="width: 100%; height: 200px; object-fit: cover;">
                        <h4 style="margin-top: 15px;">${room.room_name} (${room.room_type})</h4>
                        <p>${room.description}</p>
                        <p><strong>Price per night: </strong>LKR. ${room.price_per_night}</p>
                        <button class="btn btn-primary book-btn" 
                                data-room-name="${room.room_name}" 
                                data-room-type="${room.room_type}" 
                                data-description="${room.description}" 
                                data-price="${room.price_per_night}" 
                                data-room-id="${room.room_id}">
                            Book Now
                        </button>
                    </div>`;
                        roomsGrid.innerHTML += roomElement;
                    });

                    // Show the available rooms section
                    document.getElementById('availableRooms').style.display = 'block';

                    // Add event listeners to "Book Now" buttons
                    document.querySelectorAll('.book-btn').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const roomName = this.dataset.roomName;
                            const roomType = this.dataset.roomType;
                            const description = this.dataset.description;
                            const price = parseFloat(this.dataset.price);
                            const roomId = this.dataset.roomId;

                            // Get values from the form
                            const arrivalDate = document.getElementById('arrival').value;
                            const departureDate = document.getElementById('departure')
                                .value;
                            const guestCount = document.getElementById('guest-count').value;

                            // Calculate nights and total price
                            const checkInDate = new Date(arrivalDate);
                            const checkOutDate = new Date(departureDate);
                            const nights = Math.floor((checkOutDate - checkInDate) / (1000 *
                                60 * 60 * 24));
                            const totalPrice = nights * price;

                            // Populate the modal with the selected room details
                            document.getElementById('roomName').innerText = roomName;
                            document.getElementById('roomType').innerText = roomType;
                            document.getElementById('roomDescription').innerText =
                                description;
                            document.getElementById('arrivalDate').innerText = arrivalDate;
                            document.getElementById('departureDate').innerText =
                                departureDate;
                            document.getElementById('guestCount').innerText = guestCount;
                            document.getElementById('nights').innerText = nights;
                            document.getElementById('pricePerNight').innerText = price
                                .toFixed(2);
                            document.getElementById('totalPrice').innerText = totalPrice
                                .toFixed(2);
                            document.getElementById('bookingModal').style.display =
                                'flex'; // Show the modal
                        });
                    });

                } else {
                    roomsGrid.innerHTML = '<p>No rooms are available for the selected dates.</p>';
                    document.getElementById('availableRooms').style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    document.querySelector('.btn-close').addEventListener('click', function() {
        document.getElementById('bookingModal').style.display =
            'none'; // Close the modal when the close button is clicked
    });

    // Handle the booking confirmation
    document.getElementById('confirmBooking').addEventListener('click', function() {
        const roomId = document.querySelector('.book-btn').dataset.roomId;
        const arrivalDate = document.getElementById('arrivalDate').innerText;
        const departureDate = document.getElementById('departureDate').innerText;
        const guestCount = document.getElementById('guestCount').innerText;
        const totalPrice = document.getElementById('totalPrice').innerText;

        // Prepare data for the booking confirmation
        const bookingData = {
            room_id: roomId,
            arrival_date: arrivalDate,
            departure_date: departureDate,
            guest_count: guestCount,
            total_price: totalPrice
        };

        fetch('rooms.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ajax: 'confirm_booking',
                    ...bookingData
                })
            })
            .then(response => response.text()) // Change to text for debugging
            .then(text => {
                console.log(text); // Log the raw response
                return JSON.parse(text); // Then parse as JSON
            })
            .then(data => {
                if (data.status === 'success') {
                    alert('Booking confirmed successfully!');
                    document.getElementById('bookingModal').style.display = 'none'; // Close the modal
                } else {
                    alert('Booking failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
    </script>


</body>
<style>
/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    /* Lighter overlay */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    /* Ensure it appears above other content */
}

.modal-content {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    width: 90%;
    max-width: 700px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0;
    padding: 30px;
}

.modal-header {
    padding-bottom: 0;
    border-bottom: none;
    /* Remove border */
}

.modal-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: #b56c68;
    font-family: 'Lato', sans-serif;
}

.modal-body {
    padding: 20px 0;
    gap: 10px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    border-top: #b56c68 1px solid;
    padding-top: 20px;
}

/* Emphasize the Total Price */
.total-price p {
    font-size: 1.3rem;
    color: #b95d58;
    font-weight: bold;
}

/* Button Styles */
.btn-primary {
    background-color: #b95d58;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-primary:hover {
    background-color: #b56c68;
}

.row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
}

.row p {
    margin: 0;
    font-size: 1.1rem;
}
</style>

<?php include("../Components/footer.php"); ?>

<?php
mysqli_close($con);
?>

</html>