<!DOCTYPE html>
<html lang="en">

<head>
    <title>Medical Checkup - Patient Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gray-300 flex items-center justify-center">
    <div class="w-full max-w-md mx-auto bg-white shadow-lg rounded-xl p-8">
        <h2 class="text-2xl font-bold text-blue-700 mb-2 text-center leading-tight">Medical Checkup <br><?= esc($title) ?></h2>

        <?php if (session()->getFlashdata("msg")): ?>
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-sm text-center">
                <?= session()->getFlashdata("msg") ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata("success")): ?>
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4 text-sm text-center">
                <?= session()->getFlashdata("success") ?>
            </div>
        <?php endif; ?>

        <hr class="my-4">

        <form action="<?= base_url("auth/authenticate") ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="role" value="pasien">
            <div>
                <label for="username" class="block font-semibold text-gray-700 mb-1">Nama Pengguna atau Email</label>
                <input type="text" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
                    name="username" id="username" placeholder="Masukkan username atau email" required>
            </div>
            <div>
                <label for="password" class="block font-semibold text-gray-700 mb-1">Sandi</label>
                <input type="password" class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
                    name="password" id="password" placeholder="Masukkan sandi" required>
            </div>
            <div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition-colors duration-150">
                    Login
                </button>
            </div>
            <a href="<?= base_url("auth/google/login/pasien") ?>"
                class="flex items-center justify-center w-full mt-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 rounded transition">
                <img src="https://www.gstatic.com/marketing-cms/assets/images/d5/dc/cfe9ce8b4425b410b49b7f2dd3f3/g.webp=s48-fcrop64=1,00000000ffffffff-rw"
                    alt="Google Icon" class="w-5 h-5 mr-2">
                Login dengan Google
            </a>
            <div class="mt-4 text-center text-gray-600">
                <p class="mb-0">Belum punya akun?
                    <a href="<?= base_url("auth/register/social/pasien") ?>" class="text-blue-600 hover:underline font-semibold">
                        Daftar di sini
                    </a>
                </p>
            </div>
        </form>
    </div>
</body>

</html>
