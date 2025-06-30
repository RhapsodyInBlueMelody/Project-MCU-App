<!DOCTYPE html>
<html lang="en">
<head>
    <title>Complete Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url("assets/css/auth.css?v=1.0") ?>">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mt-5">
                    <div class="card-header">
                        Complete Your Registration
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata("error")): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata(
                                "error"
                            ) ?></div>
                        <?php endif; ?>

                        <?= form_open("auth/complete-social-registration") ?>
                        <?= csrf_field() ?> <!-- Add CSRF protection -->
                            <input type="hidden" name="role" value="<?= esc(
                                $google_data["intended_role"] ?? ""
                            ) ?>">

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= esc(
                                    $google_data["email"] ?? ""
                                ) ?>" readonly required>
                            </div>

                            <div x-data="{ username: '' }" class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" x-model="username" required>
                            </div>

                            <!-- Add password fields -->
                            <div x-data="{ showPassword: false }" class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input :type="showPassword ? 'text' : 'password'" class="form-control" id="password" name="password" required minlength="8">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                            <i :class="showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                                            <span x-text="showPassword ? 'Show' : 'Hide'"></span>
                                        </button>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Password must be at least 8 characters long and contain a mix of letters, numbers, and special characters.</small>
                            </div>

                            <div x-data="{ showPassword: false }" class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <div class="input-group">
                                    <input :type="showPassword ? 'text' : 'password'" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                            <i :class="showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                                            <span x-text="showPassword ? 'Show' : 'Hide'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <?php if (
                                $google_data["intended_role"] === "patient"
                            ): ?>
                                <div x-data="{ fullName: '', generateUsername() { this.username = this.fullName.toLowerCase().replace(/\s+/g, '_'); } }">
                                    <h4 class="mt-4">Personal Information</h4>
                                    <div class="form-group">
                                        <label for="NAMA_PASIEN">Full Name</label>
                                        <input type="text" class="form-control" name="NAMA_PASIEN" x-model="fullName" @input="generateUsername()" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="JENIS_KELAMIN">Gender</label>
                                        <select class="form-control" name="JENIS_KELAMIN" required>
                                            <option value="L">Male</option>
                                            <option value="P">Female</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="NO_TELP_PASIEN">Phone Number</label>
                                        <input type="text" class="form-control" name="NO_TELP_PASIEN" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="TEMPAT_LAHIR">Place of Birth</label>
                                        <input type="text" class="form-control" name="TEMPAT_LAHIR" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="TANGGAL_LAHIR">Date of Birth</label>
                                        <input type="date" class="form-control" name="TANGGAL_LAHIR" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ALAMAT_PASIEN">Address</label>
                                        <textarea class="form-control" name="ALAMAT_PASIEN" required></textarea>
                                    </div>
                                </div>
                            <?php elseif (
                                $google_data["intended_role"] === "doctor"
                            ): ?>
                                <div x-data="{ fullName: '', generateUsername() { this.username = this.fullName.toLowerCase().replace(/\s+/g, '_'); } }">
                                    <h4 class="mt-4">Doctor Information</h4>
                                    <div class="form-group">
                                        <label for="NAMA_DOKTER">Full Name</label>
                                        <input type="text" class="form-control" name="NAMA_DOKTER" x-model="fullName" @input="generateUsername()" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="NO_TELP_DOKTER">Phone Number</label>
                                        <input type="text" class="form-control" name="NO_TELP_DOKTER" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="KEAHLIAN">Specialization</label>
                                        <input type="text" class="form-control" name="KEAHLIAN" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="NO_LISENSI">License Number</label>
                                        <input type="text" class="form-control" name="NO_LISENSI" required>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="alert alert-warning">Unable to determine your role. Please contact support.</p>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-primary btn-block">Complete Registration</button>
                        <?= form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Font Awesome for the eye icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');

        form.addEventListener('submit', function(event) {
            // Check password format
            const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/;
            if (!passwordRegex.test(passwordInput.value)) {
                event.preventDefault();
                alert('Password must be at least 8 characters and include at least one letter, one number, and one special character.');
                return false;
            }

            // Check if passwords match
            if (passwordInput.value !== confirmPasswordInput.value) {
                event.preventDefault();
                alert('Passwords do not match');
                return false;
            }
        });
    });
    </script>

</body>
</html>
