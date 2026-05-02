<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nepal Bulletin - Online News Portal</title>
    <!-- Bootstrap 4.0 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <!-- Breaking News Ticker -->
        <div class="breaking-ticker">
            <span class="ticker-label">BREAKING</span>
            <div class="ticker-content">
                Welcome to Nepal Bulletin Board! | Latest updates on Politics, Business, Sports and more... | Stay tuned for real-time news.
            </div>
        </div>

        <!-- Main Navigation -->
        <div class="container header-container">
            <a href="/index.php" class="logo">
                Nepal <span>Bulletin</span>
            </a>

            <nav>
                <ul class="nav-links">
                    <li><a href="/index.php">Home</a></li>
                    <li><a href="/category.php?name=Politics">Politics</a></li>
                    <li><a href="/category.php?name=Business">Business</a></li>
                    <li><a href="/category.php?name=Sports">Sports</a></li>
                    <li><a href="/category.php?name=Technology">Technology</a></li>
                </ul>
            </nav>

            <div class="header-actions">
                <form action="/search.php" method="GET" style="display:flex;">
                    <input type="text" name="q" class="search-bar" placeholder="Search news..." required>
                    <button type="submit" class="btn" style="background:transparent; border:none; margin-left:-30px; cursor:pointer;">
                        <i class="fas fa-search text-gray"></i>
                    </button>
                </form>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (in_array($_SESSION['role'], ['admin', 'editor', 'journalist'])): ?>
                        <a href="/admin/index.php" class="btn btn-primary"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <?php else: ?>
                        <a href="/profile.php" class="btn btn-primary"><i class="fas fa-user"></i> Profile</a>
                    <?php endif; ?>
                    <a href="/logout.php" class="btn text-gray"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="/login.php" class="btn text-gray">Login</a>
                    <a href="/register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container">
        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger mt-2">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success mt-2">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
