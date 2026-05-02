<?php
$host = '127.0.0.1'; // Typically 127.0.0.1 or localhost for XAMPP
$username = 'root';
$password = ''; // Default XAMPP MySQL password is empty
$dbname = 'nepal_bulletin';

try {
    // Connect to MySQL server first (without DB selection) to create DB
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database '$dbname' checked/created successfully.<br>";

    // Now connect to the newly created database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Read the schema.sql file
    $sql = file_get_contents('schema.sql');
    
    // Execute the SQL queries
    $pdo->exec($sql);
    echo "Tables and initial data imported successfully from schema.sql.<br>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    die("DB Setup Failed: " . $e->getMessage());
}
?>
