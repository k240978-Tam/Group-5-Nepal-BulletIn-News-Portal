<?php
/**
 * Migration: Add Core Management Features Database Tables
 */
require_once '../includes/db.php';

$tables = [
    "site_settings" => "CREATE TABLE IF NOT EXISTS site_settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT
    )",
    "audit_logs" => "CREATE TABLE IF NOT EXISTS audit_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        action VARCHAR(255),
        details TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )"
];

$columns = [
    "internal_note" => "ALTER TABLE articles ADD COLUMN internal_note TEXT NULL"
];

$results = [];

// Create Tables
foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        $results[] = ["type" => "Table", "name" => $name, "status" => "✅ Created/Verified"];
    } catch (PDOException $e) {
        $results[] = ["type" => "Table", "name" => $name, "status" => "❌ Error: " . $e->getMessage()];
    }
}

// Add Columns
foreach ($columns as $col => $sql) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'articles' AND COLUMN_NAME = ?");
    $check->execute([$col]);
    if ($check->fetchColumn() > 0) {
        $results[] = ["type" => "Column", "name" => $col, "status" => "⚠️ Already exists"];
        continue;
    }
    try {
        $pdo->exec($sql);
        $results[] = ["type" => "Column", "name" => $col, "status" => "✅ Added successfully"];
    } catch (PDOException $e) {
        $results[] = ["type" => "Column", "name" => $col, "status" => "❌ Error: " . $e->getMessage()];
    }
}

// Seed default settings
$default_settings = [
    'site_name' => 'Nepal Bulletin',
    'site_tagline' => 'Delivering the latest news from Nepal and around the world.',
    'accent_color' => '#c0392b',
    'allow_registration' => '1'
];

foreach ($default_settings as $key => $val) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    $stmt->execute([$key, $val]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Core Features Migration</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #e2e8f0; padding: 3rem; }
        .card { background: #1e293b; border-radius: 12px; padding: 2rem; box-shadow: 0 10px 30px rgba(0,0,0,0.5); max-width: 700px; margin: 0 auto; }
        h1 { margin-top: 0; color: #f87171; font-size: 1.5rem; }
        table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        th, td { text-align: left; padding: 0.75rem; border-bottom: 1px solid #334155; }
        th { color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; }
        .btn { display: inline-block; background: #c0392b; color: #fff; padding: 0.6rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🚀 Core Features Database Migration</h1>
        <table>
            <thead><tr><th>Type</th><th>Name</th><th>Status</th></tr></thead>
            <tbody>
                <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= $r['type'] ?></td>
                    <td><code><?= $r['name'] ?></code></td>
                    <td><?= $r['status'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>✅ Database is ready for advanced management features.</p>
        <a href="/newsportal/admin/index.php" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>
