<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Dokter</h1>
    <div class="flex mt-4 md:mt-0">
        <a href="<?= base_url(
            "doctor/appointments/today"
        ) ?>" class="inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-calendar-day mr-2"></i> Janji Temu Hari Ini
        </a>
        <a href="<?= base_url(
            "doctor/schedule"
        ) ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <i class="fas fa-calendar-alt mr-2"></i> Jadwal Saya
        </a>
    </div>
</div>

<!-- Doctor Profile Card -->
<?php if (isset($doctor_info)): ?>
<div class="mb-6">
    <div class="bg-white overflow-hidden shadow-md rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col md:flex-row items-center">
                <div class="flex-shrink-0 flex items-center justify-center h-24 w-24 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-md text-5xl"></i>
                </div>
                <div class="mt-4 md:mt-0 md:ml-6 text-center md:text-left">
                    <h3 class="text-xl font-semibold text-gray-900"><?= esc(
                        $doctor_info["NAMA_DOKTER"]
                    ) ?></h3>
                    <p class="text-sm text-gray-500"><?= esc(
                        $doctor_info["nama_spesialisasi"]
                    ) ?></p>
                    <div class="mt-2 text-sm text-gray-700">
                        <div class="flex items-center justify-center md:justify-start mb-1">
                            <i class="fas fa-id-card mr-2 text-gray-500"></i>
                            <span><?= esc($doctor_info["NO_LISENSI"]) ?></span>
                        </div>
                        <div class="flex items-center justify-center md:justify-start">
                            <i class="fas fa-phone mr-2 text-gray-500"></i>
                            <span><?= esc(
                                $doctor_info["NO_TELP_DOKTER"]
                            ) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-4">
    <!-- Today's Appointments -->
    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-blue-500">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Janji Temu Hari Ini</dt>
                        <dd class="mt-1">
                            <div class="text-2xl font-semibold text-gray-900"><?= count(
                                $today_appointments
                            ) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url(
                    "doctor/appointments/today"
                ) ?>" class="font-medium text-blue-600 hover:text-blue-500">Lihat semua</a>
            </div>
        </div>
    </div>

    <!-- Pending Confirmations -->
    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-yellow-500">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Konfirmasi</dt>
                        <dd class="mt-1">
                            <div class="text-2xl font-semibold text-gray-900"><?= count(
                                $pending_appointments
                            ) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url(
                    "doctor/appointments/pending"
                ) ?>" class="font-medium text-yellow-600 hover:text-yellow-500">Lihat semua</a>
            </div>
        </div>
    </div>

    <!-- Awaiting Diagnosis -->
    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-indigo-500">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <i class="fas fa-stethoscope text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Menunggu Diagnosis</dt>
                        <dd class="mt-1">
                            <div class="text-2xl font-semibold text-gray-900"><?= count(
                                $diagnosis_queue
                            ) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url(
                    "doctor/appointments/confirmed"
                ) ?>" class="font-medium text-indigo-600 hover:text-indigo-500">Lihat semua</a>
            </div>
        </div>
    </div>

    <!-- New Lab Results -->
    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-green-500">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-flask text-green-600 text-xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Hasil Lab Baru</dt>
                        <dd class="mt-1">
                            <div class="text-2xl font-semibold text-gray-900"><?= count(
                                $patients_with_lab_results
                            ) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="#lab-results-section" class="font-medium text-green-600 hover:text-green-500">Lihat semua</a>
            </div>
        </div>
    </div>
</div>

