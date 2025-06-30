<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title)
        ? esc($title) . " - "
        : "" ?>Admin Medical Check Up</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js for dashboards -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div x-data="{ sidebarOpen: true }">
        <!-- Top navbar -->
        <nav class="fixed top-0 z-50 w-full bg-indigo-700 border-b border-indigo-800">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" type="button" class="inline-flex items-center p-2 text-sm text-white rounded-lg hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                            <span class="sr-only">Toggle sidebar</span>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
                            </svg>
                        </button>
                        <a href="<?= base_url(
                            "admin/dashboard"
                        ) ?>" class="flex ml-2 md:mr-24">
                            <span class="self-center text-xl font-semibold sm:text-2xl whitespace-nowrap text-white">Medical Check Up</span>
                        </a>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center ml-3">
                            <div x-data="{ dropdownOpen: false }" class="relative">
                                <button @click="dropdownOpen = !dropdownOpen" type="button" class="flex text-sm bg-gray-100 rounded-full focus:ring-4 focus:ring-indigo-300" id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <i class="fas fa-user-shield p-2 rounded-full text-indigo-700"></i>
                                </button>
                                <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 z-50 mt-2 w-48 bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-user">
                                    <div class="px-4 py-3 text-sm text-gray-900">
                                        <div class="font-medium"><?= session()->get(
                                            "name"
                                        ) ?? "Admin" ?></div>
                                        <div class="truncate"><?= session()->get(
                                            "email"
                                        ) ?? "admin@example.com" ?></div>
                                    </div>
                                    <ul class="py-1 text-sm text-gray-700">
                                        <li>
                                            <a href="<?= base_url(
                                                "admin/dashboard"
                                            ) ?>" class="block px-4 py-2 hover:bg-gray-100">Dashboard</a>
                                        </li>
                                        <li>
                                            <a href="<?= base_url(
                                                "admin/settings"
                                            ) ?>" class="block px-4 py-2 hover:bg-gray-100">Settings</a>
                                        </li>
                                        <li>
                                            <a href="<?= base_url(
                                                "auth/logout/admin"
                                            ) ?>" class="block px-4 py-2 hover:bg-gray-100">Sign out</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform bg-indigo-800 border-r border-indigo-900 md:translate-x-0">
            <div class="h-full px-3 pb-4 overflow-y-auto bg-indigo-800">
                <ul class="space-y-2 font-medium">
                    <li>
                        <a href="<?= base_url(
                            "admin/dashboard"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= uri_string() ==
"admin/dashboard"
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-tachometer-alt w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(
                            "admin/pending-doctor-verifications"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= strpos(
    uri_string(),
    "admin/pending-doctor-verifications"
) === 0
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-user-md w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Verifikasi Dokter</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(
                            "admin/doctor-management"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= strpos(
    uri_string(),
    "admin/doctor-management"
) === 0
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-user-md w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Manajemen Dokter</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(
                            "admin/patient-management"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= strpos(
    uri_string(),
    "admin/patient-management"
) === 0
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-users w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Manajemen Pasien</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(
                            "admin/appointment-management"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= strpos(
    uri_string(),
    "admin/appointment-management"
) === 0
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-calendar-check w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Manajemen Janji</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(
                            "admin/reports"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= strpos(
    uri_string(),
    "admin/reports"
) === 0
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-chart-bar w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Laporan</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url(
                            "admin/settings"
                        ) ?>" class="flex items-center p-3 text-white rounded-lg hover:bg-indigo-700 group <?= strpos(
    uri_string(),
    "admin/settings"
) === 0
    ? "bg-indigo-700"
    : "" ?>">
                            <i class="fas fa-cog w-5 h-5 text-white transition duration-75"></i>
                            <span class="ml-3">Pengaturan</span>
                        </a>
                    </li>
                </ul>
                <div class="pt-5 mt-5 space-y-2 border-t border-indigo-700">
                    <i class="fas fa-sign-out-alt w-5 h-5 text-white transition duration-75"></i>
                    <span class="ml-3">Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main content -->
    <div class="p-4 sm:ml-64 pt-20">
        <div class="p-4">
            <?php if (session()->has("success")): ?>
                <div id="alert-success" class="flex p-4 mb-4 bg-green-100 border-t-4 border-green-500 dark:bg-green-200" role="alert">
                    <i class="fas fa-check-circle flex-shrink-0 w-5 h-5 text-green-700"></i>
                    <div class="ml-3 text-sm font-medium text-green-700">
                        <?= session()->getFlashdata("success") ?>
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-green-100 dark:bg-green-200 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 dark:hover:bg-green-300 inline-flex h-8 w-8" data-dismiss-target="#alert-success" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (session()->has("error")): ?>
                <div id="alert-error" class="flex p-4 mb-4 bg-red-100 border-t-4 border-red-500 dark:bg-red-200" role="alert">
                    <i class="fas fa-exclamation-circle flex-shrink-0 w-5 h-5 text-red-700"></i>
                    <div class="ml-3 text-sm font-medium text-red-700">
                        <?= session()->getFlashdata("error") ?>
                    </div>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-red-100 dark:bg-red-200 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 dark:hover:bg-red-300 inline-flex h-8 w-8" data-dismiss-target="#alert-error" aria-label="Close">
                        <span class="sr-only">Close</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Page content begins -->
