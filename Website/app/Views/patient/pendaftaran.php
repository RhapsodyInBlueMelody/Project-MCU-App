<div x-data="appointmentForm()">
    <form class="p-5" action="<?= base_url(
        "patient/appointment"
    ) ?>" method="post">
    <div class="space-y-12">

        <?php if (session()->has("success")): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata(
                    "success"
                ) ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->has("error")): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= session()->getFlashdata(
                    "error"
                ) ?></span>
            </div>
        <?php endif; ?>
        <div class="border-b border-gray-900/10 pb-12">
            <h2 class="text-base/7 font-semibold text-gray-900">Buat Janji Temu</h2>
            <p class="mt-1 text-sm/6 text-gray-600">Silahkan isi data untuk membuat janji temu</p>

            <div class="mt-5 grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-6">
                <div class="sm:col-span-4">
                    <label for="nama_janji" class="block text-sm font-medium leading-6 text-gray-900">Nama Janji <span class="text-red-500">*</span></label>
                    <div class="mt-2">
                        <input type="text" name="nama_janji" id="nama_janji" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Mis: Konsultasi Umum, Medical Check-Up, dsb">
                    </div>
                </div>

                <div class="sm:col-span-3 mb-4">
                    <label for="tanggal_janji" class="block text-sm/6 font-medium text-gray-900">Tanggal Janji <span class="text-red-500">*</span></label>
                    <div class="mt-2 grid grid-cols-1">
                        <input type="date" id="tanggal_janji" name="tanggal_janji"
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:max-w-xs sm:text-sm sm:leading-6"
                               required
                               min="<?= date("Y-m-d") ?>">
                    </div>
                </div>
                <div class="sm:col-span-3">
                    <label for="waktu_janji" class="block text-sm font-medium leading-6 text-gray-900">Waktu Janji <span class="text-red-500">*</span></label>
                    <div class="mt-2">
                        <input type="time" name="waktu_janji" id="waktu_janji" required
                               class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                               min="08:00" max="17:00" step="1800">
                        <small class="text-gray-500">Tersedia antara pukul 08:00 AM - 05:00 PM, interval 30 menit</small>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="sm:col-span-4 mb-4">
                <label for="paket_terpilih" class="block text-sm font-medium leading-6 text-gray-900">Pilih Paket Medical Check-Up <span class="text-red-500">*</span></label>
                <div class="mt-2">
                    <select
                        id="paket_terpilih"
                        name="paket_terpilih"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        required
                        x-model="selectedPackage"
                        @change="updateDoctorsByPackage()"
                    >
                        <option value="">-- Pilih Paket --</option>
                        <template x-for="paket in packages" :key="paket.id_paket">
                            <option :value="paket.id_paket" x-text="`${paket.nama_paket} - ${paket.deskripsi_singkat} - Rp ${formatCurrency(paket.harga_paket)}`"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="sm:col-span-3 mb-4">
                <label class="block text-sm/6 font-medium text-gray-900">Cari Dokter <span class="text-red-500">*</span></label>
                <div class="mt-2">
                    <input
                        type="text"
                        placeholder="Cari nama dokter atau spesialisasi..."
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        x-model="searchTerm"
                    >
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm/6 font-medium text-gray-900 mb-2">Filter berdasarkan spesialisasi</label>
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        class="rounded-md px-3 py-1.5 text-sm font-medium shadow-sm"
                        :class="selectedSpecialization === '' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900 hover:bg-gray-200'"
                        @click="resetSpecialization()"
                    >
                        Semua
                    </button>
                    <template x-for="spec in specializations" :key="spec.id">
                        <button
                            type="button"
                            class="rounded-md px-3 py-1.5 text-sm font-medium shadow-sm"
                            :class="selectedSpecialization === spec.id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-900 hover:bg-gray-200'"
                            @click="selectedSpecialization = spec.id"
                            x-text="spec.name"
                        ></button>
                    </template>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm/6 font-medium text-gray-900 mb-2">Pilih Dokter <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="doctor in filteredDoctors" :key="doctor.ID_DOKTER">
                        <div class="border rounded-md p-4 hover:shadow-md cursor-pointer"
                             :class="{'border-indigo-600 bg-indigo-50': $el.querySelector('input').checked}"
                             @click="$el.querySelector('input').click()">
                            <div class="flex items-start">
                                <input type="radio" name="id_dokter" :value="doctor.ID_DOKTER" class="mt-1 mr-2" required>
                                <div>
                                    <div class="font-medium" x-text="doctor.NAMA_DOKTER"></div>
                                    <div class="text-sm text-gray-500" x-text="doctor.nama_spesialisasi"></div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredDoctors.length === 0" class="col-span-full text-center py-4 text-gray-500">
                        Tidak ada dokter yang sesuai dengan kriteria pencarian.
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <button type="button" class="text-sm/6 font-semibold text-gray-900">Cancel</button>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
            </div>
    <?php if (isset($pasien_id)): ?>
        <input type="hidden" name="id_pasien" value="<?= $pasien_id ?>">
    <?php endif; ?>
</form>
</div>

<script>
    // Use window object to explicitly make these global
    window.doctorsData = <?= json_encode($doctors) ?>;
    window.packagesData = <?= json_encode($packages) ?>;
</script>
<script src="/assets/js/appointment.js"></script>
