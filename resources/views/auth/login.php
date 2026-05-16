<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4" style="font-family: var(--font-serif);">Welcome Back</h3>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="<?= \App\Core\App::get('config')['url'] ?>/login" method="POST">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-control" required autofocus>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-danger btn-block">Login</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="#" class="text-muted d-block mb-2">Forgot Password?</a>
                        <span class="text-muted">Don't have an account? <a href="<?= \App\Core\App::get('config')['url'] ?>/register" class="text-danger font-weight-bold">Register here</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
