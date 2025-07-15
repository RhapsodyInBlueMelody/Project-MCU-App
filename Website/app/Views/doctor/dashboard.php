<div class="flex flex-col md:flex-row justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard Dokter</h1>
    <div class="flex mt-4 md:mt-0">
        <a href="<?= base_url("dokter/appointments?status=today") ?>" class="inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
            <i class="fas fa-calendar-day mr-2"></i> Janji Temu Hari Ini
        </a>
        <a href="<?= base_url("dokter/schedule") ?>" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-calendar-alt mr-2"></i> Jadwal Saya
        </a>
    </div>
</div>
<?php if (isset($doctor_info)): ?>
    <div class="mb-6">
        <div class="bg-white overflow-hidden shadow-md rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col md:flex-row items-center">
                    <div class="flex-shrink-0 flex items-center justify-center h-24 w-24 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-user-md text-5xl"></i>
                    </div>
                    <div class="mt-4 md:mt-0 md:ml-6 text-center md:text-left">
                        <h3 class="text-xl font-semibold text-gray-900"><?= esc($doctor_info["nama_dokter"]) ?></h3>
                        <p class="text-sm text-gray-500"><?= esc($doctor_info["nama_spesialisasi"] ?? '') ?></p>
                        <div class="mt-2 text-sm text-gray-700">
                            <div class="flex items-center justify-center md:justify-start mb-1">
                                <i class="fas fa-id-card mr-2 text-gray-500"></i>
                                <span><?= esc($doctor_info["no_lisensi"] ?? '-') ?></span>
                            </div>
                            <div class="flex items-center justify-center md:justify-start">
                                <i class="fas fa-phone mr-2 text-gray-500"></i>
                                <span><?= esc($doctor_info["telepon_dokter"] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-4">
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
                            <div class="text-2xl font-semibold text-gray-900"><?= count($today_appointments ?? []) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url("/dokter/appointments?status=today") ?>" class="font-medium text-blue-600 hover:text-blue-500">Lihat semua</a>
            </div>
        </div>
    </div>
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
                            <div class="text-2xl font-semibold text-gray-900"><?= count($pending_appointments ?? []) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url("dokter/appointments?status=pending") ?>" class="font-medium text-yellow-600 hover:text-yellow-500">Lihat semua</a>
            </div>
        </div>
    </div>
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
                            <div class="text-2xl font-semibold text-gray-900"><?= count($diagnosis_queue ?? []) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url("dokter/appointments?status=confirmed") ?>" class="font-medium text-indigo-600 hover:text-indigo-500">Lihat semua</a>
            </div>
        </div>
    </div>
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
                            <div class="text-2xl font-semibold text-gray-900"><?= count($patients_with_lab_results ?? []) ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3">
            <div class="text-sm">
                <a href="<?= base_url("dokter/appointments?status=awaiting_lab_results") ?>" class="font-medium text-green-600 hover:text-green-500">Lihat semua</a>
            </div>
        </div>
    </div>
</div>
