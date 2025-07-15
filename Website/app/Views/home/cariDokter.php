<!-- Section Cari Dokter -->
<section x-data="cariDokter()" class="container mx-auto px-4 py-10">
    <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Cari Dokter</h2>

    <!-- Search Bar -->
    <div class="flex justify-center mb-10">
        <input type="text"
            x-model="search"
            placeholder="Cari nama dokter atau spesialisasi..."
            class="w-full max-w-lg p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <!-- Daftar Dokter -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <template x-for="dokter in filteredDokter()" :key="dokter.nama">
            <div class="border rounded-lg shadow-md p-6 hover:shadow-lg transition">
                <div class="flex items-center space-x-4">
                    <!-- Menampilkan Foto Dokter -->
                    <img :src="dokter.foto" alt="Foto Dokter" class="rounded-full w-20 h-20 object-cover">
                    <div>
                        <!-- Menampilkan Nama Dokter -->
                        <h3 class="text-xl font-semibold text-gray-800" x-text="dokter.nama"></h3>
                        <!-- Menampilkan Spesialisasi Dokter -->
                        <p class="text-gray-500" x-text="dokter.spesialisasi"></p>
                    </div>
                </div>
                <div class="mt-4 text-gray-600 text-sm">
                    <!-- Menampilkan Lokasi dan Jadwal Dokter -->
                    <div x-text="'Lokasi: ' + dokter.lokasi"></div>
                    <div x-text="'Jadwal: ' + dokter.jadwal"></div>
                </div>
            </div>
        </template>

        <!-- Tampil jika tidak ditemukan -->
        <div x-show="filteredDokter().length === 0" class="col-span-full text-center text-gray-500">
            Dokter tidak ditemukan.
        </div>
    </div>
</section>

<script>
    function cariDokter() {
        return {
            search: '',
            dokterList: [{
                    nama: 'dr. Andi Saputra, Sp.PD',
                    spesialisasi: 'Spesialis Penyakit Dalam',
                    lokasi: 'RS Cabang Jakarta',
                    jadwal: 'Senin - Jumat, 08:00 - 14:00',
                },
                {
                    nama: 'dr. Maria Lestari, Sp.OG',
                    spesialisasi: 'Spesialis Kandungan',
                    lokasi: 'RS Cabang Bandung',
                    jadwal: 'Senin, Rabu, Jumat, 09:00 - 13:00',
                },
                {
                    nama: 'dr. Budi Santoso, Sp.B',
                    spesialisasi: 'Spesialis Bedah',
                    lokasi: 'RS Cabang Surabaya',
                    jadwal: 'Selasa & Kamis, 10:00 - 16:00',
                },
                {
                    nama: 'dr. Siti Aminah, Sp.A',
                    spesialisasi: 'Spesialis Anak',
                    lokasi: 'RS Cabang Bandung',
                    jadwal: 'Senin - Jumat, 08:00 - 12:00',
                }
            ],
            filteredDokter() {
                if (!this.search) {
                    return this.dokterList;
                }
                return this.dokterList.filter(d =>
                    d.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                    d.spesialisasi.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        }
    }
</script>
