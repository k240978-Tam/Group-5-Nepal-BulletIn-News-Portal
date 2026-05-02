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
    <link rel="stylesheet" href="/newsportal/assets/css/style.css">
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <!-- Breaking News Ticker (Bootstrap classes) -->
        <div class="bg-danger text-white py-2 px-3 small d-flex overflow-hidden">
            <span class="font-weight-bold bg-dark px-2 mr-3 z-index-2 position-relative">BREAKING</span>
            <div class="ticker-content flex-grow-1">
                Welcome to Nepal Bulletin Board! | Latest updates on Politics, Business, Sports and more... | Stay tuned for real-time news.
            </div>
        </div>

        <!-- Bootstrap 4 Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="/newsportal/index.php" style="color: var(--dark);">
                    Nepal <span class="text-danger">Bulletin</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="/newsportal/index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category.php?name=Politics">Politics</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category.php?name=Business">Business</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category.php?name=Sports">Sports</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category.php?name=Technology">Technology</a></li>
                    </ul>
                    <form action="/newsportal/search.php" method="GET" class="form-inline my-2 my-lg-0 mr-3">
                        <input class="form-control mr-sm-2" type="search" name="q" placeholder="Search news..." aria-label="Search" required>
                        <button class="btn btn-outline-danger my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
                    </form>
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (in_array($_SESSION['role'], ['admin', 'editor', 'journalist'])): ?>
                                <li class="nav-item"><a href="/newsportal/admin/index.php" class="btn btn-danger"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <?php else: ?>
                                <li class="nav-item"><a href="/newsportal/profile.php" class="btn btn-danger"><i class="fas fa-user"></i> Profile</a></li>
                            <?php endif; ?>
                            <li class="nav-item ml-2"><a href="/newsportal/logout.php" class="btn btn-light text-muted"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a href="/newsportal/login.php" class="btn btn-light text-muted mr-2">Login</a></li>
                            <li class="nav-item"><a href="/newsportal/register.php" class="btn btn-danger">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
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
