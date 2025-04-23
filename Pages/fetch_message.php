<?php
include("../Components/db.php");

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM contact_messages WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $message = mysqli_fetch_assoc($result);

    echo json_encode($message);
    mysqli_stmt_close($stmt);
}
?>