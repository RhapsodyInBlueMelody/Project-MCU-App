<div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-blue-50 via-indigo-50 to-white py-9 px-3">
    <div class="w-full max-w-3xl rounded-3xl shadow-2xl bg-white/90 ring-1 ring-blue-100/60 backdrop-blur-lg">
        <div class="rounded-t-3xl px-8 py-6 bg-gradient-to-r from-indigo-600 to-sky-400 flex items-center gap-3">
            <span class="inline-block bg-white/20 rounded-full p-2">
                <i class="fas fa-notes-medical text-white text-xl"></i>
            </span>
            <h2 class="text-white text-lg sm:text-xl font-bold drop-shadow">Formulir Diagnosis & Pemeriksaan Lab</h2>
        </div>
        <div class="px-8 py-7 sm:py-9">
            <!-- Unified Diagnosis & Approval Form Start -->
            <form action="<?= base_url('dokter/diagnosis/save-diagnosis') ?>" method="post" autocomplete="off" class="space-y-5 mb-10">
                <?= csrf_field() ?>
                <input type="hidden" name="id_janji_temu" value="<?= esc($appointment['id_janji_temu']) ?>">
                <input type="hidden" name="id_pasien" value="<?= esc($appointment['id_pasien']) ?>">
                <input type="hidden" name="id_dokter" value="<?= esc($appointment['id_dokter']) ?>">

                <div>
                    <label for="symptoms" class="block text-sm font-semibold text-blue-900 mb-1">Gejala/Keluhan</label>
                    <textarea id="symptoms" name="symptoms" rows="3" required
                        class="block w-full rounded-xl border border-blue-200 bg-blue-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 text-gray-900 shadow-sm transition disabled:bg-gray-100"><?= old('symptoms', $diagnosis['symptoms'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="diagnosis_result" class="block text-sm font-semibold text-blue-900 mb-1">Hasil Diagnosis</label>
                    <textarea id="diagnosis_result" name="diagnosis_result" rows="3" required
                        class="block w-full rounded-xl border border-blue-200 bg-blue-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 text-gray-900 shadow-sm transition disabled:bg-gray-100"><?= old('diagnosis_result', $diagnosis['diagnosis_result'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="treatment_plan" class="block text-sm font-semibold text-blue-900 mb-1">Rencana Tindakan / Terapi</label>
                    <textarea id="treatment_plan" name="treatment_plan" rows="2"
                        class="block w-full rounded-xl border border-blue-200 bg-blue-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 text-gray-900 shadow-sm transition disabled:bg-gray-100"><?= old('treatment_plan', $diagnosis['treatment_plan'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-semibold text-blue-900 mb-1">Catatan Tambahan</label>
                    <textarea id="notes" name="notes" rows="2"
                        class="block w-full rounded-xl border border-blue-200 bg-blue-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 text-gray-900 shadow-sm transition disabled:bg-gray-100"><?= old('notes', $diagnosis['notes'] ?? '') ?></textarea>
                </div>
                <div>
                    <label for="tanggal_hasil_lab" class="block text-sm font-semibold text-blue-900 mb-1">Tanggal Hasil Lab <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="date" id="tanggal_hasil_lab" name="tanggal_hasil_lab"
                        value="<?= old('tanggal_hasil_lab', $diagnosis['tanggal_hasil_lab'] ?? '') ?>"
                        class="block w-full rounded-xl border border-blue-200 bg-blue-50 focus:bg-white focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 text-gray-900 shadow-sm transition disabled:bg-gray-100" />
                </div>
                <div>
                    <label class="block text-sm font-semibold text-blue-900 mb-1">Perlu Tes Laboratorium?</label>
                    <div class="flex gap-4">
                        <label>
                            <input type="radio" name="require_lab" value="1" <?= old('require_lab', $require_lab) == '1' ? 'checked' : '' ?> required>
                            Ya
                        </label>
                        <label>
                            <input type="radio" name="require_lab" value="0" <?= old('require_lab', $require_lab) == '0' ? 'checked' : '' ?>>
                            Tidak
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-blue-900 mb-1">Pilih Tes Lab</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" id="lab-tests-section">
                        <?php foreach ($lab_tests as $test): ?>
                            <label class="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    name="lab_tests[]"
                                    value="<?= esc($test['id_lab_test_procedure']) ?>"
                                    <?= in_array($test['id_lab_test_procedure'], old('lab_tests', $checked_lab_tests)) ? 'checked' : '' ?>>
                                <?= esc($test['nama_procedure']) ?> <span class="text-xs text-gray-500">(<?= number_format($test['price'], 0, ',', '.') ?>)</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <small class="text-gray-500">Centang tes lab jika diperlukan.</small>
                </div>

                <!-- Lab Result Approval Table -->
                <hr class="my-8 border-blue-100">
                <h3 class="text-blue-900 font-semibold mb-3">Approval Hasil Pemeriksaan Lab</h3>
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200 bg-white">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jenis Tes</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Tes</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hasil</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Approval</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($orderedLabTests as $test): ?>
                                <?php
                                $labStatus = strtolower($test['status'] ?? '');
                                $labBadge = [
                                    'ordered' => ['bg-yellow-100 text-yellow-800', 'Ordered'],
                                    'in_progress' => ['bg-blue-100 text-blue-800', 'In Progress'],
                                    'completed' => ['bg-green-100 text-green-800', 'Completed'],
                                    'cancelled' => ['bg-red-100 text-red-800', 'Cancelled'],
                                ][$labStatus] ?? ['bg-gray-100 text-gray-800', ucfirst($labStatus)];
                                ?>
                                <tr>
                                    <td class="px-4 py-2"><?= esc($test['nama_procedure'] ?? '-') ?></td>
                                    <td class="px-4 py-2"><?= isset($test['tanggal_test']) ? date('d M Y H:i', strtotime($test['tanggal_test'])) : '-' ?></td>
                                    <td>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $labBadge[0] ?>">
                                            <?= $labBadge[1] ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
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
                                    <td class="px-4 py-2">
                                        <?php if ($labStatus === 'completed'): ?>
                                            <input type="checkbox" name="approve_lab_tests[]" value="<?= esc($test['id_test_lab']) ?>"
                                                <?= !empty($test['approved_by_doctor']) ? 'checked disabled' : '' ?>>
                                            <?php if (!empty($test['approved_by_doctor'])): ?>
                                                <span class="text-green-600 text-xs ml-1">Sudah Disetujui</span>
                                            <?php endif ?>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400">-</span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
                <div class="flex flex-col sm:flex-row justify-end gap-4 pt-2">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-green-600 to-blue-400 hover:from-green-700 hover:to-blue-500 text-white font-bold px-8 py-2.5 shadow-lg transition focus:outline-none focus:ring-2 focus:ring-green-200 text-lg">
                        <i class="fas fa-save"></i> Simpan Diagnosis & Approval
                    </button>
                    <a href="<?= base_url('dokter/appointments') ?>"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-100 font-semibold px-8 py-2.5 shadow transition text-lg">
                        Batal
                    </a>
                </div>
            </form>
            <!-- Unified Form End -->
        </div>
    </div>
</div>
<!-- Modal for View Report (uses JS) -->
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
