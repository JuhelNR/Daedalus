<?php
// Show all errors for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database credentials
$host = 'localhost'; // lowercase
$dbname = 'daedalus';
$user = 'root';
$pass = 'Maya@22';

try {
    // Create PDO instance
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );

    // Set error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: set default fetch mode to associative arrays
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
