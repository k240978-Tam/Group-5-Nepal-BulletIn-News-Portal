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
    <h2 class="mb-2" style="font-family: var(--font-serif); text-align: center;">Create an Account</h2>
    
    <form action="/actions/process_register.php" method="POST">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" required autofocus>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
        </div>
        
        <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
    </form>
    
    <div style="text-align: center; margin-top: 1.5rem;">
        <span class="text-gray">Already have an account? <a href="/login.php" class="text-primary font-weight-500">Login here</a></span>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
