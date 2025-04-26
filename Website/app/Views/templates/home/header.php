<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Rumah Sakit</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'merriweather': ['Merriweather', 'serif'],
                    },
                    zIndex: {
                        '100': '100',
                    }
                },
            },
        }
    </script>
    <style>
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0s linear 0.2s;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0s;
        }
    </style>
</head>
<body class="font-merriweather">
<div id="welcome-banner" class="flex items-center justify-between p-2 bg-info-500 text-white">
    <div id="realtime-clock" class="hidden md:block text-sm">
        </div>
    <div id="welcome-text" class="text-center flex-grow">
        Selamat Datang
    </div>
    <div class="md:hidden flex items-center ml-auto">
        <div id="flag-display-mobile" class="w-6 h-4 mr-2 border border-gray-300 bg-cover bg-center" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
        <div class="inline-flex items-center mr-2">
            <input type="radio" name="language-mobile" id="en-mobile" value="en" class="form-radio">
            <label for="en-mobile" class="ml-1 text-sm">EN</label>
        </div>
        <div class="inline-flex items-center">
            <input type="radio" name="language-mobile" id="id-mobile" value="id" checked class="form-radio">
            <label for="id-mobile" class="ml-1 text-sm">ID</label>
        </div>
    </div>
    <div class="hidden md:flex items-center">
        <div id="flag-display" class="w-6 h-4 mr-2 border border-gray-300 bg-cover bg-center" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
        <div class="inline-flex items-center mr-2">
            <input type="radio" name="language" id="en" value="en" class="form-radio">
            <label for="en" class="ml-1 text-sm">EN</label>
        </div>
        <div class="inline-flex items-center">
            <input type="radio" name="language" id="id" value="id" checked class="form-radio">
            <label for="id" class="ml-1 text-sm">ID</label>
        </div>
    </div>
</div>
<nav class="bg-gray-100 py-2 px-5 lg:hidden">
    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="mr-8">
            <div class="border border-gray-300 w-32 h-10 flex justify-center items-center text-gray-600 text-xs">
                Image Placeholder
            </div>
        </a>
        <button id="toggleMobileMenu" class="focus:outline-none">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
            </svg>
        </button>
    </div>
    <div id="mobileMenu" class="hidden bg-gray-100 shadow-md mt-2 rounded-md overflow-hidden">
        <ul class="space-y-2 p-4">
            <li>
                <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                    Beranda
                </a>
            </li>
            <li>
                <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                    Rumah Sakit Kami
                </a>
            </li>
            <li>
                <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                    Fasilitas Kami
                </a>
            </li>
            <li>
                <a href="#" class="block py-2 px-4 text-gray-700 hover:bg-gray-200 hover:text-blue-500 rounded">
                    Cari Dokter
                </a>
            </li>
            <li class="mt-4 border-t border-gray-200 pt-2">
                <div class="flex items-center space-x-2">
                    <div id="flag-display-nav-mobile" class="w-6 h-4 mr-2 border border-gray-300 bg-cover bg-center" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
                    <div class="inline-flex items-center mr-2">
                        <input type="radio" name="language-nav-mobile" id="en-nav-mobile" value="en" class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label class="ml-1 text-sm text-gray-700" for="en-nav-mobile">EN</label>
                    </div>
                    <div class="inline-flex items-center">
                        <input type="radio" name="language-nav-mobile" id="id-nav-mobile" value="id" checked class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <label class="ml-1 text-sm text-gray-700" for="id-nav-mobile">ID</label>
                    </div>
                </div>
            </li>
            <li class="mt-4">
                <div class="flex flex-col space-y-2 items-stretch">
                    <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded-full text-sm" type="button">Buat Janji Temu</button>
                    <button class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded-full text-sm" type="button">Daftar/Masuk</button>
                </div>
            </li>
        </ul>
    </div>
</nav>

<nav class="bg-gray-100 py-2 px-5 hidden lg:flex relative">
    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="mr-8">
            <div class="border border-gray-300 w-32 h-10 flex justify-center items-center text-gray-600 text-xs">
                Image Placeholder
            </div>
        </a>
        <ul class="flex space-x-4 mr-auto">
            <li>
                <a href="#" class="nav-link active text-blue-500">Beranda</a>
            </li>
            <li class="relative" x-data="{ open: false }">
                <a href="#"
                   class="nav-link hover:text-blue-500"
                   @mouseover="open = true"
                   @mouseleave="open = false">
                    Rumah Sakit Kami
                </a>
                <div class="absolute top-full left-0 z-50"
                     @mouseover="open = true"
                     @mouseleave="open = false">
                    <ul class="bg-white shadow-md rounded-md mt-1 py-2 min-w-[10rem] dropdown-menu"
                        :class="{'show': open}">
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 1</a></li>
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 2</a></li>
                    </ul>
                </div>
            </li>
            <li class="relative" x-data="{ open: false }">
                <a href="#"
                   class="nav-link hover:text-blue-500"
                   @mouseover="open = true"
                   @mouseleave="open = false">
                    Fasilitas Kami
                </a>
                <div class="absolute top-full left-0 z-50"
                     @mouseover="open = true"
                     @mouseleave="open = false">
                    <ul class="bg-white shadow-md rounded-md mt-1 py-2 min-w-[10rem] dropdown-menu"
                        :class="{'show': open}">
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 1</a></li>
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 2</a></li>
                    </ul>
                </div>
            </li>
            <li>
                <a href="#" class="nav-link hover:text-blue-500">Cari Dokter</a>
            </li>
        </ul>
        <div class="flex">
            <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-1 px-3 border border-blue-500 hover:border-transparent rounded-full text-sm mr-2" type="button">Buat Janji Temu</button>
            <button class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-1 px-3 border border-green-500 hover:border-transparent rounded-full text-sm" type="button">Daftar/Masuk</button>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleMobileMenu = document.getElementById('toggleMobileMenu');
        const mobileMenu = document.getElementById('mobileMenu');

        if (toggleMobileMenu && mobileMenu) {
            toggleMobileMenu.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
