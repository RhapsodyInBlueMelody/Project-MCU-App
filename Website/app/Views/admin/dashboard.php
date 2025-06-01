<div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8 mb-4">
    <h1 class="text-xl font-bold mb-4">Dashboard Admin</h1>
    <p class="text-gray-500 mb-6">Selamat datang di panel admin Medical Check Up System.</p>

    <!-- Stats widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Doctors Widget -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-md overflow-hidden">
            <div class="p-4 flex items-start justify-between">
                <div>
                    <p class="text-white text-sm font-medium">Total Dokter</p>
                    <h3 class="text-white text-2xl font-bold mt-1"><?= $total_doctors ?></h3>
                </div>
                <div class="rounded-full bg-white/20 p-3">
                    <i class="fas fa-user-md text-white text-xl"></i>
                </div>
            </div>
            <div class="bg-blue-700 px-4 py-2">
                <a href="<?= base_url(
                    "admin/doctor-management"
                ) ?>" class="text-white text-xs flex items-center hover:underline">
                    <span>Lihat Semua</span>
                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Pending Verifications Widget -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow-md overflow-hidden">
            <div class="p-4 flex items-start justify-between">
                <div>
                    <p class="text-white text-sm font-medium">Verifikasi Pending</p>
                    <h3 class="text-white text-2xl font-bold mt-1"><?= $pending_verifications ?></h3>
                </div>
                <div class="rounded-full bg-white/20 p-3">
                    <i class="fas fa-user-check text-white text-xl"></i>
                </div>
            </div>
            <div class="bg-yellow-700 px-4 py-2">
                <a href="<?= base_url(
                    "admin/pending-doctor-verifications"
                ) ?>" class="text-white text-xs flex items-center hover:underline">
                    <span>Verifikasi Sekarang</span>
                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Total Patients Widget -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-md overflow-hidden">
            <div class="p-4 flex items-start justify-between">
                <div>
                    <p class="text-white text-sm font-medium">Total Pasien</p>
                    <h3 class="text-white text-2xl font-bold mt-1"><?= $total_patients ?></h3>
                </div>
                <div class="rounded-full bg-white/20 p-3">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
            <div class="bg-green-700 px-4 py-2">
                <a href="<?= base_url(
                    "admin/patient-management"
                ) ?>" class="text-white text-xs flex items-center hover:underline">
                    <span>Lihat Semua</span>
                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>

        <!-- Total Appointments Widget -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-md overflow-hidden">
            <div class="p-4 flex items-start justify-between">
                <div>
                    <p class="text-white text-sm font-medium">Total Janji Temu</p>
                    <h3 class="text-white text-2xl font-bold mt-1"><?= $total_appointments ?></h3>
                </div>
                <div class="rounded-full bg-white/20 p-3">
                    <i class="fas fa-calendar-check text-white text-xl"></i>
                </div>
            </div>
            <div class="bg-purple-700 px-4 py-2">
                <a href="<?= base_url(
                    "admin/appointment-management"
                ) ?>" class="text-white text-xs flex items-center hover:underline">
                    <span>Lihat Semua</span>
                    <i class="fas fa-arrow-right ml-1 text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Chart and Recent Appointments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Statistik Janji Temu</h3>
            <div>
                <canvas id="appointmentChart" height="300"></canvas>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Janji Temu Terbaru</h3>
                <a href="<?= base_url(
                    "admin/appointment-management"
                ) ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
            </div>

            <?php if (empty($recent_appointments)): ?>
                <div class="text-center py-4 text-gray-500">
                    <p>Belum ada janji temu.</p>
                </div>
            <?php else: ?>
                <div class="flow-root">
                    <ul role="list" class="divide-y divide-gray-200">
                        <?php foreach ($recent_appointments as $appointment): ?>
                            <li class="py-3 sm:py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-gray-100">
                                            <i class="fas fa-user-circle text-gray-500 text-xl"></i>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            <?= esc(
                                                $appointment["patient_name"]
                                            ) ?>
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">
                                            <?= esc(
                                                $appointment["NAMA_JANJI"]
                                            ) ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= date(
                                                "d M Y",
                                                strtotime(
                                                    $appointment[
                                                        "TANGGAL_JANJI"
                                                    ]
                                                )
                                            ) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= date(
                                                "H:i",
                                                strtotime(
                                                    $appointment["WAKTU_JANJI"]
                                                )
                                            ) ?>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sample data - replace with actual data from your backend
        const ctx = document.getElementById('appointmentChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Janji Temu',
                    data: [12, 19, 3, 5, 2, 3, 20, 33, 23, 12, 33, 10],
                    backgroundColor: 'rgba(79, 70, 229, 0.2)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
