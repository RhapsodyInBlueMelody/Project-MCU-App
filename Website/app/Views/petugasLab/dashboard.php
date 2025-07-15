<?php
$statusMap = [
    'ordered'     => 'New',
    'in_progress' => 'In Progress',
    'completed'   => 'Completed',
    'cancelled'   => 'Cancelled'
];
?>

<main class="container mx-auto px-2 md:px-4 py-4 md:py-8">

    <!-- Petugas Lab Profile Card -->
    <div class="bg-white rounded shadow flex flex-col md:flex-row items-center md:items-start p-4 mb-6">
        <?php if (!empty($petugasProfile['foto'])): ?>
            <img src="<?= esc($petugasProfile['foto']) ?>"
                alt="Foto Petugas"
                class="w-24 h-24 rounded-full object-cover mb-4 md:mb-0 md:mr-6 border-2 border-blue-200 shadow-sm" />
        <?php else: ?>
            <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-3xl text-blue-400 mb-4 md:mb-0 md:mr-6">
                <span><?= esc(strtoupper(substr($petugasProfile['nama_petugas_lab'], 0, 1))) ?></span>
            </div>
        <?php endif; ?>
        <div>
            <h2 class="text-lg md:text-2xl font-bold"><?= esc($petugasProfile['nama_petugas_lab']) ?></h2>
            <div class="text-gray-500 text-xs md:text-sm mb-1">NIP: <?= esc($petugasProfile['no_lisensi']) ?></div>
            <div class="text-gray-500 text-xs md:text-sm mb-1"><?= esc($petugasProfile['telepon_petugas_lab']) ?></div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded shadow p-4 text-center">
            <div class="text-xs md:text-sm text-gray-500 mb-1">New Orders</div>
            <div class="text-2xl md:text-3xl font-bold text-blue-600"><?= esc($summary['new_orders'] ?? 0) ?></div>
        </div>
        <div class="bg-white rounded shadow p-4 text-center">
            <div class="text-xs md:text-sm text-gray-500 mb-1">In Progress</div>
            <div class="text-2xl md:text-3xl font-bold text-yellow-500"><?= esc($summary['in_progress'] ?? 0) ?></div>
        </div>
        <div class="bg-white rounded shadow p-4 text-center">
            <div class="text-xs md:text-sm text-gray-500 mb-1">Done Today</div>
            <div class="text-2xl md:text-3xl font-bold text-green-600"><?= esc($summary['done_today'] ?? 0) ?></div>
        </div>
    </div>

    <!-- New Orders Table -->
    <div class="bg-white rounded shadow mb-6">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-base md:text-lg">New Lab Test Orders</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Order #</th>
                        <th class="p-2">Patient</th>
                        <th class="p-2">Test</th>
                        <th class="p-2">Doctor</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($newOrders ?? [] as $order): ?>
                        <tr>
                            <td class="p-2"><?= esc($order['order_number']) ?></td>
                            <td class="p-2"><?= esc($order['nama_pasien']) ?></td>
                            <td class="p-2"><?= esc($order['test_name']) ?></td>
                            <td class="p-2"><?= esc($order['nama_dokter']) ?></td>
                            <td class="p-2"><?= esc($statusMap[$order['status']] ?? $order['status']) ?></td>
                            <td class="p-2">
                                <form action="<?= site_url('petugas_lab/take_order/' . $order['order_number']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-xs md:text-sm" type="submit">
                                        Ambil
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- In Progress Table -->
    <div class="bg-white rounded shadow">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-base md:text-lg">Your In-Progress Tests</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2">Order #</th>
                        <th class="p-2">Patient</th>
                        <th class="p-2">Test</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inProgress ?? [] as $order): ?>
                        <tr>
                            <td class="p-2"><?= esc($order['order_number']) ?></td>
                            <td class="p-2"><?= esc($order['nama_pasien']) ?></td>
                            <td class="p-2"><?= esc($order['test_name']) ?></td>
                            <td class="p-2"><?= esc($statusMap[$order['status']] ?? $order['status']) ?></td>
                            <td class="p-2">
                                <a href="<?= site_url('petugas_lab/complete_order/' . $order['order_number']) ?>"
                                    class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 text-xs md:text-sm">
                                    Complete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
