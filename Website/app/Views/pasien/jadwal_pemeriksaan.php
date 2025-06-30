
<div class="container px-6 py-8 mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800">Jadwal Pemeriksaan</h2>
    <p class="mt-2 text-gray-600">Daftar janji temu Anda yang sudah terjadwal.</p>

    <?php if (session()->has("success")): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->getFlashdata(
                "success"
            ) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->has("error")): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= session()->getFlashdata(
                "error"
            ) ?></span>
        </div>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mt-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button id="tab-upcoming" class="tab-button border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Janji Temu Mendatang
            </button>
            <button id="tab-past" class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Riwayat Janji Temu
            </button>
        </nav>
    </div>

    <!-- Upcoming Appointments Tab Content -->
    <div id="content-upcoming" class="tab-content">
        <?php if (empty($upcoming)): ?>
            <div class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada janji temu</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum memiliki janji temu yang akan datang.</p>
                <div class="mt-6">
                    <a href="<?= base_url(
                        "pasien/appointment"
                    ) ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Buat Janji Temu
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama Janji</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Dokter</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tanggal & Waktu</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($upcoming as $appointment): ?>
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    <?= esc($appointment["nama_janji"]) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= esc($appointment["nama_dokter"]) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= date(
                                        "d M Y",
                                        strtotime($appointment["tanggal_janji"])
                                    ) ?>
                                    <br><?= date(
                                        "H:i",
                                        strtotime($appointment["waktu_janji"])
                                    ) ?> WIB
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <?php if (
                                        $appointment["status"] === "pending"
                                    ): ?>
                                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Menunggu</span>
                                    <?php elseif (
                                        $appointment["status"] === "confirmed"
                                    ): ?>
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Terkonfirmasi</span>
                                    <?php else: ?>
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800"><?= ucfirst(
                                            $appointment["status"]
                                        ) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="<?= base_url(
                                        "pasien/riwayat-pemeriksaan/" .
                                            $appointment["id_janji_temu"]
                                    ) ?>" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                    <?php if (
                                        $appointment["status"] === "pending"
                                    ): ?>
                                        <a href="<?= base_url(
                                            "pasien/cancel-appointment/" .
                                                $appointment["id_janji_temu"]
                                        ) ?>"
                                           class="text-red-600 hover:text-red-900 ml-4"
                                           onclick="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                            Batalkan
                                        </a>
                                    <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Past Appointments Tab Content -->
    <div id="content-past" class="tab-content hidden">
        <?php if (empty($past)): ?>
            <div class="text-center py-10">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada riwayat</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum memiliki riwayat janji temu yang telah selesai.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama Janji</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Dokter</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tanggal & Waktu</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($past as $appointment): ?>
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    <?= esc($appointment["nama_janji"]) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= esc($appointment["nama_dokter"]) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= date(
                                        "d M Y",
                                        strtotime($appointment["tanggal_janji"])
                                    ) ?>
                                    <br><?= date(
                                        "H:i",
                                        strtotime($appointment["waktu_janji"])
                                    ) ?> WIB
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <?php if (
                                        $appointment["status"] === "completed"
                                    ): ?>
                                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Selesai</span>
                                    <?php elseif (
                                        $appointment["status"] === "cancelled"
                                    ): ?>
                                        <span class="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">Dibatalkan</span>
                                    <?php else: ?>
                                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800"><?= ucfirst(
                                            $appointment["status"]
                                        ) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="<?= base_url(
                                        "pasien/riwayat-pemeriksaan/" .
                                            $appointment["id_janji_temu"]) ?>" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add this at the end of jadwal_pemeriksaan.php -->
<script>
    // Define CSRF variables globally
    var baseUrl = '<?= base_url() ?>';
    var csrf_token_name = '<?= csrf_token() ?>';
    var csrf_hash = '<?= csrf_hash() ?>';
</script>
<script src="<?= base_url("assets/js/appointment-schedule.js") ?>"></script>
