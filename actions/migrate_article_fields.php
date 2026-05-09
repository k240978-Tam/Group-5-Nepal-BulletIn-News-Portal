<?php
/**
 * Migration: Add enhanced article fields
 * Run this ONCE at: http://localhost/newsportal/actions/migrate_article_fields.php
 * After running, this file can be deleted for security.
 */
require_once '../includes/db.php';

$migrations = [
    "image_position"   => "ALTER TABLE articles ADD COLUMN image_position ENUM('left','center','right','full') DEFAULT 'center'",
    "image_caption"    => "ALTER TABLE articles ADD COLUMN image_caption VARCHAR(255) NULL",
    "article_color"    => "ALTER TABLE articles ADD COLUMN article_color VARCHAR(7) DEFAULT '#c0392b'",
    "language"         => "ALTER TABLE articles ADD COLUMN language ENUM('en','ne','bilingual') DEFAULT 'en'",
    "article_type"     => "ALTER TABLE articles ADD COLUMN article_type ENUM('standard','opinion','interview','photo_essay','breaking') DEFAULT 'standard'",
    "meta_description" => "ALTER TABLE articles ADD COLUMN meta_description TEXT NULL",
    "slug"             => "ALTER TABLE articles ADD COLUMN slug VARCHAR(255) NULL",
    "is_featured"      => "ALTER TABLE articles ADD COLUMN is_featured TINYINT(1) DEFAULT 0",
    "scheduled_at"     => "ALTER TABLE articles ADD COLUMN scheduled_at DATETIME NULL",
];

$results = [];

foreach ($migrations as $column => $sql) {
    // Check if column already exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'articles' AND COLUMN_NAME = ?");
    $check->execute([$column]);
    if ($check->fetchColumn() > 0) {
        $results[] = ["column" => $column, "status" => "⚠️ Already exists — skipped"];
        continue;
    }
    try {
        $pdo->exec($sql);
        $results[] = ["column" => $column, "status" => "✅ Added successfully"];
    } catch (PDOException $e) {
        $results[] = ["column" => $column, "status" => "❌ Error: " . $e->getMessage()];
    }
}

// Also add unique index on slug if not exists
try {
    $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_articles_slug ON articles(slug)");
} catch (PDOException $e) {
    // Ignore if already exists or unsupported syntax — try alternative
    try {
        $pdo->exec("ALTER TABLE articles ADD UNIQUE KEY idx_articles_slug (slug)");
    } catch (PDOException $e2) {
        // Already exists, ignore
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Migration — Nepal Bulletin</title>
<style>
    body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 2rem; }
    h1 { color: #f87171; }
    table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
    th, td { text-align: left; padding: 0.6rem 1rem; border-bottom: 1px solid #334155; }
    th { background: #1e293b; color: #94a3b8; }
    .link { margin-top: 2rem; }
    .link a { color: #60a5fa; }
</style>
</head>
<body>
<h1>🗄️ Article Fields Migration</h1>
<p style="color:#94a3b8;">Adding enhanced fields to the <code>articles</code> table...</p>
<table>
    <thead><tr><th>Column</th><th>Result</th></tr></thead>
    <tbody>
    <?php foreach ($results as $r): ?>
        <tr>
            <td><code><?= htmlspecialchars($r['column']) ?></code></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="link">
    <p>✅ Migration complete. <a href="/newsportal/admin/editor.php">Go to Article Editor →</a></p>
    <p style="color:#f87171;font-size:0.85rem;">⚠️ For security, you can now delete this file: <code>actions/migrate_article_fields.php</code></p>
</div>
</body>
</html>
