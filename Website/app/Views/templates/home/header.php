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
                },
            },
        }
    </script>
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
<nav class="bg-gray-100 py-2 px-5">
    <div class="container mx-auto flex justify-between items-center">
        <a href="#" class="mr-8">
            <div class="border border-gray-300 w-32 h-10 flex justify-center items-center text-gray-600 text-xs">
                Image Placeholder
            </div>
        </a>
        <button class="md:hidden focus:outline-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation"
                x-data="{ open: false }"
                @click="open = !open">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
            </svg>
        </button>
        <div class="hidden md:flex flex-grow justify-between items-center" id="navbarNav"
             x-data="{ open: false }"
             :class="{'hidden': !open, 'flex': open}"
             @click.away="open = false">
            <ul class="flex space-x-4 mr-auto">
                <li>
                    <a href="#" class="nav-link active text-blue-500">Beranda</a>
                </li>
                <li class="relative" x-data="{ openDropdownRumahSakit: false }">
                    <a href="#" class="nav-link hover:text-blue-500" @mouseover="openDropdownRumahSakit = true" @mouseleave="openDropdownRumahSakit = false">
                        Rumah Sakit Kami
                    </a>
                    <ul class="absolute top-full left-0 bg-white shadow-md rounded-md mt-1 py-2 min-w-[10rem] transition-all origin-top scale-y-0"
                        x-show="openDropdownRumahSakit"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-y-50"
                        x-transition:enter-end="opacity-100 transform scale-y-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-y-100"
                        x-transition:leave-end="opacity-0 transform scale-y-50"
                        @mouseover="openDropdownRumahSakit = true"
                        @mouseleave="openDropdownRumahSakit = false">
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 1</a></li>
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 2</a></li>
                    </ul>
                </li>
                <li class="relative" x-data="{ openDropdownFasilitas: false }">
                    <a href="#" class="nav-link hover:text-blue-500" @mouseover="openDropdownFasilitas = true" @mouseleave="openDropdownFasilitas = false">
                        Fasilitas Kami
                    </a>
                    <ul class="absolute top-full left-0 bg-white shadow-md rounded-md mt-1 py-2 min-w-[10rem] transition-all origin-top scale-y-0"
                        x-show="openDropdownFasilitas"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-y-50"
                        x-transition:enter-end="opacity-100 transform scale-y-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-y-100"
                        x-transition:leave-end="opacity-0 transform scale-y-50"
                        @mouseover="openDropdownFasilitas = true"
                        @mouseleave="openDropdownFasilitas = false">
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 1</a></li>
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Sub Menu 2</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="nav-link hover:text-blue-500">Cari Dokter</a>
                </li>
                <li class="md:hidden mt-2">
                    <div class="flex items-center">
                        <div id="flag-display-nav" class="w-6 h-4 mr-2 border border-gray-300 bg-cover bg-center" style="background-image: url('https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png');"></div>
                        <div class="inline-flex items-center mr-2">
                            <input type="radio" name="language-nav" id="en-nav" value="en" class="form-radio">
                            <label for="en-nav" class="ml-1 text-sm">EN</label>
                        </div>
                        <div class="inline-flex items-center">
                            <input type="radio" name="language-nav" id="id-nav" value="id" checked class="form-radio">
                            <label for="id-nav" class="ml-1 text-sm">ID</label>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="flex">
                <button class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-1 px-3 border border-blue-500 hover:border-transparent rounded-full text-sm mr-2" type="button">Buat Janji Temu</button>
                <button class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-1 px-3 border border-green-500 hover:border-transparent rounded-full text-sm" type="button">Daftar/Masuk</button>
            </div>
        </div>
    </div>
</nav>