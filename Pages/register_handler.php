<?php
session_start();
include("../Components/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Check if all inputs are filled
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
        exit;
    }

    // Check if image is uploaded
    if (!isset($_FILES['profile-pic']) || $_FILES['profile-pic']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Profile picture is required."]);
        exit;
    }

    // Process image upload
    $upload_dir = "../uploads/";
    $file_name = time() . "_" . basename($_FILES["profile-pic"]["name"]);
    $target_file = $upload_dir . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    $check = getimagesize($_FILES["profile-pic"]["tmp_name"]);
    if ($check === false) {
        echo json_encode(["status" => "error", "message" => "File is not an image."]);
        exit;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["profile-pic"]["size"] > 5000000) {
        echo json_encode(["status" => "error", "message" => "Sorry, your file is too large."]);
        exit;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo json_encode(["status" => "error", "message" => "Sorry, only JPG, JPEG, PNG & GIF files are allowed."]);
        exit;
    }

    // Upload file
    if (!move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $target_file)) {
        echo json_encode(["status" => "error", "message" => "Sorry, there was an error uploading your file."]);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    $query = "INSERT INTO userdetails (username, email, password, image) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $hashed_password, $file_name);

    if (mysqli_stmt_execute($stmt)) {
        // Registration successful, redirect to login page
        echo json_encode(["status" => "success", "message" => "Registration successful!"]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Error: " . mysqli_error($con)]);
        exit;
    }
}
?>