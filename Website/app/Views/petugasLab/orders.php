<main class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-4">Daftar Order Tes Laboratorium</h1>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-blue-100">
                    <th class="p-2">No Order</th>
                    <th class="p-2">Tanggal Order</th>
                    <th class="p-2">Pasien</th>
                    <th class="p-2">Jenis Tes</th>
                    <th class="p-2">Dokter</th>
                    <th class="p-2">Status</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr class="<?= $order['status'] === 'in_progress' ? 'bg-yellow-50' : ($order['status'] === 'completed' ? 'bg-green-50' : '') ?>">
                        <td class="p-2"><?= esc($order['id_test_lab']) ?></td>
                        <td class="p-2"><?= esc(date('d-m-Y', strtotime($order['created_at']))) ?></td>
                        <td class="p-2"><?= esc($order['nama_pasien'] ?? '-') ?></td>
                        <td class="p-2"><?= esc($order['nama_procedure']) ?></td>
                        <td class="p-2"><?= esc($order['nama_dokter'] ?? '-') ?></td>
                        <td class="p-2 capitalize">
                            <?php if ($order['status'] === 'ordered'): ?>
                                <span class="text-blue-600 font-semibold">Baru</span>
                            <?php elseif ($order['status'] === 'in_progress'): ?>
                                <span class="text-yellow-600 font-semibold">Sedang Dikerjakan</span>
                            <?php else: ?>
                                <span class="text-green-600 font-semibold">Selesai</span>
                            <?php endif ?>
                        </td>
                        <td class="p-2">
                            <?php if ($order['status'] === 'ordered'): ?>
                                <!-- Ambil order -->
                                <form action="<?= site_url('petugas_lab/take_order/' . $order['id']) ?>" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700" type="submit" title="Ambil Order">
                                        Ambil
                                    </button>
                                </form>
                            <?php elseif ($order['status'] === 'in_progress' && $order['id_petugas_lab'] == $petugasLabId): ?>
                                <!-- Kerjakan / Lihat detail -->
                                <a href="<?= site_url('petugas_lab/order_work/' . $order['id_test_lab']) ?>" class="bg-yellow-600 text-white px-3 py-1 rounded hover:bg-yellow-700" title="Kerjakan/Lihat">
                                    Kerjakan
                                </a>
                            <?php elseif ($order['status'] === 'in_progress'): ?>
                                <span class="text-xs text-gray-400">Diambil petugas lain</span>
                            <?php else: ?>
                                <a href="<?= site_url('petugas_lab/order_work/' . $order['id_test_lab']) ?>" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700" title="Lihat Hasil">
                                    Lihat
                                </a>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">Belum ada order tes untuk spesialisasi Anda.</td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
    </div>
</main>
