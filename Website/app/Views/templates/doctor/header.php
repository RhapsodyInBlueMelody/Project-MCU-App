<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? esc($title) . " - " : "" ?>Medical Check Up | Dokter</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <div x-data="{ sidebarOpen: true }">
        <!-- Top navbar -->
        <nav class="fixed top-0 z-30 w-full bg-blue-600 shadow-md">
            <div class="px-4 mx-auto sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <a href="<?= base_url("dokter/dashboard") ?>" class="text-xl font-bold text-white ml-10">
                            <i class="fas fa-hospital-user mr-2"></i>Medical Check Up
                        </a>
                        <div class="hidden lg:block ml-10">
                            <a href="<?= base_url("dokter/dashboard") ?>"
                                class="px-3 py-2 rounded-md text-sm font-medium <?= uri_string() == 'dokter/dashboard' ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500' ?>">
                                Dashboard</a>
                            <a href="<?= base_url("dokter/appointments") ?>"
                                class="px-3 py-2 rounded-md text-sm font-medium <?= strpos(uri_string(), 'dokter/appointments') === 0 ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500' ?>">
                                Janji Temu</a>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <div class="flex items-center ml-4">
                            <div class="relative ml-3" x-data="{ profileOpen: false }">
                                <button @click="profileOpen = !profileOpen"
                                    class="flex items-center max-w-xs text-sm text-white rounded-full focus:outline-none">
                                    <i class="fas fa-user-md mr-1"></i>
                                    <span class="ml-1"><?= isset($dokter_name) ? esc($doctor_name) : "Dokter" ?></span>
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </button>
                                <div x-show="profileOpen" @click.away="profileOpen = false"
                                    x-transition
                                    class="absolute right-0 z-50 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <a href="<?= base_url("dokter/profile") ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-id-card mr-2"></i>Profil</a>
                                    <a href="<?= base_url("dokter/schedule") ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-calendar mr-2"></i>Jadwal</a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="<?= base_url("auth/logout/dokter") ?>"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="-mr-2 flex lg:hidden">
                        <!-- Mobile menu button -->
                        <button @click="sidebarOpen = !sidebarOpen" type="button"
                            class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-blue-500 focus:outline-none">
                            <span class="sr-only">Open main menu</span>
                            <i x-show="!sidebarOpen" class="fas fa-bars block h-6 w-6"></i>
                            <i x-show="sidebarOpen" class="fas fa-times block h-6 w-6"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile menu -->
            <div x-show="sidebarOpen" class="lg:hidden" id="mobile-menu">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="<?= base_url("dokter/dashboard") ?>"
                        class="block px-3 py-2 rounded-md text-base font-medium <?= uri_string() == 'dokter/dashboard' ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500' ?>">Dashboard</a>
                    <a href="<?= base_url("dokter/appointments") ?>"
                        class="block px-3 py-2 rounded-md text-base font-medium <?= strpos(uri_string(), 'dokter/appointments') === 0 ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500' ?>">Janji Temu</a>
                    <a href="<?= base_url("dokter/profile") ?>"
                        class="block px-3 py-2 rounded-md text-base font-medium <?= strpos(uri_string(), 'dokter/profile') === 0 ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500' ?>">Profil Saya</a>
                    <a href="<?= base_url("dokter/schedule") ?>"
                        class="block px-3 py-2 rounded-md text-base font-medium <?= strpos(uri_string(), 'dokter/schedule') === 0 ? 'bg-blue-700 text-white' : 'text-white hover:bg-blue-500' ?>">Jadwal Saya</a>
                    <a href="<?= base_url("auth/logout/dokter") ?>"
                        class="text-white hover:bg-red-500 block px-3 py-2 rounded-md text-base font-medium">Logout</a>
                </div>
            </div>
        </nav>
        <!-- Main Content -->
        <div class="lg:pl-0">
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
