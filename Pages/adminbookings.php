<?php 
session_start();
include("../Components/db.php");

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

    <title>Admin Panel - Room Management</title>

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
    }

    .container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .content {
        background: url('../images/background.jpg') no-repeat center center fixed;
        background-size: cover;
        position: relative;
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

    /* Floating button styles */
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

    /* Table styling */
    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #ddd;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    th,
    td {
        padding: 8px;
        text-align: center;
        border: 1px solid #ddd;
        min-width: 100px;
    }

    th {
        background-color: #f2f2f2;
    }

    .manage-btn {
        display: block;
        margin-bottom: 5px;
        padding: 10px 20px;
        font-size: 16px;
        color: #fff;
        border: none;
        border-radius: 5px;
        text-align: center;
        cursor: pointer;
        text-decoration: none;
        font-size: 16px;
        background-color: #b95d58;
        transition: background-color 0.3s, opacity 0.3s;
    }

    .manage-btn:hover {
        opacity: 0.7;
        color: #f0f0f0;
        text-decoration: none;
    }

    /* Filter and Search Styles */
    .filter-tags {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-search-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .filter-tags button {
        background-color: #f0f0f0;
        color: #b95d58;
        border: 1px solid #b95d58;
        padding: 10px 20px;
        cursor: pointer;
        margin-left: 20px;
        border-radius: 20px;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .filter-tags button:hover,
    .filter-tags button:active,
    .filter-tags button:focus {
        background-color: #b95d58;
        color: #f0f0f0;
        outline: none;
    }

    .filter-tags button.active {
        background-color: #b95d58;
        color: #f0f0f0;
    }

    .search-bar {
        position: relative;
        float: right;
        margin-bottom: 20px;
    }



    .search-bar input {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        width: 300px;
    }

    .search-bar img {
        position: absolute;
        left: 260px;
        top: 14px;
        width: 20px;
        height: 20px;
    }

    .status-select {
        padding: 10px 15px;
        font-size: 15px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        color: #333;
        width: 90%;
        align-items: center;
        appearance: none;
    }

    .status-select:focus {
        outline: none;
        /* Remove default focus outline */
        border-color: #007bff;
        /* Highlight border on focus */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        /* Focus shadow */
    }

    .status-select option {
        padding: 10px;
        /* Add padding to the options */
    }


    .order-items {
        display: none;
        background-color: #f9f9f9;
    }

    .order-items td {
        padding: 10px;
        border-top: 1px solid #ddd;
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
            <h1>Manage Bookings</h1>
            <p>SURF EASE by Surf Bay</p>
        </div>

        <div class="filter-search-bar">
            <div class="filter-tags">
                <form action="adminbooking.php" method="get">
                    <button type="button" class="tag active" data-status="all">All</button>
                    <button type="button" class="tag" data-status="pending">Pending</button>
                    <button type="button" class="tag" data-status="completed">Completed</button>
                    <button type="button" class="tag" data-status="cancelled">Cancelled</button>
                </form>
            </div>
            <!--<div class="search-bar">
                <form action="adminbooking.php" method="get">
                    <input type="text" name="search" placeholder="Search..."
                        value="<?php echo htmlspecialchars($search); ?>" />
                    <img src="https://img.icons8.com/ios/50/000000/search.png" alt="Search Icon" />
                </form>
            </div>-->
        </div>
        <table>
            <thead>
                <tr>
                    <th class="">Booking ID</th>
                    <th>Booking Date</th>
                    <th>Customer Name</th>
                    <th>Room ID</th>
                    <th>Check-in Date</th>
                    <th>Check-out Date</th>
                    <th>Guest Count</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
        $bookings_result = mysqli_query($con, "SELECT * FROM room_bookings");
        
        if (mysqli_num_rows($bookings_result) > 0) {
            while ($row = mysqli_fetch_assoc($bookings_result)) {
        ?>
                <tr>
                    <td class="id-column"><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['room_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['arrival_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['departure_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['guest_count']); ?></td>
                    <td>
                        <select class="status-select">
                            <option value="Pending" <?php echo $row['status'] == 'Pending' ? 'selected' : ''; ?>>Pending
                            </option>
                            <option value="Confirmed" <?php echo $row['status'] == 'Confirmed' ? 'selected' : ''; ?>>
                                Confirmed</option>
                            <option value="Cancelled" <?php echo $row['status'] == 'Cancelled' ? 'selected' : ''; ?>>
                                Cancelled</option>
                            <option value="Completed" <?php echo $row['status'] == 'Completed' ? 'selected' : ''; ?>>
                                Completed</option>
                        </select>
                    </td>
                    <td>
                        <a href="#" class="manage-btn"
                            onclick="updateBooking(<?php echo $row['id']; ?>, '<?php echo $row['status']; ?>')">Update</a>
                    </td>
                </tr>
                <?php
            }
        } else {
        ?>
                <tr>
                    <td colspan="7">No bookings found.</td>
                </tr>
                <?php
        }
        ?>
            </tbody>
        </table>

    </div>
</body>

<script>
document.querySelectorAll('.tag').forEach(tag => {
    tag.addEventListener('click', () => {
        // Remove active class from all tags
        document.querySelectorAll('.tag').forEach(t => t.classList.remove('active'));
        // Add active class to the clicked tag
        tag.classList.add('active');

        const status = tag.getAttribute('data-status');
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const rowStatus = row.querySelector('.status-select').value.toLowerCase();
            if (status === 'all' || rowStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

document.querySelectorAll('.manage-btn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        const statusSelect = row.querySelector('.status-select');
        const newStatus = statusSelect.value;

        // Get the booking ID from the row
        const bookingId = row.querySelector('.id-column').textContent;

        // Send AJAX request to update_booking.php
        fetch('updatebooking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: bookingId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response data:', data);
                if (data.status === 'success') {
                    alert('Status updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the status.');
            });
    });
});
</script>

</html>