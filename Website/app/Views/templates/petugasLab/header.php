<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Lab Dashboard') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-800 text-white shadow-md">
        <div class="container mx-auto flex flex-col md:flex-row md:justify-between md:items-center py-3 px-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 3v18m9-9H3" />
                    </svg>
                    <span class="font-bold text-xl">Lab Dashboard</span>
                </div>
                <button @click="open = !open" x-data="{ open: false }" class="md:hidden focus:outline-none">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav x-data="{ open: false }" :class="{'block': open, 'hidden': !open}" class="md:flex md:items-center md:space-x-5 space-y-2 md:space-y-0 mt-2 md:mt-0 hidden">
                <a href="<?= site_url('petugas_lab/dashboard') ?>" class="block py-2 px-4 rounded hover:bg-blue-700 md:inline md:py-0 md:px-3">Dashboard</a>
                <a href="<?= site_url('petugas_lab/orders') ?>" class="block py-2 px-4 rounded hover:bg-blue-700 md:inline md:py-0 md:px-3">Order Tes</a>
                <a href="<?= site_url('petugas_lab/profile') ?>" class="block py-2 px-4 rounded hover:bg-blue-700 md:inline md:py-0 md:px-3">Profil</a>
                <a href="<?= site_url('auth/logout/petugas_lab') ?>" class="block py-2 px-4 bg-red-500 hover:bg-red-600 text-white rounded md:inline md:py-0 md:px-3 md:bg-transparent md:text-red-500 md:hover:text-white md:hover:bg-red-600">Logout</a>
            </nav>
        </div>
    </header>
