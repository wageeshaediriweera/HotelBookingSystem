<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the query parameters
$arrival = $_GET['arrival'];
$departure = $_GET['departure'];
$guests = $_GET['guest-count'];

include("../Components/db.php");

// Escape user inputs
$arrival = mysqli_real_escape_string($con, $arrival);
$departure = mysqli_real_escape_string($con, $departure);
$guests = mysqli_real_escape_string($con, $guests);

// Fetch available rooms
$query = "
    SELECT * FROM rooms 
    WHERE id NOT IN (
        SELECT room_id FROM room_bookings 
        WHERE (arrival_date BETWEEN '$arrival' AND '$departure') 
           OR (departure_date BETWEEN '$arrival' AND '$departure')
           OR ('$arrival' BETWEEN arrival_date AND departure_date)
           OR ('$departure' BETWEEN arrival_date AND departure_date)
    )";

$result = mysqli_query($con, $query);
$rooms = [];
if (mysqli_num_rows($result) > 0) {
    while ($room = mysqli_fetch_assoc($result)) {
        $rooms[] = $room;
    }
} else {
    $rooms = []; // No available rooms
}

// Check if room ID is provided to fetch details
if (isset($_GET['room_id'])) {
    $room_id = mysqli_real_escape_string($con, $_GET['room_id']);
    $room_query = "SELECT * FROM rooms WHERE id = '$room_id'";
    $room_result = mysqli_query($con, $room_query);
    
    if ($room_result) {
        $room_details = mysqli_fetch_assoc($room_result);
        echo json_encode($room_details);
        exit();
    } else {
        echo json_encode(['error' => 'Room not found.']);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
    <link rel="stylesheet" href="../css/ourcafe.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/rooms.css">
    <link rel="stylesheet" href="../css/ourcafehomestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
    body {
        background-color: #f0f0f0;
    }

    h1 {
        text-align: center;
        color: white;
        background-color: #b95d58;
        font-size: 2rem;
        padding: 20px;
    }

    /* Modal Styles */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1050;
    }

    .modal-content {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 90%;
        max-width: 700px;
        padding: 30px;
        margin: auto;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
    }

    .modal::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        backdrop-filter: blur(1px);
        z-index: -1;
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #ccc;
    }

    .modal-header h2 {
        margin: 0;
        display: flex;
        align-items: center;
    }

    .modal-header h2 i {
        margin-right: 10px;
    }

    .modal-header .btn-close {
        cursor: pointer;
        font-size: 1.5rem;
    }

    .modal-body {
        padding: 30px;
    }

    .row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .row p {
        margin: 0;
        font-size: 1rem;
    }

    .total-price p {
        font-size: 1.5rem;
        color: #b95d58;
        font-weight: bold;
    }

    hr {
        margin: 20px 0;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        border-top: #b56c68 1px solid;
        padding: 20px;
    }

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
    </style>
</head>

<body>
    <h1>Available Rooms</h1>
    <section id="availableRooms" style="margin-top: 10px; padding: 4%;">
        <div class="rooms-grid">
            <?php if (!empty($rooms)): ?>
            <?php foreach ($rooms as $room): ?>
            <div class="room">
                <h3><?php echo htmlspecialchars($room['room_name']); ?></h3>
                <p><?php echo htmlspecialchars($room['description']); ?></p>
                <p><strong>Room Type:</strong> <?php echo htmlspecialchars($room['room_type']); ?></p>
                <p><strong>Price per Night:</strong> <?php echo htmlspecialchars($room['price_per_night']); ?></p>
                <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="Room Image"
                    style="width:200px;height:150px;">
                <button class="btn-primary btn-book" data-room-id="<?php echo htmlspecialchars($room['id']); ?>">Book
                    Now</button>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No rooms are available for the selected dates.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Modal for booking confirmation -->
    <div id="bookingModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fa-solid fa-receipt"></i> Booking Summary</h2>
                <span class="btn-close">&times;</span>
            </div>

            <div class="modal-body">
                <div class="row">
                    <p><strong>Room Name:</strong> <span id="roomName"></span></p>
                    <p><strong>Room Type:</strong> <span id="roomType"></span></p>
                </div>
                <div class="row">
                    <p><strong>Arrival Date:</strong> <span id="arrivalDate"></span></p>
                    <p><strong>Departure Date:</strong> <span id="departureDate"></span></p>
                </div>
                <hr>

                <div class="row">
                    <p><strong>Guest Count:</strong>
                        <span id="adultCount"></span> Adults,
                        <span id="kidCount"></span> Kids
                    </p>
                    <p><strong>Day Count:</strong> <span id="dayCount"></span></p>
                </div>
                <div class="row">
                    <p><strong>Price Per Night:</strong> <span id="pricePerNight"></span></p>
                </div>
                <div class="total-price">
                    <p><strong>Total Amount:</strong> <span id="totalAmount"></span></p>
                </div>
                <hr>
                <button class="btn-primary btn-confirm">Confirm Booking</button>
            </div>

        </div>
    </div>

    <?php include('../Components/footer.php'); ?>

    <script>
    document.querySelectorAll('.btn-book').forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.dataset.roomId; // Get the room ID

            // Fetch room details based on room ID
            fetch(
                    `?room_id=${roomId}&arrival=<?php echo $arrival; ?>&departure=<?php echo $departure; ?>&guest-count=<?php echo $guests; ?>`
                )
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }

                    // Populate modal with room data
                    document.getElementById('roomName').textContent = data.room_name;
                    document.getElementById('roomType').textContent = data.room_type;
                    document.getElementById('arrivalDate').textContent = '<?php echo $arrival; ?>';
                    document.getElementById('departureDate').textContent =
                        '<?php echo $departure; ?>';

                    const guestCounts = '<?php echo $guests; ?>'.split(',');
                    const adultCount = guestCounts[0] || 0;
                    const kidCount = guestCounts[1] || 0;
                    document.getElementById('adultCount').textContent = adultCount;
                    document.getElementById('kidCount').textContent = kidCount;

                    const pricePerNight = parseFloat(data.price_per_night);
                    const days = Math.ceil((new Date('<?php echo $departure; ?>') - new Date(
                        '<?php echo $arrival; ?>')) / (1000 * 60 * 60 * 24));
                    document.getElementById('dayCount').textContent = days;
                    document.getElementById('pricePerNight').textContent = pricePerNight.toFixed(2);
                    document.getElementById('totalAmount').textContent = (pricePerNight * days)
                        .toFixed(2);

                    // Show modal
                    document.getElementById('bookingModal').style.display = 'flex';
                })
                .catch(err => console.error('Error fetching room details:', err));
        });
    });

    document.querySelector('.btn-close').addEventListener('click', () => {
        document.getElementById('bookingModal').style.display = 'none';
    });

    document.querySelector('.btn-confirm').addEventListener('click', function() {
        const roomId = document.querySelector('.btn-book[data-room-id]').dataset.roomId; // Get the room ID

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'confirm_booking.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                alert('Booking confirmed! Check your activity for confirmation.');
                document.getElementById('bookingModal').style.display = 'none';
            }
        };
        const arrival = '<?php echo $arrival; ?>';
        const departure = '<?php echo $departure; ?>';
        const guests = '<?php echo $guests; ?>';
        xhr.send(`room_id=${roomId}&arrival=${arrival}&departure=${departure}&guests=${guests}`);
    });
    </script>
</body>

</html>