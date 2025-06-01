<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title)
        ? esc($title) . " - "
        : "" ?>Medical Check Up | Dokter</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Custom styles -->
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div x-data="{ sidebarOpen: true }">
        <!-- Mobile menu button -->
        <div class="fixed top-0 left-0 z-40 flex items-center p-4 lg:hidden">
            <button @click="sidebarOpen = !sidebarOpen" class="p-1 text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                <span class="sr-only">Open sidebar</span>
                <i x-show="!sidebarOpen" class="fas fa-bars text-lg"></i>
                <i x-show="sidebarOpen" class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Top navbar -->
        <nav class="fixed top-0 z-30 w-full bg-primary-600 shadow-md">
            <div class="px-4 mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 ml-10 lg:ml-0">
                            <a href="<?= base_url(
                                "doctor/dashboard"
                            ) ?>" class="text-xl font-bold text-white">
                                <i class="fas fa-hospital-user mr-2"></i>Medical Check Up
                            </a>
                        </div>
                        <div class="hidden lg:block">
                            <div class="flex items-center ml-10 space-x-4">
                                <a href="<?= base_url(
                                    "doctor/dashboard"
                                ) ?>" class="<?= uri_string() ==
"doctor/dashboard"
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                                <a href="<?= base_url(
                                    "doctor/appointments"
                                ) ?>" class="<?= strpos(
    uri_string(),
    "doctor/appointments"
) === 0
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> px-3 py-2 rounded-md text-sm font-medium">Janji Temu</a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <div class="flex items-center ml-4">
                            <div class="relative ml-3" x-data="{ profileOpen: false }">
                                <div>
                                    <button @click="profileOpen = !profileOpen" class="flex items-center max-w-xs text-sm text-white rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-600 focus:ring-white" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                        <span class="sr-only">Open user menu</span>
                                        <i class="fas fa-user-md mr-1"></i>
                                        <span class="ml-1"><?= isset(
                                            $doctor_name
                                        )
                                            ? esc($doctor_name)
                                            : "Dokter" ?></span>
                                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                    </button>
                                </div>
                                <div x-show="profileOpen" @click.away="profileOpen = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 z-50 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <a href="<?= base_url(
                                        "doctor/profile"
                                    ) ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"><i class="fas fa-id-card mr-2"></i>Profil</a>
                                    <a href="<?= base_url(
                                        "doctor/schedule"
                                    ) ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"><i class="fas fa-calendar mr-2"></i>Jadwal</a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="<?= base_url(
                                        "auth/logout/doctor"
                                    ) ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="-mr-2 flex lg:hidden">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-primary-600 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                            <span class="sr-only">Open main menu</span>
                            <i x-bind:class="sidebarOpen ? 'hidden' : 'block'" class="fas fa-bars block h-6 w-6"></i>
                            <i x-bind:class="sidebarOpen ? 'block' : 'hidden'" class="fas fa-times block h-6 w-6"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div x-show="sidebarOpen" class="lg:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="<?= base_url(
                        "doctor/dashboard"
                    ) ?>" class="<?= uri_string() == "doctor/dashboard"
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                    <a href="<?= base_url(
                        "doctor/appointments"
                    ) ?>" class="<?= strpos(
    uri_string(),
    "doctor/appointments"
) === 0
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> block px-3 py-2 rounded-md text-base font-medium">Janji Temu</a>
                    <a href="<?= base_url(
                        "doctor/appointments/today"
                    ) ?>" class="<?= strpos(
    uri_string(),
    "doctor/appointments/today"
) === 0
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> block px-3 py-2 rounded-md text-base font-medium">Janji Temu Hari Ini</a>
                    <a href="<?= base_url(
                        "doctor/schedule"
                    ) ?>" class="<?= strpos(uri_string(), "doctor/schedule") ===
0
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> block px-3 py-2 rounded-md text-base font-medium">Jadwal Saya</a>
                    <a href="<?= base_url(
                        "doctor/profile"
                    ) ?>" class="<?= strpos(uri_string(), "doctor/profile") ===
0
    ? "bg-primary-700 text-white"
    : "text-white hover:bg-primary-500" ?> block px-3 py-2 rounded-md text-base font-medium">Profil Saya</a>
                    <a href="<?= base_url(
                        "auth/logout/doctor"
                    ) ?>" class="text-white hover:bg-red-500 block px-3 py-2 rounded-md text-base font-medium">Logout</a>
                </div>
            </div>
        </nav>

        <!-- Sidebar Navigation -->
        <aside x-bind:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-20 w-64 px-2 py-4 overflow-y-auto transition duration-200 transform bg-gray-800 lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center mt-16 lg:mt-0">
                <div class="flex items-center">
                    <i class="fas fa-user-md text-4xl text-primary-400"></i>
                    <span class="ml-3 text-xl font-bold text-white">Panel Dokter</span>
                </div>
            </div>
            <nav class="mt-10">
                <a href="<?= base_url(
                    "doctor/dashboard"
                ) ?>" class="<?= uri_string() == "doctor/dashboard"
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-tachometer-alt mr-3 text-lg"></i>
                    Dashboard
                </a>
                <a href="<?= base_url(
                    "doctor/appointments"
                ) ?>" class="<?= strpos(uri_string(), "doctor/appointments") ===
    0 && uri_string() == "doctor/appointments"
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 mt-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-calendar-check mr-3 text-lg"></i>
                    Semua Janji Temu
                </a>
                <a href="<?= base_url(
                    "doctor/appointments/today"
                ) ?>" class="<?= strpos(
    uri_string(),
    "doctor/appointments/today"
) === 0
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 mt-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-calendar-day mr-3 text-lg"></i>
                    Janji Temu Hari Ini
                </a>
                <a href="<?= base_url(
                    "doctor/appointments/pending"
                ) ?>" class="<?= strpos(
    uri_string(),
    "doctor/appointments/pending"
) === 0
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 mt-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-clock mr-3 text-lg"></i>
                    Menunggu Konfirmasi
                </a>
                <a href="<?= base_url("doctor/schedule") ?>" class="<?= strpos(
    uri_string(),
    "doctor/schedule"
) === 0
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 mt-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-calendar-alt mr-3 text-lg"></i>
                    Jadwal Saya
                </a>
                <a href="<?= base_url("doctor/profile") ?>" class="<?= strpos(
    uri_string(),
    "doctor/profile"
) === 0
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 mt-2 rounded-lg text-sm font-medium">
                    <i class="fas fa-user-md mr-3 text-lg"></i>
                    Profil Saya
                </a>

                <div class="mt-10">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Laporan
                    </p>
                    <a href="<?= base_url(
                        "doctor/reports/monthly"
                    ) ?>" class="<?= strpos(
    uri_string(),
    "doctor/reports/monthly"
) === 0
    ? "bg-gray-900 text-white"
    : "text-gray-300 hover:bg-gray-700 hover:text-white" ?> flex items-center px-4 py-2 mt-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-chart-line mr-3 text-lg"></i>
                        Laporan Bulanan
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <main class="px-4 py-6 mx-auto mt-16 sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <?php if (session()->has("success")): ?>
                    <div class="relative px-4 py-3 mb-4 text-green-700 bg-green-100 border border-green-400 rounded alert-dismissible" role="alert">
                        <?= session()->getFlashdata("success") ?>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has("error")): ?>
                    <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded alert-dismissible" role="alert">
                        <?= session()->getFlashdata("error") ?>
                        <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
