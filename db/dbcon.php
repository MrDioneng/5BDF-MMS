<?php
$servername = "localhost";  // Database server name
$username = "root";         // Database username
$password = "";             // Database password (empty for local environments)
$database = "5bdf-memo";    // Database name

// Establish connection to the database
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    // If the connection fails, display a detailed error message
    die("Connection failed: " . $conn->connect_error);
}

// Optionally, you can set the character set for the connection (to prevent character encoding issues)
$conn->set_charset("utf8mb4");
?>
