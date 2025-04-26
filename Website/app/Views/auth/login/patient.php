<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medical Checkup - Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url("assets/css/auth.css?v=1.0") ?>">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container card bg-white">
                    <h2 class="text-center mb-4">Medical Checkup Login<br><?= esc($title) ?></h2>

                    <?php if (session()->getFlashdata("msg")): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata(
                            "msg"
                        ) ?></div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata("success")): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata(
                            "success"
                        ) ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url(
                        "auth/authenticate"
                    ) ?>" method="post">
                        <div class="form-group">
                            <label for="username">Username or Email</label>
                            <input type="text" class="form-control" name="username" id="username" placeholder="Enter username or email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>

                        <div class="mt-3 text-center">
                            <p class="mb-0">Don't have an account? <a href="<?= base_url(
                                "auth/register"
                            ) ?>">Register here</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
