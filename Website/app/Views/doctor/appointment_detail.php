<div class="max-w-3xl mx-auto my-8 bg-white rounded-2xl shadow-xl ring-1 ring-blue-100/60 backdrop-blur-lg">
    <div class="rounded-t-2xl px-8 py-6 bg-gradient-to-r from-indigo-600 to-sky-400 flex items-center gap-3">
        <span class="inline-block bg-white/20 rounded-full p-2">
            <i class="fas fa-calendar-check text-white text-xl"></i>
        </span>
        <h2 class="text-white text-lg sm:text-xl font-bold drop-shadow"><?= esc($title ?? "Detail Janji Temu") ?></h2>
    </div>
    <div class="px-8 py-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
        <!-- Appointment Info -->
        <div>
            <h3 class="text-blue-900 font-semibold mb-3">Info Janji Temu</h3>
            <dl class="divide-y divide-blue-100 text-sm">
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">ID Janji Temu</dt>
                    <dd><?= esc($appointment['id_janji_temu'] ?? '-') ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Tanggal</dt>
                    <dd><?= isset($appointment['tanggal_janji']) ? date('d M Y', strtotime($appointment['tanggal_janji'])) : '-' ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Waktu</dt>
                    <dd><?= isset($appointment['waktu_janji']) ? date('H:i', strtotime($appointment['waktu_janji'])) . " WIB" : '-' ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Status</dt>
                    <dd>
                        <?php
                        $status = strtolower($appointment['status'] ?? '');
                        $badge = [
                            'pending' => ['bg-yellow-100 text-yellow-800', 'Menunggu'],
                            'confirmed' => ['bg-blue-100 text-blue-800', 'Terkonfirmasi'],
                            'completed' => ['bg-green-100 text-green-800', 'Selesai'],
                            'cancelled' => ['bg-red-100 text-red-800', 'Dibatalkan'],
                            'awaiting_lab_results' => ['bg-gray-100 text-gray-800', 'Menunggu Hasil Lab'],
                        ][$status] ?? ['bg-gray-100 text-gray-800', ucfirst($status)];
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $badge[0] ?>">
                            <?= $badge[1] ?>
                        </span>
                    </dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Paket</dt>
                    <dd><?= esc($appointment['nama_paket'] ?? 'Tanpa paket') ?></dd>
                </div>
            </dl>
        </div>

        <!-- Patient Info -->
        <div>
            <h3 class="text-blue-900 font-semibold mb-3">Data Pasien</h3>
            <dl class="divide-y divide-blue-100 text-sm">
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Nama</dt>
                    <dd><?= esc($patient['nama_pasien'] ?? '-') ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">No. Rekam Medis</dt>
                    <dd><?= esc($patient['id_pasien'] ?? '-') ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Tanggal Lahir</dt>
                    <dd><?= isset($patient['tanggal_lahir']) ? date('d M Y', strtotime($patient['tanggal_lahir'])) : '-' ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Jenis Kelamin</dt>
                    <dd><?= esc($patient['jenis_kelamin'] ?? '-') ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Telepon</dt>
                    <dd><?= esc($patient['telepon'] ?? '-') ?></dd>
                </div>
                <div class="py-2 flex justify-between">
                    <dt class="font-medium">Alamat</dt>
                    <dd><?= esc($patient['alamat'] ?? '-') ?></dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Diagnosis section -->
    <div class="border-t px-8 py-6">
        <h3 class="text-blue-900 font-semibold mb-3">Diagnosis</h3>
        <?php if ($diagnosis): ?>
            <dl class="divide-y divide-blue-100 text-sm">
                <div class="py-2">
                    <dt class="font-medium">Gejala/Keluhan</dt>
                    <dd><?= esc($diagnosis['symptoms'] ?? '-') ?></dd>
                </div>
                <div class="py-2">
                    <dt class="font-medium">Hasil Diagnosis</dt>
                    <dd><?= esc($diagnosis['diagnosis_result'] ?? '-') ?></dd>
                </div>
                <div class="py-2">
                    <dt class="font-medium">Rencana Tindakan/Terapi</dt>
                    <dd><?= esc($diagnosis['treatment_plan'] ?? '-') ?></dd>
                </div>
                <div class="py-2">
                    <dt class="font-medium">Catatan Tambahan</dt>
                    <dd><?= esc($diagnosis['notes'] ?? '-') ?></dd>
                </div>
                <?php if (!empty($diagnosis['tanggal_hasil_lab'])): ?>
                    <div class="py-2">
                        <dt class="font-medium">Tanggal Hasil Lab</dt>
                        <dd><?= date('d M Y', strtotime($diagnosis['tanggal_hasil_lab'])) ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        <?php else: ?>
            <div class="text-gray-500 italic">Belum ada diagnosis untuk janji temu ini.</div>
        <?php endif ?>
    </div>

    <!-- Lab Test section -->
    <div class="border-t px-8 py-6">
        <h3 class="text-blue-900 font-semibold mb-3">Pemeriksaan Lab</h3>
        <?php if (!empty($lab_tests)): ?>
            <table class="min-w-full divide-y divide-gray-200 bg-white">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Tes</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Tes</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hasil</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($lab_tests as $test): ?>
                        <tr>
                            <td class="px-4 py-2"><?= esc($test['nama_procedure'] ?? '-') ?></td>
                            <td class="px-4 py-2"><?= isset($test['tanggal_test']) ? date('d M Y H:i', strtotime($test['tanggal_test'])) : '-' ?></td>
                            <td class="px-4 py-2">
                                <?php
                                $labStatus = strtolower($test['status'] ?? '');
                                $labBadge = [
                                    'ordered' => ['bg-yellow-100 text-yellow-800', 'Ordered'],
                                    'in_progress' => ['bg-blue-100 text-blue-800', 'In Progress'],
                                    'completed' => ['bg-green-100 text-green-800', 'Completed'],
                                    'cancelled' => ['bg-red-100 text-red-800', 'Cancelled'],
                                ][$labStatus] ?? ['bg-gray-100 text-gray-800', ucfirst($labStatus)];
                                ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $labBadge[0] ?>">
                                    <?= $labBadge[1] ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($test['hasil_test'])): ?>
                                    <button type="button" class="view-report bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-700"
                                        data-url="<?= site_url('dokter/download_report/' . $test['id_test_lab']) ?>"
                                        data-filename="<?= esc($test['hasil_test']) ?>">
                                        Lihat
                                    </button>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">Belum ada hasil</span>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-gray-500 italic">Belum ada pemeriksaan lab untuk janji temu ini.</div>
        <?php endif ?>
    </div>
