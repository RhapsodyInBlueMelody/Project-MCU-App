<main class="container mx-auto px-4 py-8">
    <h1 class="text-xl font-bold mb-4">Kerjakan Order Lab #<?= esc($order['id_test_lab']) ?></h1>
    <div class="bg-white rounded shadow p-4">
        <form action="<?= site_url('petugas_lab/submit_work/' . $order['id_test_lab']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label for="hasil_file" class="block font-semibold mb-2">Upload Laporan Hasil</label>
                <input type="file" name="hasil_file" id="hasil_file" accept=".pdf,.jpg,.png,.doc,.docx" class="block w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label for="notes" class="block font-semibold mb-2">Catatan (opsional):</label>
                <textarea name="notes" id="notes" class="w-full border rounded p-2"><?= esc($order['notes'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan & Selesai</button>
        </form>
        <?php if (!empty($order['hasil_test'])): ?>
            <div class="mt-6">
                <strong>Laporan Saat Ini:</strong>
                <a href="<?= site_url('petugas_lab/download_report/' . $order['id_test_lab']) ?>" class="text-blue-500 underline" target="_blank">Download Hasil</a>
            </div>
        <?php endif ?>
    </div>
</main>
