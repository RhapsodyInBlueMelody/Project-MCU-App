<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medical Checkup - Complete Registration</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 py-4">
                <h2 class="text-center text-2xl font-bold text-white">
                    Medical Checkup
                </h2>
                <p class="text-center text-white text-sm mt-1">
                    Complete Doctor Registration
                </p>
            </div>

            <div class="p-6">
                <?php if (session()->getFlashdata("error")): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= session()->getFlashdata(
                            "error"
                        ) ?></span>
                    </div>
                <?php endif; ?>

                <div class="text-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Complete Your Registration</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        We need some additional information to complete your doctor registration.
                    </p>
                </div>

                <form action="<?= base_url(
                    "auth/complete-doctor-social-registration"
                ) ?>" method="post" class="mt-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="email" value="<?= $email ?>">
                    <input type="hidden" name="name" value="<?= $name ?>">
                    <input type="hidden" name="oauth_id" value="<?= $oauth_id ?>">
                    <input type="hidden" name="oauth_provider" value="<?= $oauth_provider ?>">

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username <span class="text-red-500">*</span></label>
                            <input type="text" name="username" id="username" value="<?= old(
                                "username"
                            ) ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div>
                            <label for="no_telp_dokter" class="block text-sm font-medium text-gray-700">Nomor Telepon <span class="text-red-500">*</span></label>
                            <input type="text" name="no_telp_dokter" id="no_telp_dokter" value="<?= old(
                                "no_telp_dokter"
                            ) ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>

                        <div>
                            <label for="id_spesialisasi" class="block text-sm font-medium text-gray-700">Spesialisasi <span class="text-red-500">*</span></label>
                            <select name="id_spesialisasi" id="id_spesialisasi" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" required>
                                <option value="">Pilih Spesialisasi</option>
                                <?php foreach ($specializations as $spec): ?>
                                    <option value="<?= $spec[
                                        "id_spesialisasi"
                                    ] ?>" <?= old("id_spesialisasi") ==
$spec["id_spesialisasi"]
    ? "selected"
    : "" ?>>
                                        <?= esc($spec["nama_spesialisasi"]) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="no_lisensi" class="block text-sm font-medium text-gray-700">Nomor Lisensi/STR <span class="text-red-500">*</span></label>
                            <input type="text" name="no_lisensi" id="no_lisensi" value="<?= old(
                                "no_lisensi"
                            ) ?>" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required>
                        </div>
                    </div>

                    <div class="mt-8">
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Complete Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
