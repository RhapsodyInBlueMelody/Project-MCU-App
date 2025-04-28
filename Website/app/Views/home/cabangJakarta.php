<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>RS Cabang Jakarta</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800">

    <!-- Header -->
    <header class="bg-blue-600 text-white py-6 shadow-md">
        <div class="max-w-6xl mx-auto px-4">
            <h1 class="text-3xl font-bold">Rumah Sakit Cabang Jakarta</h1>
            <p class="text-sm">Bagian dari jaringan Rumah Sakit Kami</p>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-6xl mx-auto px-4 py-10">

        <!-- Deskripsi -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-4">Tentang Kami</h2>
            <p class="text-gray-700 leading-relaxed">
                RS Cabang Jakarta berdiri sejak 2005 dan telah menjadi pusat pelayanan kesehatan terpercaya di wilayah Jakarta. Kami menyediakan layanan medis 24 jam dengan dokter dan tenaga medis profesional.
            </p>
        </section>

        <!-- Fasilitas -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-8">Fasilitas Unggulan</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- IGD -->
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
                    <svg class="h-12 w-12 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-9 6h18a2 2 0 002-2V6a2 2 0 00-2-2H3a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="font-bold text-lg mb-2">IGD 24 Jam</h3>
                    <p>Siap melayani pasien darurat dengan cepat dan profesional.</p>
                </div>

                <!-- Rawat Inap -->
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
                    <svg class="h-12 w-12 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <h3 class="font-bold text-lg mb-2">Ruang Rawat Inap Nyaman</h3>
                    <p>Dilengkapi fasilitas modern dan privasi pasien.</p>
                </div>

                <!-- Laboratorium -->
                <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center text-center">
                    <svg class="h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-6h6v6m-2 4a6 6 0 100-12 6 6 0 000 12z"></path>
                    </svg>
                    <h3 class="font-bold text-lg mb-2">Laboratorium & Radiologi</h3>
                    <p>Hasil cepat dan akurat untuk kebutuhan diagnosis.</p>
                </div>

            </div>
        </section>

        <!-- Kontak -->
        <section class="mb-10">
            <h2 class="text-2xl font-semibold mb-4">Kontak Kami</h2>
            <p><strong>Alamat:</strong> Jl. Kesehatan No.10, Jakarta Pusat</p>
            <p><strong>Telepon:</strong> (021) 123-4567</p>
            <p><strong>Email:</strong> info@rscabangjakarta.co.id</p>
        </section>

        <!-- Lokasi -->
        <section>
            <h3 class="text-xl font-bold mb-4">Lokasi Kami</h3>
            <div class="w-full h-64 rounded-lg overflow-hidden shadow-lg">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.3773958155087!2d106.81666641476994!3d-6.200000295517238!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f5c3f3b0d35b%3A0x6eec8ef6531ef1dd!2sJakarta%20Pusat!5e0!3m2!1sid!2sid!4v1610000000000!5m2!1sid!2sid"
                    width="100%"
                    height="100%"
                    style="border:0;"
                    allowfullscreen=""
                    loading="lazy">
                </iframe>
            </div>
        </section>

</html>