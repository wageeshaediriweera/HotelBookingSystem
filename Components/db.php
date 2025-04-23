<?php
$db_host = 'localhost';  
$db_user = 'root';       
$db_pass = '';           
$db_name = 'hoteldb';  

// Create connection
$con = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
?>