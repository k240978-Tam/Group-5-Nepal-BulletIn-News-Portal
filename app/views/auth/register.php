<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-danger text-white text-center">
                <h4 class="mb-0">Create an Account</h4>
            </div>
            <div class="card-body p-4">
                <form action="/newsportal/register" method="POST">
                    <div class="form-group mb-3">
                        <label for="name">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="Enter your full name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" required placeholder="Enter email">
                    </div>
                    <div class="form-group mb-4">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="Create a password">
                    </div>
                    <button type="submit" class="btn btn-danger btn-block w-100">Register</button>
                </form>
                <div class="text-center mt-3">
                    <p>Already have an account? <a href="/newsportal/login" class="text-danger">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
