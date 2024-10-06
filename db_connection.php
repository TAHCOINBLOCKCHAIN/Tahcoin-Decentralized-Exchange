<?php
// Database connection settings
$servername = "localhost"; // Change to your server name (e.g., localhost)
$username = "your_database"; // Change to your database username
$password = "your_password"; // Change to your database password
$dbname = "your_database"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set character set to utf8mb4 for better compatibility with emojis and special characters
$conn->set_charset("utf8mb4");

?>