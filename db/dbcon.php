<?php
$servername = "localhost"; 
$username = "root";         
$password = "";          
$database = "5bdf-memo";   

// Improved error handling using try-catch
try {
    $conn = new mysqli($servername, $username, $password, $database);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    // Set the character set to utf8mb4 for better handling of multi-byte characters
    $conn->set_charset("utf8mb4");
} catch (Exception $e) {
    // Display error message if connection fails
    die("Error: " . $e->getMessage());
}
?>
