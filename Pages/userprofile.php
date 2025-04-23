<?php
session_start();
include("../Components/db.php");

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];

// Fetch user details from the database
$query = "SELECT username, email, image FROM userdetails WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$fetch_profile = $result->fetch_assoc();

if (!$fetch_profile) {
    echo "No user found or query failed.";
    exit();
}

// Fetch orders related to the user
$order_query = "SELECT order_id, order_date, status, total_amount FROM orders WHERE user_session = ?";
$order_stmt = $con->prepare($order_query);
$order_stmt->bind_param("i", $user_name);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

$error_message = '';
$success_message = '';
$refresh_page = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_email = $_POST['email'];
    $new_username = $_POST['username'];
    
    // Validate inputs
    if (empty($new_email) || empty($new_username)) {
        $error_message = "Email and username cannot be empty.";
    } else {
        // Set current image in case no new image is uploaded
        $new_image = $fetch_profile['image'] ? $fetch_profile['image'] : 'default.jpg'; // Default to current image

        // Handle file upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_image']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                // Generate a unique name for the new image
                $new_image = uniqid() . "." . $ext;
                $upload_path = "../uploads/" . $new_image;

                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                    // Delete old image if it exists and is not the default image
                    if ($fetch_profile['image'] != 'default.jpg' && file_exists("../uploads/" . $fetch_profile['image'])) {
                        unlink("../uploads/" . $fetch_profile['image']);
                    }
                } else {
                    $error_message = "Failed to upload image. Please try again.";
                }
            } else {
                $error_message = "Invalid file type. Allowed types: jpg, jpeg, png, gif.";
            }
        }

        if (empty($error_message)) {
            // Update the database with new details and image
            $update_query = "UPDATE userdetails SET email = ?, username = ?, image = ? WHERE id = ?";
            $stmt = $con->prepare($update_query);
            $stmt->bind_param("sssi", $new_email, $new_username, $new_image, $user_id);

            if ($stmt->execute()) {
                $success_message = "Profile updated successfully!";
                // Update session variables
                $_SESSION['email'] = $new_email;
                $_SESSION['username'] = $new_username;
                $refresh_page = true;
            } else {
                $error_message = "Failed to update profile. Please try again.";
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate inputs
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } else {
        // Fetch the current hashed password from the database
        $query = "SELECT password FROM userdetails WHERE id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($current_password, $user['password'])) {
            // Update the password
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE userdetails SET password = ? WHERE id = ?";
            $stmt = $con->prepare($update_query);
            $stmt->bind_param("si", $new_hashed_password, $user_id);
            
            if ($stmt->execute()) {
                $success_message = "Password changed successfully!";
            } else {
                $error_message = "Failed to change password. Please try again.";
            }
        } else {
            $error_message = "Current password is incorrect.";
        }
    }
}

// Fetch user details from the database
$query = "SELECT username, email, image FROM userdetails WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$fetch_profile = $result->fetch_assoc();

if (!$fetch_profile) {
    echo "No user found or query failed.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/userprofile.css" />
</head>

<body>
    <!-- Back button -->
    <a href="index.php" class="back-btn">← Back</a>

    <section class="profile">
        <div class="profile-pic">
            <img src="<?= htmlspecialchars('../uploads/' . $fetch_profile['image']); ?>" alt="Profile Picture" />
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Email:</h3>
                <p><?= htmlspecialchars($fetch_profile['email']); ?></p>
            </div>
            <div class="card">
                <h3>Username:</h3>
                <p><?= htmlspecialchars($fetch_profile['username']); ?></p>
            </div>
        </div>

        <div class="buttons-container">
            <div class="top-buttons">
                <a href="#" id="updateProfileBtn"><i class="fas fa-user-edit"></i> Update Profile</a>
                <a href="#" id="changePasswordBtn"><i class="fas fa-key"></i> Change Password</a>
            </div>
            <div class="top-buttons">
                <a href="myactivity.php"><i class="fas fa-tasks"></i> My Activities</a>
            </div>
            <div class="bottom-button">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </section>



    <!-- Profile Update Modal -->
    <div id="profileUpdateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Profile</h2>
            <div id="message-container">
                <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                <p class="success-message"><?= htmlspecialchars($success_message); ?></p>
                <?php endif; ?>
            </div>
            <form id="updateProfileForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"
                enctype="multipart/form-data">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($fetch_profile['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username"
                        value="<?= htmlspecialchars($fetch_profile['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="profile_image">Profile Picture:</label>
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                </div>
                <button type="submit" name="update_profile" class="submit-btn">Update Profile</button>
            </form>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Change Password</h2>
            <div id="message-container">
                <?php if (!empty($error_message)): ?>
                <p class="error-message"><?= htmlspecialchars($error_message); ?></p>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                <p class="success-message"><?= htmlspecialchars($success_message); ?></p>
                <?php endif; ?>
            </div>
            <form id="changePasswordForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password:</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="submit-btn">Change Password</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const updateProfileModal = document.getElementById('profileUpdateModal');
        const changePasswordModal = document.getElementById('changePasswordModal');
        const updateProfileBtn = document.getElementById('updateProfileBtn');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const closeBtns = document.querySelectorAll('.close');

        if (updateProfileBtn) {
            updateProfileBtn.addEventListener('click', () => {
                updateProfileModal.style.display = 'block';
            });
        }

        if (changePasswordBtn) {
            changePasswordBtn.addEventListener('click', () => {
                changePasswordModal.style.display = 'block';
            });
        }

        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                updateProfileModal.style.display = 'none';
                changePasswordModal.style.display = 'none';
            });
        });

        window.addEventListener('click', (event) => {
            if (event.target === updateProfileModal || event.target === changePasswordModal) {
                updateProfileModal.style.display = 'none';
                changePasswordModal.style.display = 'none';
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const activityCards = document.querySelectorAll('.activity-cards .card');
        const eBillModal = document.getElementById('eBillModal');

        activityCards.forEach(card => {
            card.addEventListener('click', () => {
                // Extract information from the card
                const orderId = card.querySelector('h3').innerText.split(': ')[1];
                const orderDate = card.querySelector('p').innerText.split(': ')[1];
                const items = 3; // Example static value, replace with actual data if available
                const total =
                    2300; // Example static value, replace with actual data if available

                // Populate the eBill modal with the extracted information
                document.getElementById('orderId').innerText = orderId;
                document.getElementById('orderDate').innerText = orderDate;
                document.getElementById('customerName').innerText =
                    'John Doe'; // Replace with actual customer name

                // Populate the bill items dynamically (example)
                const billItems = document.getElementById('billItems');
                billItems.innerHTML = ''; // Clear previous items
                for (let i = 1; i <= items; i++) {
                    const row = document.createElement('tr');
                    row.innerHTML =
                        `<td>Item ${i}</td><td>LKR 500</td><td>1</td><td>LKR 500</td>`;
                    billItems.appendChild(row);
                }

                // Set the grand total
                document.getElementById('grandTotal').innerText = total;

                // Show the modal
                eBillModal.style.display = 'block';
            });
        });
    });

    function closeModal() {
        document.getElementById('eBillModal').style.display = 'none';
    }
    </script>

</body>

</html>