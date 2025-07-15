<div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Verifikasi Dokter</h3>
            <p class="text-sm text-gray-500">Daftar dokter yang menunggu verifikasi</p>
        </div>
        <a href="<?= base_url(
                        "admin/dokter-management"
                    ) ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-users-cog mr-2"></i> Manajemen Dokter
        </a>
    </div>

    <?php if (empty($pending_verifications)): ?>
        <div class="bg-blue-50 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Tidak ada permintaan verifikasi</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>Saat ini tidak ada dokter yang menunggu verifikasi.</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spesialisasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Lisensi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl. Daftar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($pending_verifications as $doctor): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-indigo-100 rounded-full">
                                        <i class="fas fa-user-md text-indigo-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?= esc(
                                                                                            $doctor["nama_dokter"]
                                                                                        ) ?></div>
                                        <div class="text-sm text-gray-500"><?= esc(
                                                                                $doctor["email"]
                                                                            ) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc(
                                                                        $doctor["nama_spesialisasi"]
                                                                    ) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= esc(
                                                                        $doctor["no_lisensi"]
                                                                    ) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?= date(
                                                                        "d M Y",
                                                                        strtotime($doctor["created_at"])
                                                                    ) ?></div>
                                <div class="text-sm text-gray-500"><?= date(
                                                                        "H:i",
                                                                        strtotime($doctor["created_at"])
                                                                    ) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Menunggu Verifikasi
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url(
                                                "admin/verify-dokter/" .
                                                    $doctor["id_dokter"]
                                            ) ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Verifikasi</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
