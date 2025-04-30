<?php
$servername = "localhost"; // XAMPP default
$username = "root";         // XAMPP default
$password = "";             // XAMPP default (no password)
$database = "redacted_db";  // Our new DB

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset to utf8mb4 for emojis etc
$conn->set_charset("utf8mb4");
?>
