<?php

// Database credentials
$servername = "localhost";
$username = "unai";
$password = "xd";
$dbname = "bootstrapwebsite";

try {
    // Create a new PDO instance for database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

    // Set PDO attributes to handle errors
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Catch and display any connection errors
    echo "Connection failed: " . $e->getMessage();
}
