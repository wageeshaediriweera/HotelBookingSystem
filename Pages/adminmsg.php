<?php
session_start();
include("../Components/db.php");

// Handle message read status update
if (isset($_GET['mark_read']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $update_query = "UPDATE contact_messages SET read_status = 1 WHERE id = ?";
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Fetch messages
$query = "SELECT * FROM contact_messages ORDER BY id DESC";
$result = mysqli_query($con, $query);
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

    <title>Admin Panel - Contact Messages</title>

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

    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
    }

    th,
    td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .manage-btn {
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .manage-btn.read {
        background-color: #007bff;
        color: white;
        border: none;
    }

    .manage-btn.read:hover {
        background-color: #365899;
    }

    .manage-btn.view {
        background-color: transparent;
        border: 1px solid #333;
        color: #333;
    }

    .manage-btn.view:hover {
        background-color: #f1f1f1;
    }

    .read {
        background-color: #f0f0f0;
        color: #333;
        border: 1px solid #ddd;
    }

    .unread {
        background-color: #fff;
        color: #333;
        border: 1px solid #ddd;
    }

    .full-message {
        display: none;
        padding: 20px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
    }

    .full-message.show {
        display: block;
    }

    .table-container {
        height: 635px;
        overflow-y: auto;
        border: 1px solid #ddd;
    }

    .table-container table {
        width: 100%;
    }

    .table-container thead {
        position: sticky;
        top: 0;
        background-color: #fff;
        z-index: 1;
    }

    .content {
        height: auto;
        min-height: 100vh;
    }

    tr.selected {
        border-left: 10px solid #007bff;
        color: #007bff;
    }

    tr {
        transition: all 0.3s ease;
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
            <h1>Messages</h1>
            <p>SURF EASE by Surf Bay</p>
        </div>

        <!-- Messages Table -->
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th> <i class="fas fa-inbox"></i> Inbox</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="<?php echo $row['read_status'] ? 'read' : 'unread'; ?>"
                                    onclick="showFullMessage(<?php echo $row['id']; ?>, this.querySelector('.manage-btn'))">
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['email']); ?></strong><br>
                                        <?php echo htmlspecialchars($row['subject']); ?>
                                    </td>
                                    <td>
                                        <button class="manage-btn <?php echo $row['read_status'] ? 'view' : 'read'; ?>"
                                            onclick="showFullMessage(<?php echo $row['id']; ?>, this); event.stopPropagation();">
                                            <?php echo $row['read_status'] ? 'View' : 'Read'; ?>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr>
                                    <td colspan="2">No messages received.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div id="full-message" class="full-message">
                        <h4>Details</h4>
                        <p><strong>Date:</strong> <span id="message-date">N/A</span></p>
                        <p><strong>Name:</strong> <span id="message-name">N/A</span></p>
                        <p><strong>Email:</strong> <span id="message-email">N/A</span></p>
                        <p><strong>Subject:</strong> <span id="message-subject">N/A</span></p>
                        <p><strong>Message:</strong></p>
                        <p id="message-content">Click any message to view details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showFullMessage(id, buttonElement) {
        // Remove 'selected' class from all rows
        document.querySelectorAll('tr').forEach(row => row.classList.remove('selected'));

        // Add 'selected' class to the clicked row
        buttonElement.closest('tr').classList.add('selected');

        // Fetch message details from server using AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'fetch_message.php?id=' + id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const message = JSON.parse(xhr.responseText);
                document.getElementById('message-date').textContent = message.date || 'N/A';
                document.getElementById('message-name').textContent = message.name || 'N/A';
                document.getElementById('message-email').textContent = message.email || 'N/A';
                document.getElementById('message-subject').textContent = message.subject || 'N/A';
                document.getElementById('message-content').textContent = message.message || 'N/A';
                document.getElementById('full-message').classList.add('show');

                // Update the read status
                const updateXhr = new XMLHttpRequest();
                updateXhr.open('GET', 'update_read_status.php?id=' + id, true);
                updateXhr.onload = function() {
                    if (updateXhr.status === 200) {
                        // Update the button to 'View'
                        buttonElement.textContent = 'View';
                        buttonElement.classList.remove('read');
                        buttonElement.classList.add('view');
                    }
                };
                updateXhr.send();
            }
        };
        xhr.send();
    }
    </script>
</body>

</html>