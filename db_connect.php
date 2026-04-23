<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blog_database";

// Turn off mysqli exceptions so we can handle errors manually
mysqli_report(MYSQLI_REPORT_OFF);

// Create connection
$conn = @new mysqli($servername, $username, $password, $dbname);

// Flag for connection status
$db_connected = !$conn->connect_error;

if (!$db_connected) {
    error_log("DB connection failed: " . $conn->connect_error);
}
?>