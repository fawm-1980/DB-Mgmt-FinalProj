<?php
$servername = "127.0.0.1";
$port = 3307;
$username = "student_s006";
$password = '4$GKFxG!@&R88%&2';
$dbname = "blog_s006";

// Turn off mysqli exceptions so we can handle errors manually
mysqli_report(MYSQLI_REPORT_OFF);

// Create connection
$conn = @new mysqli($servername, $username, $password, $dbname, $port);

// Flag for connection status
$db_connected = !$conn->connect_error;

if (!$db_connected) {
    error_log("DB connection failed: " . $conn->connect_error);
}
?>