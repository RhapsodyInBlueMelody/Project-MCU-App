<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Janji Temu Pasien</h1>
    <div class="flex space-x-2 mt-4 md:mt-0">
        <?php
        $statuses = [
            'all' => 'Semua',
            'today' => 'Hari Ini',
            'pending' => 'Menunggu Konfirmasi',
            'awaiting_lab_results' => 'Menunggu Hasil Test Laboratorium',
            'confirmed' => 'Terkonfirmasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
        foreach ($statuses as $key => $label): ?>
            <a href="<?= base_url('dokter/appointments?status=' . $key) ?>"
                class="px-4 py-2 rounded-md text-sm font-medium
           <?= $active_status === $key ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-blue-100' ?>">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-4 border-b bg-blue-600 rounded-t-lg">
        <h2 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-calendar-check mr-2"></i>
            Daftar Janji Temu <?= esc($filter_title) ?>
        </h2>
    </div>
    <div class="overflow-x-auto">
        <?php if (empty($appointments)): ?>
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-4 text-gray-400"></i>
                <p>Tidak ada janji temu pada filter ini.</p>
            </div>
        <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($appointments as $appointment): ?>
                        <?php
                        $status = strtolower($appointment['STATUS'] ?? $appointment['status'] ?? '');
                        $badge = [
                            'pending' => ['bg-yellow-100 text-yellow-800', 'Menunggu'],
                            'confirmed' => ['bg-blue-100 text-blue-800', 'Terkonfirmasi'],
                            'completed' => ['bg-green-100 text-green-800', 'Selesai'],
                            'cancelled' => ['bg-red-100 text-red-800', 'Dibatalkan'],
                            'awaiting_lab_results' => ['bg-gray-100 text-gray-800', 'Menunggu Hasil Lab'],
                            'rejected' => ['bg-red-200 text-red-800', 'Ditolak'],
                        ][$status] ?? ['bg-gray-100 text-gray-800', ucfirst($status)];
                        // Add badge for finished awaiting_lab_results
                        $hasCompletedLab = !empty($appointment['has_completed_lab']) && $appointment['has_completed_lab'];
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d M Y', strtotime($appointment['tanggal_janji'] ?? '')) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('H:i', strtotime($appointment['waktu_janji'] ?? '')) ?> WIB
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900">
                                    <?= esc($appointment['patient_name'] ?? $appointment['nama_pasien'] ?? '-') ?>
                                </span>
                                <?php if (!empty($appointment['patient_phone'])): ?>
                                    <span class="block text-xs text-gray-500"><?= esc($appointment['patient_phone']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= esc($appointment['nama_paket'] ?? 'Tanpa paket') ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge[0] ?>">
                                    <?= $badge[1] ?>
                                </span>
                                <?php if ($status === 'awaiting_lab_results' && $hasCompletedLab): ?>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 animate-pulse">
                                        <i class="fas fa-vial mr-1"></i> Hasil Lab Siap
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?= base_url('dokter/appointment/' . ($appointment['id_janji_temu'] ?? '')) ?>"
                                    class="text-blue-600 hover:text-blue-900 mr-2">
                                    <i class="fas fa-info-circle mr-1"></i>Detail
                                </a>
                                <?php if ($status === 'confirmed' || $status === 'awaiting_lab_results'): ?>
                                    <a href="<?= base_url('dokter/diagnosis/' . ($appointment['id_janji_temu'] ?? '')) ?>"
                                        class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-notes-medical mr-1"></i>Diagnosis
                                    </a>
                                <?php endif ?>
                                <?php if ($status === 'pending'): ?>
                                    <form action="<?= base_url('dokter/appointments/accept/' . $appointment['id_janji_temu']) ?>"
                                        method="post" style="display:inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="text-green-600 hover:text-green-900 mr-2" title="Terima">
                                            <i class="fas fa-check-circle"></i> Terima
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="text-red-600 hover:text-red-900"
                                        onclick="showRejectModal('<?= $appointment['id_janji_temu'] ?>')"
                                        title="Tolak">
                                        <i class="fas fa-times-circle"></i> Tolak
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>
    </div>
</div>

<!-- Modal for rejection reason -->
<div id="rejectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-30 z-50" style="display:none;">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
        <form id="rejectForm" method="post" action="<?= base_url('dokter/appointments/reject') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id_janji_temu" id="rejectAppointmentId">
            <label for="rejection_reason" class="block mb-2 font-semibold">Alasan Penolakan</label>
            <textarea name="rejection_reason" id="rejection_reason" class="w-full border rounded mb-4" required></textarea>
            <div class="flex justify-end gap-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Tolak</button>
                <button type="button" class="bg-gray-300 px-4 py-2 rounded" onclick="hideRejectModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
<script>
    function showRejectModal(id) {
        document.getElementById('rejectAppointmentId').value = id;
        document.getElementById('rejection_reason').value = '';
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function hideRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }
</script>
