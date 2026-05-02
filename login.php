<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/auth_check.php';

if (is_logged_in()) {
    redirect('/index.php');
}

require_once 'includes/header.php';
?>

<div class="auth-container">
    <h2 class="mb-2" style="font-family: var(--font-serif); text-align: center;">Welcome Back</h2>
    
    <form action="/actions/process_login.php" method="POST">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
    </form>
    
    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="#" class="text-gray" style="display:block; margin-bottom:0.5rem;">Forgot Password?</a>
        <span class="text-gray">Don't have an account? <a href="/register.php" class="text-primary font-weight-500">Register here</a></span>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
