<!DOCTYPE html>
<html lang="en">

<head>
    <title>Medical Checkup - Patient Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url("assets/css/auth.css") ?>">
</head>


<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container card bg-white">
                    <h2 class="text-center mb-4">Medical Checkup <br><?= esc(
                                                                            $title
                                                                        ) ?></h2>

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

                    <hr>

                    <form action="<?= base_url(
                                        "auth/authenticate"
                                    ) ?>" method="post">
                        <?= csrf_field() ?> <!-- Add CSRF protection -->
                        <input type="hidden" name="role" value="pasien">
                        <div class="form-group">
                            <label for="username">Nama Pengguna atau Email</label>
                            <input type="text" class="form-control" name="username" id="username" placeholder="Enter username or email" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Sandi</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                        <a href="<?= base_url("auth/google/login/pasien") ?>" class="google-login-btn">
                            <img src="https://www.gstatic.com/marketing-cms/assets/images/d5/dc/cfe9ce8b4425b410b49b7f2dd3f3/g.webp=s48-fcrop64=1,00000000ffffffff-rw" alt="Google Icon" class="google-icon">
                            Login dengan Google
                        </a>
                        <div class="mt-3 text-center">
                            <p class="mb-0">Belum punya akun? <a href="<?= base_url("auth/register/patient") ?>">Register here</a></p>
                        </div>
                    </form>
                </div>
            </div>
</body>

</html>
