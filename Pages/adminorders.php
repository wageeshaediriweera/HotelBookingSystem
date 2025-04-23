<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../Components/db.php');

// Fetch orders
$query = "
    SELECT o.order_id, o.user_session, o.order_date, o.total_amount, o.status
    FROM orders o
";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Manage Orders</title>
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
    <img src="../images/logo.png" alt="Logo" class="logo">
    <div class="content">
        <div class="navbar-section">
            <h1>Manage Orders</h1>
            <p>SURF EASE by Surf Bay</p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr onclick="toggleOrderItems('<?php echo $row['order_id']; ?>')" style="cursor: pointer;">
                    <td><?php echo $row['order_id']; ?></td>
                    <td><?php echo $row['order_date']; ?></td>
                    <td><?php echo $row['user_session']; ?></td>
                    <td>LKR <?php echo $row['total_amount']; ?></td>
                    <td>
                        <select class="status-select"
                            onchange="updateOrderStatus('<?php echo $row['order_id']; ?>', this.value)">
                            <option value="Placed" <?php echo $row['status'] == 'Placed' ? 'selected' : ''; ?>>Placed
                            </option>
                            <option value="Processing" <?php echo $row['status'] == 'Processing' ? 'selected' : ''; ?>>
                                Processing</option>
                            <option value="Completed" <?php echo $row['status'] == 'Completed' ? 'selected' : ''; ?>>
                                Completed</option>
                            <option value="Cancelled" <?php echo $row['status'] == 'Cancelled' ? 'selected' : ''; ?>>
                                Cancelled</option>
                        </select>
                    </td>
                </tr>
                <tr id="items-<?php echo $row['order_id']; ?>" style="display:none;">
                    <td colspan="5">
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsBody-<?php echo $row['order_id']; ?>">
                                <!-- Items will be dynamically added here -->
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="5">No orders found.</td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>

        <!-- Modal for Order Items -->
        <div id="orderItemsModal" class="modal" style="display:none;">
            <div class="modal-content">
                <span class="close" onclick="closeOrderItemsModal()">&times;</span>
                <h2>Order Items</h2>
                <table id="orderItemsTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="orderItemsBody">
                        <!-- Items will be dynamically added here -->
                    </tbody>
                </table>
            </div>
        </div>

        <script>
        function toggleOrderItems(orderId) {
            const orderItemsRow = document.getElementById(`items-${orderId}`);
            const orderItemsBody = document.getElementById(`orderItemsBody-${orderId}`);

            // Toggle display of the items row
            if (orderItemsRow.style.display === 'none' || orderItemsRow.style.display === '') {
                // Clear previous items
                orderItemsBody.innerHTML = '';

                // Fetch order items via AJAX
                fetch(`get_order_items.php?order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            data.items.forEach(item => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${item.item_name}</td>
                                    <td>LKR ${item.item_price}</td>
                                    <td>${item.qty}</td>
                                    <td>LKR ${item.total_price}</td>
                                `;
                                orderItemsBody.appendChild(row);
                            });
                        } else {
                            alert('Failed to load order items.');
                        }
                        orderItemsRow.style.display = 'table-row'; // Show the row
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while fetching order items.');
                    });
            } else {
                orderItemsRow.style.display = 'none'; // Hide the row
            }
        }

        function updateOrderStatus(orderId, status) {
            fetch(`update_order_status.php?order_id=${orderId}&status=${status}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order status updated successfully!');
                    } else {
                        alert('Failed to update order status.');
                    }
                });
        }

        function closeOrderItemsModal() {
            document.getElementById('orderItemsModal').style.display = 'none'; // Hide the modal
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('orderItemsModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        };
        </script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>

</html>

<?php
// Close the database connection
mysqli_close($con);
?>