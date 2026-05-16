<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4" style="font-family: var(--font-serif);">Create an Account</h3>
                    
                    <form action="<?= \App\Core\App::get('config')['url'] ?>/register" method="POST">
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
                        
                        <button type="submit" class="btn btn-danger btn-block">Register</button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <span class="text-muted">Already have an account? <a href="<?= \App\Core\App::get('config')['url'] ?>/login" class="text-danger font-weight-bold">Login here</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