</div>


<div id="reportModal" class="fixed inset-0 hidden bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-lg w-full">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold" id="modalFileName"></h4>
            <button onclick="closeModal()" class="text-red-500 text-xl">&times;</button>
        </div>
        <iframe id="reportFrame" src="" class="w-full h-96 border rounded"></iframe>
    </div>
</div>
<script>
    document.querySelectorAll('.view-report').forEach(function(btn) {
        btn.onclick = function() {
            document.getElementById('reportFrame').src = btn.dataset.url;
            document.getElementById('modalFileName').innerText = btn.dataset.filename;
            document.getElementById('reportModal').classList.remove('hidden');
        };
    });

    function closeModal() {
        document.getElementById('reportModal').classList.add('hidden');
        document.getElementById('reportFrame').src = '';
    }
    // Lab test section enable/disable logic
    function toggleLabTests() {
        var requireLab = document.querySelector('input[name="require_lab"]:checked');
        var enable = requireLab && requireLab.value == "1";
        var checkboxes = document.querySelectorAll('#lab-tests-section input[type="checkbox"]');
        checkboxes.forEach(function(box) {
            box.disabled = !enable;
            if (!enable) box.checked = false;
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="require_lab"]').forEach(function(input) {
            input.addEventListener('change', toggleLabTests);
        });
        toggleLabTests(); // Initial state
    });
</script>
