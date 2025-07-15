<div class="max-w-xl mx-auto bg-white mt-8 p-6 rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-4 text-blue-700">Tambah Jadwal Praktek</h2>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('error') ?></div>
    <?php endif ?>
    <form action="<?= base_url('dokter/schedule/add') ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block font-semibold mb-2">Hari Praktek</label>
            <div class="grid grid-cols-2 gap-2">
                <?php
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                foreach ($days as $day): ?>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="hari[]" value="<?= $day ?>" class="form-checkbox text-blue-600">
                        <span class="ml-2"><?= $day ?></span>
                    </label>
                <?php endforeach ?>
            </div>
        </div>
        <div>
            <label class="block font-semibold mb-1">Jam Mulai</label>
            <input type="time" name="jam_mulai" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block font-semibold mb-1">Jam Selesai</label>
            <input type="time" name="jam_selesai" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block font-semibold mb-1">Lokasi</label>
            <select name="lokasi" class="border rounded px-3 py-2 w-full" required>
                <option value="">Pilih Lokasi</option>
                <option value="Jakarta">Jakarta</option>
                <option value="Bandung">Bandung</option>
                <option value="Surabaya">Surabaya</option>
            </select>
        </div>
        <div class="text-right">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Simpan Jadwal</button>
            <a href="<?= base_url('dokter/profile') ?>" class="ml-2 text-gray-600 hover:underline">Batal</a>
        </div>
    </form>
</div>
