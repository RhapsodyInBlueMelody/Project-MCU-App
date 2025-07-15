<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Medical Check Up Portal for pasiens">
    <meta name="robots" content="noindex, nofollow">
    <!-- Security Headers -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">

    <title><?= isset($title)
                ? esc($title) . " - "
                : "" ?>Medical Check Up</title>

    <!-- Preload critical assets -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= base_url("assets/css/pasien.min.css") ?>">

    <!-- Defer non-critical JavaScript -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <?php if (ENVIRONMENT === "production"): ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php else: ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php endif; ?>

    <!-- Inline critical CSS -->
    <style>
        /* Critical path CSS */
        .flag-container {
            width: 24px;
            height: 16px;
            background-size: cover;
            margin-right: 0.5rem;
        }

        .bg-info-500 {
            background-color: #3498db;
        }

        /* Add skeleton loader styles for perceived performance */
        .skeleton {
            animation: pulse 1.5s infinite;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
        }

        @keyframes pulse {
            0% {
                background-position: 0% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }
    </style>

    <!-- CSRF Token for AJAX requests -->
    <script>
        const csrfToken = '<?= csrf_hash() ?>';
        const baseUrl = '<?= base_url() ?>';

        // Add mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const toggleMobileMenu = document.getElementById('toggleMobileMenu');
            const mobileMenu = document.getElementById('mobileMenu');

            if (toggleMobileMenu && mobileMenu) {
                toggleMobileMenu.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    const isExpanded = mobileMenu.classList.contains('hidden') === false;
                    toggleMobileMenu.setAttribute('aria-expanded', isExpanded);
                    mobileMenu.setAttribute('aria-hidden', !isExpanded);
                });
            }
        });
    </script>
</head>

<body class="bg-gray-100 font-sans antialiased min-h-screen flex flex-col">
    <!-- Accessibility skip link -->
    <a href="#content" class="sr-only focus:not-sr-only focus:absolute focus:p-4 focus:bg-white focus:text-blue-500 focus:z-50">Skip to content</a>

    <!-- Welcome Banner (Mobile) -->
    <div id="welcome-banner-mobile" class="flex items-center justify-between p-2 bg-info-500 text-white lg:hidden">
        <div id="welcome-text-mobile" class="text-center flex-grow">
            Selamat Datang <?= esc(session()->get("username") ?? "Tamu") ?>
        </div>
        <div id="language-switcher-mobile" class="flex items-center">
            <div class="flag-container" id="flag-display-mobile" style="background-image: url('<?= base_url(
                                                                                                    "assets/images/flag-indonesia.png"
                                                                                                ) ?>');"></div>
            <div class="inline-flex items-center mr-2">
                <input type="radio" name="language-mobile" id="en-mobile" value="en" class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label class="ml-1 text-sm text-gray-700" for="en-mobile">EN</label>
            </div>
            <div class="inline-flex items-center">
                <input type="radio" name="language-mobile" id="id-mobile" value="id" checked class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label class="ml-1 text-sm text-gray-700" for="id-mobile">ID</label>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation -->
    <nav class="bg-white shadow py-2 px-4 lg:hidden">
        <div class="container mx-auto flex items-center justify-between">
            <a href="<?= base_url() ?>" class="flex items-center">
                <img src="<?= base_url(
                                "assets/images/logo.jpg"
                            ) ?>" alt="Medical Check Up Logo" class="w-32 h-10" loading="eager">
            </a>
            <button id="toggleMobileMenu" class="focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-1 rounded" aria-expanded="false" aria-controls="mobileMenu">
                <span class="sr-only">Open main menu</span>
                <i class="fas fa-bars text-xl text-gray-600"></i>
            </button>
        </div>
        <div id="mobileMenu" class="hidden bg-gray-100 shadow-md mt-2 rounded-md overflow-hidden" aria-hidden="true">
            <ul class="space-y-2 p-4">
                <li>
                    <a href="<?= base_url(
                                    "pasien/dashboard"
                                ) ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        <i class="fas fa-home mr-2" aria-hidden="true"></i> Beranda
                    </a>
                </li>
                <li>
                    <a href="<?= base_url(
                                    "pasien/appointment"
                                ) ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        <i class="fas fa-user-plus mr-2" aria-hidden="true"></i> Buat Janji
                    </a>
                </li>
                <li>
                    <a href="<?= base_url(
                                    "pasien/jadwal-pemeriksaan"
                                ) ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        <i class="fas fa-stethoscope mr-2" aria-hidden="true"></i> Jadwal Pemeriksaan
                    </a>
                </li>
                <li class="mt-4 border-t border-gray-200 pt-2">
                    <a href="<?= base_url(
                                    "auth/logout/pasien"
                                ) ?>" class="block py-2 px-4 text-red-600 hover:bg-gray-200 hover:text-red-700 rounded">
                        <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Desktop Navigation -->
    <nav class="bg-white shadow py-4 hidden lg:block">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center">
                <button id="toggleSidebarBtn" class="focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-1 rounded mr-4" aria-expanded="true" aria-controls="sidebar">
                    <span class="sr-only">Toggle sidebar</span>
                    <i class="fas fa-bars text-xl text-gray-600"></i>
                </button>
                <a href="<?= base_url() ?>" class="text-xl font-semibold text-gray-800">Medical Check Up</a>
            </div>
            <ul class="hidden lg:flex items-center space-x-4">
                <li>
                    <button class="focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-1 rounded relative" aria-label="Notifications">
                        <i class="fas fa-bell text-lg text-gray-600"></i>
                        <span class="inline-flex absolute -top-1 -right-1 justify-center items-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">2</span>
                    </button>
                </li>
                <li>
                    <span class="text-gray-700">
                        Welcome, <?= esc(
                                        session()->get("username") ?? "Guest"
                                    ) ?>
                    </span>
                </li>
                <li>
                    <a href="<?= base_url(
                                    "auth/logout/pasien"
                                ) ?>" class="text-red-600 hover:text-red-700 flex items-center">
                        <i class="fas fa-sign-out-alt mr-1" aria-hidden="true"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="container mx-auto mt-8 flex flex-col lg:flex-row flex-grow">
        <!-- Sidebar (Desktop) -->
        <div id="sidebar" class="bg-gray-200 w-64 p-4 space-y-4 hidden lg:block transition-transform duration-300 ease-in-out" aria-label="Main navigation">
            <div class="text-center p-2">
                <i class="fas fa-plus-square fa-2x text-blue-500" aria-hidden="true"></i>
                <h5 class="mt-2 text-lg font-semibold text-gray-700">MEDICAL CHECK UP</h5>
            </div>
            <hr class="border-t border-gray-300">
            <a href="<?= base_url(
                            "pasien/dashboard"
                        ) ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded transition duration-150 ease-in-out">
                <i class="fas fa-home mr-2" aria-hidden="true"></i> Beranda
            </a>
            <a href="<?= base_url(
                            "pasien/appointment"
                        ) ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded transition duration-150 ease-in-out">
                <i class="fas fa-user-plus mr-2" aria-hidden="true"></i> Buat Janji
            </a>
            <a href="<?= base_url(
                            "pasien/jadwal-pemeriksaan"
                        ) ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded transition duration-150 ease-in-out">
                <i class="fas fa-stethoscope mr-2" aria-hidden="true"></i> Jadwal Pemeriksaan
            </a>
            <hr class="border-t border-gray-300">
        </div>

        <!-- Main Content -->
        <main id="content" class="flex-1 ml-0 lg:ml-8 p-4" tabindex="-1">
