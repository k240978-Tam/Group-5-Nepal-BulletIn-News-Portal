<?php
$host = '127.0.0.1';
$dbname = 'nepal_bulletin';
$username = 'root';
$password = '';

echo "<h2>Database Setup Script</h2>";

try {
    // Connect to MySQL server first
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "<p>Database `$dbname` created or already exists.</p>";

    // Connect to the specific database
    $pdo->exec("USE `$dbname`");

    // Read schema.sql
    $sql_file = __DIR__ . '/../schema.sql';
    if (file_exists($sql_file)) {
        $sql = file_get_contents($sql_file);
        $pdo->exec($sql);
        echo "<p>Successfully imported `schema.sql`. Tables created and seeded.</p>";
    } else {
        echo "<p style='color:red;'>Error: `schema.sql` file not found in root directory!</p>";
    }
    
    echo "<p>Setup complete! You can now <a href='index.php'>go to the homepage</a> or <a href='login.php'>login</a> (Default admin: admin@nepalbulletin.com / Admin@123).</p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Setup Failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
