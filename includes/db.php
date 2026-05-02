<?php
$host = '127.0.0.1';
$dbname = 'nepal_bulletin';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch mode associative by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection failed. Please ensure MySQL is running and the database 'nepal_bulletin' exists. You can run <a href='setup.php'>setup.php</a> to create it.");
}
?>
