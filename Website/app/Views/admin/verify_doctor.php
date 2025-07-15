<div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
    <div class="mb-4">
        <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-900 mb-2">Verifikasi Dokter</h3>
            <a href="<?= base_url(
                            "admin/pending-doctor-verifications"
                        ) ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
        <p class="text-sm text-gray-500">Verifikasi informasi dokter untuk memastikan kredensialnya valid</p>
    </div>

    <div class="border border-gray-200 rounded-lg p-6 mb-6">
        <h4 class="text-lg font-medium text-gray-900 mb-4">Informasi Dokter</h4>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                <p class="text-base font-medium"><?= esc(
                                                        $doctor["nama_dokter"]
                                                    ) ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Username</p>
                <p class="text-base font-medium"><?= esc(
                                                        $doctor["username"]
                                                    ) ?></p>
                </disegment
                    <div>
                <p class="text-sm text-gray-500 mb-1">Email</p>
                <p class="text-base font-medium"><?= esc(
                                                        $doctor["email"]
                                                    ) ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">No. Telepon</p>
                <p class="text-base font-medium"><?= esc(
                                                        $doctor["telepon_dokter"]
                                                    ) ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Spesialisasi</p>
                <p class="text-base font-medium"><?= esc(
                                                        $doctor["nama_spesialisasi"]
                                                    ) ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">No. Lisensi</p>
                <p class="text-base font-medium"><?= esc(
                                                        $doctor["no_lisensi"]
                                                    ) ?></p>
            </div>

            <div>
                <p class="text-sm text-gray-500 mb-1">Tanggal Pendaftaran</p>
                <p class="text-base font-medium"><?= date(
                                                        "d M Y, H:i",
                                                        strtotime($doctor["created_at"])
                                                    ) ?></p>
            </div>
        </div>
    </div>

    <form action="<?= base_url(
                        "admin/process-dokter-verification"
                    ) ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="doctor_id" value="<?= $doctor["id_dokter"] ?>">

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keputusan Verifikasi</label>
            <div class="flex items-center space-x-6">
                <div class="flex items-center">
                    <input id="approve" name="status" type="radio" value="approved" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" required>
                    <label for="approve" class="ml-2 block text-sm text-gray-700">Setujui sebagai Dokter</label>
                </div>
                <div class="flex items-center">
                    <input id="reject" name="status" type="radio" value="rejected" class="focus:ring-red-500 h-4 w-4 text-red-600 border-gray-300" required>
                    <label for="reject" class="ml-2 block text-sm text-gray-700">Tolak Verifikasi</label>
                </div>
            </div>
            <p class="mt-2 text-sm text-gray-500">Pilih apakah akan menyetujui atau menolak dokter ini.</p>
        </div>

        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan Verifikasi</label>
            <textarea id="notes" name="notes" rows="4" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Tambahkan catatan verifikasi di sini..."></textarea>
            <p class="mt-2 text-sm text-gray-500">Catatan ini akan disimpan dan dikirim ke dokter bersangkutan.</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Proses Verifikasi
            </button>
        </div>
    </form>
</div>
