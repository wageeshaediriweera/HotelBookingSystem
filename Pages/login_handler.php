<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("../Components/db.php");

header('Content-Type: application/json');

// Collect form data
$email = $_POST['email'];
$password = $_POST['password'];

// Basic validation
if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
    exit();
}

// Prepare and execute SQL query to fetch user details
$stmt = $con->prepare("SELECT id, username, password, image, acc_type FROM userdetails WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($user_id, $username, $hashed_password, $image, $acc_type);
$stmt->fetch();
$stmt->close();

// Check if email exists and password matches
if ($hashed_password && password_verify($password, $hashed_password)) {
    // Password is correct
    $_SESSION['user_id'] = $user_id;
    $_SESSION['email'] = $email;
    $_SESSION['username'] = $username;
    $_SESSION['image'] = $image;

    // Debugging: Log the account type
    error_log("Account Type: " . $acc_type); // This will log to the server's error log file

    // Determine the redirect URL based on account type
    if ($acc_type === 'admin') {
        $redirectUrl = '/myFolder/SurfBay/Pages/adminhome.php';
    } else {
        $redirectUrl = '/myFolder/SurfBay/Pages/index.php';
    }

    echo json_encode(['status' => 'success', 'message' => 'Login successful!', 'redirect' => $redirectUrl]);
} else {
    // Invalid credentials
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}


$con->close();
?>