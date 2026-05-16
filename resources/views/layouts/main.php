<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Nepal Bulletin - Online News Portal' ?></title>
    <!-- Bootstrap 4.0 CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= \App\Core\App::get('config')['url'] ?>/public/assets/css/app.css">
    <!-- Load FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <!-- Top Utility Bar -->
        <div class="top-bar py-1 d-none d-md-block">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <span class="mr-3"><i class="far fa-calendar-alt mr-1"></i> <?= date('l, F j, Y') ?></span>
                        <span><i class="fas fa-map-marker-alt mr-1"></i> Kathmandu, Nepal</span>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="social-links d-inline-block mr-3">
                            <a href="#" class="mx-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="mx-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="mx-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="mx-2"><i class="fab fa-youtube"></i></a>
                        </div>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="<?= \App\Core\App::get('config')['url'] ?>/logout" class="small text-white-50"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        <?php else: ?>
                            <a href="<?= \App\Core\App::get('config')['url'] ?>/login" class="small text-white-50"><i class="fas fa-user"></i> Member Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Branding Area -->
        <div class="branding-area d-none d-md-block">
            <div class="container text-center">
                <a href="<?= \App\Core\App::get('config')['url'] ?>/" class="text-decoration-none">
                    <h1 class="logo-text">Nepal <span>Bulletin</span></h1>
                    <p class="tagline">The Voice of the Nation</p>
                </a>
            </div>
        </div>

        <!-- Modern Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container">
                <!-- Mobile Logo -->
                <a class="navbar-brand d-md-none font-weight-bold" href="<?= \App\Core\App::get('config')['url'] ?>/">
                    Nepal <span class="text-danger">Bulletin</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item"><a class="nav-link" href="<?= \App\Core\App::get('config')['url'] ?>/">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Politics">Politics</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Business">Business</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Sports">Sports</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Technology">Technology</a></li>
                    </ul>
                    
                    <div class="header-actions d-flex align-items-center">
                        <form action="<?= \App\Core\App::get('config')['url'] ?>/search" method="GET" class="search-form-container mr-3 d-none d-lg-block">
                            <input class="search-input-elegant" type="search" name="q" placeholder="Search news..." aria-label="Search" required>
                            <button type="submit" class="btn btn-link text-muted position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%);">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (in_array($_SESSION['role'], ['admin', 'editor', 'journalist'])): ?>
                                <a href="<?= \App\Core\App::get('config')['url'] ?>/admin" class="btn btn-sm btn-danger px-3"><i class="fas fa-tachometer-alt"></i></a>
                            <?php else: ?>
                                <a href="<?= \App\Core\App::get('config')['url'] ?>/profile" class="btn btn-sm btn-danger px-3"><i class="fas fa-user"></i></a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="<?= \App\Core\App::get('config')['url'] ?>/register" class="btn btn-sm btn-outline-danger px-3 d-none d-md-inline-block">Subscribe</a>
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
    <main class="container py-4">
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

        <?= $content ?>
        
    </main>
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Nepal Bulletin</h4>
                    <p class="text-gray mb-2">Delivering the latest news, breaking stories, and insightful analysis from Nepal and around the world.</p>
                </div>
                <div class="footer-col">
                    <h4>Categories</h4>
                    <ul class="footer-links">
                        <li><a href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Politics">Politics</a></li>
                        <li><a href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Business">Business</a></li>
                        <li><a href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Sports">Sports</a></li>
                        <li><a href="<?= \App\Core\App::get('config')['url'] ?>/category?name=Technology">Technology</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Subscribe</h4>
                    <p class="text-gray mb-2">Get daily news updates right to your inbox.</p>
                    <form style="display:flex; gap:0.5rem;">
                        <input type="email" class="form-control" placeholder="Email address" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; <?= date('Y') ?> Nepal Bulletin Board. Group 5 Project.
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap 4.0 JS Dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
