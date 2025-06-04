<div x-data="appointmentForm()" x-init="init()" class="w-full max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-4 sm:p-8">
    <form action="<?= base_url("patient/appointment") ?>" method="post" class="space-y-8">
        <!-- Flash Messages -->
        <?php if (session()->has("success")): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?= session()->getFlashdata("success") ?>
            </div>
        <?php endif; ?>
        <?php if (session()->has("error")): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= session()->getFlashdata("error") ?>
            </div>
        <?php endif; ?>

        <!-- Section: Appointment Information -->
        <div>
            <h2 class="text-lg md:text-xl font-bold text-indigo-700 mb-1">Buat Janji Temu</h2>
            <p class="text-gray-500 mb-4">Isi data untuk membuat janji temu dengan dokter pilihan Anda.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama_janji" class="block text-sm font-medium text-gray-700">Nama Janji <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_janji" id="nama_janji" required
                        class="mt-1 block w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="Konsultasi Umum, Medical Check-Up, dsb">
                </div>
                <div>
                    <label for="tanggal_janji" class="block text-sm font-medium text-gray-700">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_janji" name="tanggal_janji"
                        class="mt-1 block w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        required min="<?= date("Y-m-d") ?>">
                </div>
                <div>
                    <label for="waktu_janji" class="block text-sm font-medium text-gray-700">Waktu <span class="text-red-500">*</span></label>
                    <input type="time" name="waktu_janji" id="waktu_janji" required
                        class="mt-1 block w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        min="08:00" max="17:00" step="1800">
                    <span class="text-xs text-gray-500 block mt-1">Antara 08:00 - 17:00, interval 30 menit</span>
                </div>
            </div>
        </div>

        <!-- Section: Pilih Paket -->
        <div>
            <h3 class="text-base font-semibold text-indigo-700 mb-2">Pilih Paket Medical Check-Up</h3>
            <select id="paket_terpilih" name="paket_terpilih"
                class="block w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 mb-2"
                required x-model="selectedPackage" @change="updateDoctorsByPackage()">
                <option value="">-- Pilih Paket --</option>
                <template x-for="paket in filteredPackages" :key="paket.id_paket">
                    <option :value="paket.id_paket" x-text="`${paket.nama_paket} (${paket.deskripsi}) - Rp${formatCurrency(paket.harga)}`"></option>
                </template>
            </select>
            <template x-if="selectedPackageObj">
                <div class="bg-indigo-50 border-l-4 border-indigo-400 p-3 rounded">
                    <div class="font-semibold" x-text="selectedPackageObj.nama_paket"></div>
                    <div class="text-sm text-gray-600" x-text="selectedPackageObj.deskripsi"></div>
                    <div class="text-sm font-bold text-indigo-600" x-text="'Rp' + formatCurrency(selectedPackageObj.harga)"></div>
                </div>
            </template>
        </div>

        <!-- Section: Cari & Pilih Dokter -->
        <div>
            <h3 class="text-base font-semibold text-indigo-700 mb-2">Cari & Pilih Dokter</h3>
            <input type="text"
                placeholder="Cari nama dokter atau spesialisasi..."
                class="block w-full rounded-md border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 mb-3"
                x-model="searchTerm"
            >
            <div class="flex flex-wrap gap-2 mb-3">
                <button
                    type="button"
                    class="rounded px-3 py-1.5 text-sm font-medium transition min-w-[96px]"
                    :class="selectedSpecialization === '' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300'"
                    @click="resetSpecialization()">
                    Semua
                </button>
                <template x-for="spec in specializations" :key="spec.id">
                    <button
                        type="button"
                        class="rounded px-3 py-1.5 text-sm font-medium transition min-w-[96px]"
                        :class="selectedSpecialization === spec.id ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-900 hover:bg-gray-300'"
                        @click="selectedSpecialization = spec.id"
                        x-text="spec.name"
                    ></button>
                </template>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <template x-for="doctor in filteredDoctors" :key="doctor.id_dokter">
                    <label class="block border rounded-lg p-4 cursor-pointer hover:shadow-md transition bg-gray-50"
                        :class="{'border-indigo-600 bg-indigo-50': selectedDoctor === doctor.id_dokter}">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="id_dokter" :value="doctor.id_dokter" class="accent-indigo-600" required x-model="selectedDoctor">
                            <div>
                                <div class="font-semibold text-gray-900" x-text="doctor.nama_dokter"></div>
                                <div class="text-sm text-gray-600" x-text="doctor.nama_spesialisasi"></div>
                            </div>
                        </div>
                    </label>
                </template>
            </div>
            <div x-show="filteredDoctors.length === 0" class="text-center py-4 text-gray-500">
                Tidak ada dokter yang sesuai dengan kriteria pencarian.
            </div>
        </div>

        <!-- Section: Actions -->
        <div class="flex flex-col sm:flex-row justify-end gap-4">
            <button type="button" class="rounded-md border border-indigo-600 px-6 py-2 text-sm font-semibold text-indigo-600 bg-white hover:bg-indigo-50 transition">Batal</button>
            <button type="submit" class="rounded-md bg-indigo-600 px-6 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 transition">Simpan</button>
        </div>
        <?php if (isset($pasien_id)): ?>
            <input type="hidden" name="id_pasien" value="<?= $pasien_id ?>">
        <?php endif; ?>
    </form>
</div>

<script>
window.doctorsData = <?= json_encode($doctors) ?>;
window.packagesData = <?= json_encode($packages) ?>;
</script>
<script src="/assets/js/appointment.js"></script>