<!-- Today's Appointments Section -->
<div class="mt-8">
    <div class="bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 bg-blue-600">
            <h3 class="text-lg leading-6 font-medium text-white">
                <i class="fas fa-calendar-day mr-2"></i> Janji Temu Hari Ini
            </h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <?php if (empty($today_appointments)): ?>
                <div class="text-center py-10 text-gray-500">
                    <i class="fas fa-calendar-times text-5xl mb-4 text-gray-400"></i>
                    <p>Tidak ada janji temu untuk hari ini.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Janji Temu</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach (
                                $today_appointments
                                as $appointment
                            ): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?= date(
                                            "H:i",
                                            strtotime(
                                                $appointment["WAKTU_JANJI"]
                                            )
                                        ) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= esc(
                                            $appointment["patient_name"]
                                        ) ?></div>
                                        <?php if (
                                            isset($appointment["patient_phone"])
                                        ): ?>
                                            <div class="text-sm text-gray-500"><?= esc(
                                                $appointment["patient_phone"]
                                            ) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($appointment["NAMA_JANJI"]) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc(
                                            $appointment["nama_paket"] ??
                                                "Tanpa paket"
                                        ) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status = $appointment["STATUS"];
                                        $statusClass = "";
                                        $statusText = "";

                                        switch ($status) {
                                            case "pending":
                                                $statusClass =
                                                    "bg-yellow-100 text-yellow-800";
                                                $statusText = "Menunggu";
                                                break;
                                            case "confirmed":
                                                $statusClass =
                                                    "bg-blue-100 text-blue-800";
                                                $statusText = "Terkonfirmasi";
                                                break;
                                            case "completed":
                                                $statusClass =
                                                    "bg-green-100 text-green-800";
                                                $statusText = "Selesai";
                                                break;
                                            case "cancelled":
                                                $statusClass =
                                                    "bg-red-100 text-red-800";
                                                $statusText = "Dibatalkan";
                                                break;
                                            case "awaiting_lab_results":
                                                $statusClass =
                                                    "bg-gray-100 text-gray-800";
                                                $statusText =
                                                    "Menunggu Hasil Lab";
                                                break;
                                            default:
                                                $statusClass =
                                                    "bg-gray-100 text-gray-800";
                                                $statusText = $status;
                                        }
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                            <?= $statusText ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?= base_url(
                                            "doctor/appointment/" .
                                                $appointment["ID_JANJI_TEMU"]
                                        ) ?>" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                        <?php if ($status === "confirmed"): ?>
                                            <a href="<?= base_url(
                                                "doctor/diagnosis/" .
                                                    $appointment[
                                                        "ID_JANJI_TEMU"
                                                    ]
                                            ) ?>" class="text-indigo-600 hover:text-indigo-900">Diagnosis</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Two Column Content - Pending Confirmations and Awaiting Diagnosis -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
    <!-- Pending Appointments -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-yellow-600 px-4 py-4">
            <h3 class="text-lg leading-6 font-medium text-white">
                <i class="fas fa-clock mr-2"></i> Menunggu Konfirmasi
            </h3>
        </div>
        <div class="p-4">
            <?php if (empty($pending_appointments)): ?>
                <div class="text-center py-8 text-gray-500">
                    <p>Tidak ada janji temu yang menunggu konfirmasi.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach (
                        array_slice($pending_appointments, 0, 5)
                        as $appointment
                    ): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-md transition duration-150">
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900"><?= esc(
                                            $appointment["patient_name"]
                                        ) ?></h4>
                                        <p class="text-sm text-gray-600"><?= esc(
                                            $appointment["NAMA_JANJI"]
                                        ) ?></p>
                                        <div class="flex items-center mt-2 text-sm text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <span><?= date(
                                                "d M Y",
                                                strtotime(
                                                    $appointment[
                                                        "TANGGAL_JANJI"
                                                    ]
                                                )
                                            ) ?></span>
                                            <i class="fas fa-clock ml-3 mr-1"></i>
                                            <span><?= date(
                                                "H:i",
                                                strtotime(
                                                    $appointment["WAKTU_JANJI"]
                                                )
                                            ) ?> WIB</span>
                                        </div>
                                    </div>
                                    <a href="<?= base_url(
                                        "doctor/appointment/" .
                                            $appointment["ID_JANJI_TEMU"]
                                    ) ?>" class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        Konfirmasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($pending_appointments) > 5): ?>
                    <div class="mt-4 text-center">
                        <a href="<?= base_url(
                            "doctor/appointments/pending"
                        ) ?>" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Lihat Semua
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Awaiting Diagnosis -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-indigo-600 px-4 py-4">
            <h3 class="text-lg leading-6 font-medium text-white">
                <i class="fas fa-stethoscope mr-2"></i> Menunggu Diagnosis
            </h3>
        </div>
        <div class="p-4">
            <?php if (empty($diagnosis_queue)): ?>
                <div class="text-center py-8 text-gray-500">
                    <p>Tidak ada pasien yang menunggu diagnosis.</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($diagnosis_queue as $appointment): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-md transition duration-150">
                            <div class="p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900"><?= esc(
                                            $appointment["patient_name"]
                                        ) ?></h4>
                                        <p class="text-sm text-gray-600"><?= esc(
                                            $appointment["NAMA_JANJI"]
                                        ) ?></p>
                                        <div class="flex items-center mt-2 text-sm text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <span><?= date(
                                                "d M Y",
                                                strtotime(
                                                    $appointment[
                                                        "TANGGAL_JANJI"
                                                    ]
                                                )
                                            ) ?></span>
                                            <i class="fas fa-clock ml-3 mr-1"></i>
                                            <span><?= date(
                                                "H:i",
                                                strtotime(
                                                    $appointment["WAKTU_JANJI"]
                                                )
                                            ) ?> WIB</span>
                                        </div>
                                    </div>
                                    <a href="<?= base_url(
                                        "doctor/diagnosis/" .
                                            $appointment["ID_JANJI_TEMU"]
                                    ) ?>" class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        Diagnosa
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Patients with Lab Results Section -->
<div class="mt-8" id="lab-results-section">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-green-600 px-4 py-4">
            <h3 class="text-lg leading-6 font-medium text-white">
                <i class="fas fa-flask mr-2"></i> Hasil Lab Terbaru
            </h3>
        </div>
        <div class="p-4">
            <?php if (empty($patients_with_lab_results)): ?>
                <div class="text-center py-8 text-gray-500">
                    <p>Tidak ada hasil lab baru.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Janji Temu</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paket</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach (
                                $patients_with_lab_results
                                as $appointment
                            ): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= esc(
                                            $appointment["patient_name"]
                                        ) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc($appointment["NAMA_JANJI"]) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date(
                                            "d M Y",
                                            strtotime(
                                                $appointment["TANGGAL_JANJI"]
                                            )
                                        ) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= esc(
                                            $appointment["nama_paket"] ??
                                                "Tanpa paket"
                                        ) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="<?= base_url(
                                            "doctor/lab-results/" .
                                                $appointment["ID_JANJI_TEMU"]
                                        ) ?>" class="text-green-600 hover:text-green-900 mr-3">Lihat Hasil</a>
                                        <a href="<?= base_url(
                                            "doctor/complete-lab-results/" .
                                                $appointment["ID_JANJI_TEMU"]
                                        ) ?>" class="text-blue-600 hover:text-blue-900">Selesaikan</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
