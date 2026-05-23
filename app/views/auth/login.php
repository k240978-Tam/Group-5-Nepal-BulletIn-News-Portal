<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white text-center">
                <h4 class="mb-0">Member Login</h4>
            </div>
            <div class="card-body p-4">
                <form action="/newsportal/login" method="POST">
                    <div class="form-group mb-3">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required placeholder="Enter email">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Enter password">
                    </div>
                    <button type="submit" class="btn btn-danger btn-block w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <p>Don't have an account? <a href="/newsportal/register" class="text-danger">Register here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
