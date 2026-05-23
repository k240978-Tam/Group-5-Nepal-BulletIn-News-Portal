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
    <link rel="stylesheet" href="/newsportal/public/assets/css/style.css">
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Premium Typography from Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap">
    <!-- Dark Mode Detection & Persistence Script (before page paint) -->
    <script>
        (function() {
            const darkModeKey = 'nepal-bulletin-dark-mode';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDarkMode = localStorage.getItem(darkModeKey) !== null 
                ? localStorage.getItem(darkModeKey) === 'true' 
                : prefersDark;
            
            if (isDarkMode) {
                document.documentElement.classList.add('dark-mode');
                document.body.classList.add('dark-mode');
            }
        })();
    </script>
</head>
<body>
    <header>
        <!-- Top Utility Bar -->
        <div class="top-bar py-1 d-none d-md-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="mr-3"><i class="far fa-calendar-alt mr-1"></i> <?php echo date('l, F j, Y'); ?></span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i> Kathmandu, Nepal</span>
                    </div>
                    <div class="col-md-6 text-right d-flex justify-content-md-end justify-content-between align-items-center">
                        <div class="social-links d-inline-block mr-md-3">
                            <a href="#" class="mx-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="mx-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="mx-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="mx-2"><i class="fab fa-youtube"></i></a>
                        </div>
                        <!-- Dark Mode Toggle -->
                        <button id="darkModeToggle" class="btn btn-sm btn-link text-white-50 mx-2" title="Toggle dark mode" style="padding: 2px 8px; border: none; background: none; cursor: pointer; font-size: 1rem;">
                            <i class="fas fa-moon"></i>
                        </button>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="/newsportal/logout" class="small text-white-50"><i class="fas fa-sign-out-alt"></i> <span class="d-none d-md-inline">Logout</span></a>
                        <?php else: ?>
                            <a href="/newsportal/login" class="small text-white-50"><i class="fas fa-user"></i> <span class="d-none d-md-inline">Login</span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Branding Area -->
        <div class="branding-area d-none d-md-block">
            <div class="container text-center">
                <a href="/newsportal/" class="text-decoration-none">
                    <h1 class="logo-text">Nepal <span>Bulletin</span></h1>
                    <p class="tagline">The Voice of the Nation</p>
                </a>
            </div>
        </div>

        <!-- Modern Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container">
                <!-- Mobile Logo -->
                <a class="navbar-brand d-md-none font-weight-bold" href="/newsportal/">
                    Nepal <span class="text-danger">Bulletin</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="/newsportal/">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category?name=Politics">Politics</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category?name=Business">Business</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category?name=Sports">Sports</a></li>
                        <li class="nav-item"><a class="nav-link" href="/newsportal/category?name=Technology">Technology</a></li>
                        <li class="nav-item"><a class="nav-link text-danger font-weight-bold" href="/newsportal/bulletin"><i class="fas fa-bolt"></i> Bulletin</a></li>
                    </ul>
                    
                    <div class="header-actions d-flex align-items-center">
                        <form action="/newsportal/search" method="GET" class="search-form-container mr-3 d-none d-lg-block">
                            <input class="search-input-elegant" type="search" name="q" placeholder="Search news..." aria-label="Search" required>
                            <button type="submit" class="btn btn-link text-muted position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%);">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (in_array($_SESSION['role'], ['admin', 'editor', 'journalist'])): ?>
                                <a href="/newsportal/admin" class="btn btn-sm btn-danger px-3"><i class="fas fa-tachometer-alt"></i></a>
                            <?php else: ?>
                                <a href="/newsportal/profile" class="btn btn-sm btn-danger px-3"><i class="fas fa-user"></i></a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/newsportal/register" class="btn btn-sm btn-outline-danger px-3 d-none d-md-inline-block">Subscribe</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Breaking News Ticker -->
        <div class="breaking-ticker d-flex overflow-hidden">
            <div class="container d-flex align-items-center">
                <span class="ticker-label">BREAKING</span>
                <div class="ticker-content flex-grow-1">
                    Welcome to Nepal Bulletin Board! | Latest updates on Politics, Business, Sports and more... | Stay tuned for real-time news.
                </div>
            </div>
        </div>
    </header>
    <main class="<?= (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || basename($_SERVER['PHP_SELF']) == '/newsportal/profile') ? 'container-fluid px-md-5' : 'container' ?>">
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
