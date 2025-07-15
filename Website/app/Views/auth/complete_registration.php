<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= esc($title ?? "Registrasi") ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center py-8">
    <div class="bg-white shadow-xl rounded-xl w-full max-w-lg mx-auto p-8">
        <h1 class="text-2xl font-bold text-blue-700 mb-2 text-center"><?= esc($title) ?></h1>
        <p class="mb-6 text-gray-600 text-center">Silakan lengkapi data di bawah ini untuk melanjutkan.</p>
        <?php if (session()->getFlashdata("error")): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                <?= session()->getFlashdata("error") ?>
            </div>
        <?php endif; ?>

        <?= form_open("auth/complete-social-registration", ['class' => 'space-y-4']) ?>
        <?= csrf_field() ?>
        <input type="hidden" name="role" value="<?= esc($role ?? '') ?>">

        <?php
        $email = $google_data['email'] ?? old('email');
        $username = old('username');
        ?>
        <?= view('partials/account_info', compact('email', 'username')) ?>
        <?= view('partials/password_fields') ?>

        <?php if ($role === "dokter"): ?>
            <?= view('partials/dokter_profile', []) ?>
            <?= view('partials/spesialisasi_dropdown', [
                'list' => array_map(fn($s) => [
                    'id' => $s['id_spesialisasi'],
                    'nama' => $s['nama_spesialisasi']
                ], $spesialisasiList),
                'name' => 'id_spesialisasi',
                'label' => 'Keahlian'
            ]) ?>
        <?php elseif ($role === "petugas_lab"): ?>
            <?= view('partials/petugas_lab_profile', []) ?>
            <?= view('partials/spesialisasi_dropdown', [
                'list' => array_map(fn($s) => [
                    'id' => $s['id_spesialisasi_lab'],
                    'nama' => $s['nama_spesialisasi']
                ], $spesialisasiList),
                'name' => 'id_spesialisasi_lab',
                'label' => 'Spesialisasi Lab'
            ]) ?>
        <?php elseif ($role === "pasien"): ?>
            <?= view('partials/pasien_profile', []) ?>
        <?php endif; ?>

        <button type="submit"
            class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-md transition-colors duration-150">
            Lengkapi Registrasi
        </button>
        <?= form_close() ?>
    </div>
</body>

</html>
