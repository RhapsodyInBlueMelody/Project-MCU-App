<!-- app/Views/patient/dashboard.php -->
<div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Welcome Card -->
    <div class="p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
        <h1 class="text-2xl font-bold mb-2">Selamat Datang, <?= esc(
            session()->get("username") ?? "Pasien"
        ) ?></h1>
        <p class="opacity-90">Kelola kesehatan Anda dengan mudah melalui portal pasien kami.</p>
    </div>

    <!-- Quick Actions -->
    <div class="p-6 border-b">
        <h2 class="text-xl font-semibold mb-4">Tindakan Cepat</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="<?= base_url(
                "patient/appointment"
            ) ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border flex items-center transition duration-150">
                <div class="rounded-full bg-blue-100 p-3 mr-4">
                    <i class="fas fa-calendar-plus text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-medium">Buat Janji Temu</h3>
                    <p class="text-sm text-gray-600">Jadwalkan kunjungan baru</p>
                </div>
            </a>

            <a href="<?= base_url(
                "patient/jadwal-pemeriksaan"
            ) ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border flex items-center transition duration-150">
                <div class="rounded-full bg-green-100 p-3 mr-4">
                    <i class="fas fa-stethoscope text-green-600"></i>
                </div>
                <div>
                    <h3 class="font-medium">Jadwal Pemeriksaan</h3>
                    <p class="text-sm text-gray-600">Lihat janji temu mendatang</p>
                </div>
            </a>

            <a href="<?= base_url(
                "patient/riwayat-medical-checkup"
            ) ?>" class="bg-gray-50 hover:bg-gray-100 p-4 rounded-lg border flex items-center transition duration-150">
                <div class="rounded-full bg-purple-100 p-3 mr-4">
                    <i class="fas fa-file-medical-alt text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-medium">Riwayat Kesehatan</h3>
                    <p class="text-sm text-gray-600">Akses riwayat medical check-up</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="p-6">
        <h2 class="text-xl font-semibold mb-4">Janji Temu Mendatang</h2>

        <?php if (
            isset($upcoming_appointments) &&
            !empty($upcoming_appointments)
        ): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Janji</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokter</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach (
                            $upcoming_appointments
                            as $appointment
                        ): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= date(
                                "d M Y",
                                strtotime($appointment["TANGGAL_JANJI"])
                            ) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= date(
                                "H:i",
                                strtotime($appointment["WAKTU_JANJI"])
                            ) ?> WIB</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"><?= esc(
                                $appointment["NAMA_JANJI"]
                            ) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm"><?= esc(
                                $appointment["NAMA_DOKTER"]
                            ) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if (
                                    $appointment["STATUS"] === "pending"
                                ): ?>
                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Menunggu</span>
                                <?php elseif (
                                    $appointment["STATUS"] === "confirmed"
                                ): ?>
                                    <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Terkonfirmasi</span>
                                <?php else: ?>
                                    <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800"><?= ucfirst(
                                        $appointment["STATUS"]
                                    ) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600">
                                <a href="<?= base_url(
                                    "patient/riwayat-pemeriksaan/" .
                                        $appointment["ID_JANJI_TEMU"]
                                ) ?>" class="hover:text-indigo-900">Detail</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada janji temu</h3>
                <p class="mt-1 text-sm text-gray-500">Anda belum memiliki janji temu mendatang.</p>
                <div class="mt-6">
                    <a href="<?= base_url(
                        "patient/appointment"
                    ) ?>" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i> Buat Janji Temu
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Health Tips -->
    <div class="p-6 bg-gray-50">
        <h2 class="text-xl font-semibold mb-4">Tips Kesehatan</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-lg border shadow-sm">
                <h3 class="font-medium text-lg mb-2">Pentingnya Medical Check-Up Rutin</h3>
                <p class="text-gray-600">Pemeriksaan kesehatan rutin dapat membantu mendeteksi masalah kesehatan sejak dini. Kami merekomendasikan melakukan medical check-up lengkap setidaknya setahun sekali.</p>
            </div>
            <div class="bg-white p-4 rounded-lg border shadow-sm">
                <h3 class="font-medium text-lg mb-2">Hidup Sehat Setiap Hari</h3>
                <p class="text-gray-600">Konsumsi makanan bergizi, rutin berolahraga, istirahat cukup, dan kelola stres adalah kunci untuk menjaga kesehatan optimal.</p>
            </div>
        </div>
    </div>
</div>

<!-- Health Stats Widget - Lazy loaded -->
<div class="mt-6 bg-white shadow rounded-lg overflow-hidden" id="health-stats-container">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-xl font-semibold">Statistik Kesehatan</h2>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Placeholder loading skeleton -->
            <div class="health-stat-placeholder skeleton h-24 rounded"></div>
            <div class="health-stat-placeholder skeleton h-24 rounded"></div>
            <div class="health-stat-placeholder skeleton h-24 rounded"></div>
        </div>
    </div>
</div>

<!-- Lazy load health stats -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Wait until user scrolls near stats to load them
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        loadHealthStats();
                        observer.disconnect();
                    }, 500); // Small delay for better user experience
                }
            });
        }, {
            rootMargin: '100px' // Start loading when within 100px
        });

        observer.observe(document.getElementById('health-stats-container'));

        function loadHealthStats() {
            fetch(`${baseUrl}/patient/api/health-stats`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = document.getElementById('health-stats-container').querySelector('.grid');
                    container.innerHTML = ''; // Clear placeholders

                    // Create actual stats UI
                    const stats = [
                        { label: 'Kunjungan Terakhir', value: data.lastVisit || 'Belum ada', icon: 'fa-calendar-check' },
                        { label: 'Total Pemeriksaan', value: data.totalCheckups || '0', icon: 'fa-clipboard-check' },
                        { label: 'Status Kesehatan', value: data.healthStatus || 'Belum diperiksa', icon: 'fa-heartbeat' }
                    ];

                    stats.forEach(stat => {
                        const statEl = document.createElement('div');
                        statEl.className = 'bg-gray-50 p-4 rounded-lg border';
                        statEl.innerHTML = `
                            <div class="flex items-center">
                                <div class="rounded-full bg-blue-100 p-3 mr-3">
                                    <i class="fas ${stat.icon} text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">${stat.label}</p>
                                    <p class="text-lg font-semibold">${stat.value}</p>
                                </div>
                            </div>
                        `;
                        container.appendChild(statEl);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading health stats:', error);
            });
        }
    });
</script>
