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
                        <input type="hidden" name="role" value="<?= esc($role ?? '') ?>">

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= esc(
                                                                                                        $google_data["email"] ?? ""
                                                                                                    ) ?>" readonly required>
                        </div>

                        <div x-data="{ username: '' }" class="form-group">
                            <label for="username">Nama Pengguna</label>
                            <input type="text" class="form-control" id="username" name="username" x-model="username" required>
                        </div>

                        <!-- Add password fields -->
                        <div x-data="{ showPassword: false }" class="form-group">
                            <label for="password">Kata Sandi</label>
                            <div class="input-group">
                                <input :type="showPassword ? 'text' : 'password'" class="form-control" id="password" name="password" required minlength="8">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" @click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                                        <span x-text="showPassword ? 'Show' : 'Hide'"></span>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">Kata sandi harus memiliki panjang minimal 8 karakter dan berisi campuran huruf, angka, dan karakter khusus.</small>
                        </div>

                        <div x-data="{ showPassword: false }" class="form-group">
                            <label for="confirm_password">Konfirmasi Kata Sandi</label>
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

                        <?php if ($role === "pasien"): ?>
                            <div x-data="{ fullName: '', generateUsername() { this.username = this.fullName.toLowerCase().replace(/\s+/g, '_'); } }">
                                <h4 class="mt-4">Data Diri</h4>
                                <div class="form-group">
                                    <label for="nama_pasien">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_pasien" x-model="fullName" @input="generateUsername()" required>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin</label>
                                    <select class="form-control" name="jenis_kelamin" required>
                                        <option value="">Select Gender</option>
                                        <option value="L">Laki-Laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="telepon">Nomor Telepon</label>
                                    <input type="text" class="form-control" name="no_telp_pasien" required>
                                </div>
                                <div class="form-group">
                                    <label for="no_identitas">No KTP</label>
                                    <input type="text" class="form-control" name="no_identitas" required>
                                </div>
                                <div class="form-group">
                                    <label for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" class="form-control" name="tempat_lahir" required>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" required>
                                </div>
                                <div class="form-group">
                                    <label for="location">Lokasi Rumah Sakit</label>
                                    <select class="form-control" name="lokasi" required>
                                        <option value="JKT">Cabang Jakarta</option>
                                        <option value="BDG">Cabang Bandung</option>
                                        <option value="SBY">Cabang Surabaya</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="alamat">Alamat</label>
                                    <textarea class="form-control" name="alamat" required></textarea>
                                </div>
                            </div>
                        <?php elseif ($role === "dokter"): ?>
                            <div x-data="{ fullName: '', generateUsername() { this.username = this.fullName.toLowerCase().replace(/\s+/g, '_'); } }">
                                <h4 class="mt-4">Informasi Dokter</h4>
                                <div class="form-group">
                                    <label for="nama_dokter">Nama Lengkap</label>
                                    <input type="text" class="form-control" name="nama_dokter" x-model="fullName" @input="generateUsername()" required>
                                </div>
                                <div class="form-group">
                                    <label for="jenis_kelamin">Gender</label>
                                    <select class="form-control" name="jenis_kelamin" required>
                                        <option value="">Select Gender</option>
                                        <option value="L">Laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_lahir">Date of Birth</label>
                                    <input type="date" class="form-control" name="tanggal_lahir" required>
                                </div>
                                <div class="form-group">
                                    <label for="telepon_dokter">Nomor Telepon</label>
                                    <input type="text" class="form-control" name="telepon_dokter" required>
                                </div>
                                <div class="form-group">
                                    <label for="id_spesialisasi">Keahlian</label>
                                    <select class="form-control" name="id_spesialisasi" required>
                                        <option value="">Pilih Keahlian</option>
                                        <?php foreach ($spesialisasiList as $spesialisasi): ?>
                                            <option value="<?= esc($spesialisasi['id_spesialisasi']) ?>">
                                                <?= esc($spesialisasi['nama_spesialisasi']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="lokasi_kerja">Lokasi Rumah Sakit</label>
                                    <select class="form-control" name="lokasi_kerja" required>
                                        <option value="Jakarta">Cabang Jakarta</option>
                                        <option value="Bandung">Cabang Bandung</option>
                                        <option value="Surabaya">Cabang Surabaya</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="no_lisensi">License Number</label>
                                    <input type="text" class="form-control" name="no_lisensi" required>
                                </div>
                                <div class="form-group">
                                    <label for="alamat_dokter">Address</label>
                                    <textarea class="form-control" name="alamat_dokter" rows="2" required></textarea>
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
                const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&\-]).{8,}$/;
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
