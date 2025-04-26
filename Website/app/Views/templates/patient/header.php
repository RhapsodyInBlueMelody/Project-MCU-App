<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Check Up</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Basic styling for the flag container */
        .flag-container {
            width: 24px;
            height: 16px;
            background-size: cover;
            margin-right: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div id="welcome-banner-mobile" class="flex items-center justify-between p-2 bg-info-500 text-white lg:hidden">
        <div id="welcome-text-mobile" class="text-center flex-grow">
            Selamat Datang
        </div>
        <div id="language-switcher-mobile" class="flex items-center">
            <div class="flag-container" id="flag-display-mobile" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
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

    <nav class="bg-white shadow py-2 px-4 lg:hidden">
        <div class="container mx-auto flex items-center justify-between">
            <a href="#" class="flex items-center">
                <div class="border border-gray-300 w-32 h-10 flex justify-center items-center text-gray-700 text-xs">
                    Image Placeholder
                </div>
            </a>
            <button id="toggleMobileMenu" class="focus:outline-none">
                <i class="fas fa-bars text-xl text-gray-600"></i>
            </button>
        </div>
        <div id="mobileMenu" class="hidden bg-gray-100 shadow-md mt-2 rounded-md overflow-hidden">
            <ul class="space-y-2 p-4">
                <li>
                    <a href="<?php echo base_url('/patient/dashboard'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        Beranda
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('/patient/pendaftaran'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        Pendaftaran
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('/patient/jadwal-pemeriksaan'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        Jadwal Pemeriksaan
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('/patient/riwayat-medical-checkup'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                        Riwayat Medical Check Up
                    </a>
                </li>
                <li class="mt-4 border-t border-gray-200 pt-2">
                    <div id="language-switcher-nav-mobile" class="flex items-center space-x-2">
                        <div class="flag-container" id="flag-display-nav-mobile" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
                        <div class="inline-flex items-center">
                            <input type="radio" name="language-nav-mobile" id="en-nav-mobile" value="en" class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label class="ml-1 text-sm text-gray-700" for="en-nav-mobile">EN</label>
                        </div>
                        <div class="inline-flex items-center">
                            <input type="radio" name="language-nav-mobile" id="id-nav-mobile" value="id" checked class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <label class="ml-1 text-sm text-gray-700" for="id-nav-mobile">ID</label>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    <nav class="bg-white shadow py-4 hidden lg:block">
        <div class="container mx-auto flex items-center justify-between">
            <button id="toggleSidebarBtn" class="focus:outline-none">
                <i class="fas fa-bars text-xl text-gray-600"></i>
            </button>
            <a href="#" class="text-xl font-semibold text-gray-800">Medical Check Up</a>
            <ul class="hidden lg:flex items-center space-x-4">
                <li><button class="focus:outline-none"><i class="fas fa-bell text-lg text-gray-600"></i></button></li>
                <li><span class="text-gray-700">Welcome, <?php // echo $this->session->userdata('username'); ?></span></li>
            </ul>
        </div>
    </nav>

    <div class="container mx-auto mt-8 flex">
        <div id="sidebar" class="bg-gray-200 w-64 p-4 space-y-4 hidden lg:block">
            <div class="text-center p-2">
                <i class="fas fa-plus-square fa-2x text-blue-500"></i>
                <h5 class="mt-2 text-lg font-semibold text-gray-700">MEDICAL CHECK UP</h5>
            </div>
            <hr class="border-t border-gray-300">
            <a href="<?php echo base_url('/patient/dashboard'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded">
                <i class="fas fa-home mr-2"></i> Beranda
            </a>
            <a href="<?php echo base_url('/patient/pendaftaran'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded">
                <i class="fas fa-user-plus mr-2"></i> Pendaftaran
            </a>
            <a href="<?php echo base_url('/patient/jadwal-pemeriksaan'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded">
                <i class="fas fa-stethoscope mr-2"></i> Jadwal Pemeriksaan
            </a>
            <a href="<?php echo base_url('/patient/riwayat-medical-checkup'); ?>" class="block py-2 px-4 text-gray-700 hover:bg-gray-300 hover:text-blue-500 rounded">
                <i class="fas fa-history mr-2"></i> Riwayat Medical Check Up
            </a>
        </div>

        <div id="content" class="flex-1 ml-0 lg:ml-8 p-4">