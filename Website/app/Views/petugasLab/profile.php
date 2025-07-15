<div class="max-w-3xl mx-auto p-6 bg-white shadow-lg rounded-xl mt-8">
    <h2 class="text-2xl font-bold mb-6 text-blue-700">Profil Petugas Laboratorium</h2>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 text-green-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('error') ?></div>
    <?php endif ?>

    <form action="<?= base_url('petugas_lab/profile/update') ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="font-semibold">Nama</label>
                <input type="text" name="nama" value="<?= esc($profile['nama_petugas_lab'] ?? '') ?>" class="w-full border rounded px-3 py-2 mt-1" required>
            </div>
            <div>
                <label class="font-semibold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border rounded px-3 py-2 mt-1" required>
                    <option value="L" <?= ($profile['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= ($profile['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="font-semibold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?= esc($profile['tanggal_lahir'] ?? '') ?>" class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">No. Lisensi</label>
                <input type="text" name="nip" value="<?= esc($profile['no_lisensi'] ?? '') ?>" class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">Telepon</label>
                <input type="text" name="telepon" value="<?= esc($profile['telepon_petugas_lab'] ?? '') ?>" class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">Spesialisasi</label>
                <input type="text" value="<?= esc($profile['spesialisasi_lab'] ?? '') ?>" class="w-full border rounded px-3 py-2 mt-1 bg-gray-100" disabled>
            </div>
            <div class="md:col-span-2">
                <label class="font-semibold">Alamat</label>
                <textarea name="alamat" rows="2" class="w-full border rounded px-3 py-2 mt-1"><?= esc($profile['alamat_petugas_lab'] ?? '') ?></textarea>
            </div>
        </div>
        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">Simpan Profil</button>
        </div>
    </form>

    <hr class="my-8">

    <div class="flex items-center justify-between">
        <h3 class="text-xl font-semibold text-blue-700 mb-0">Keamanan Akun</h3>
        <a href="<?= base_url('petugas_lab/profile/password') ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">Ganti Password</a>
    </div>
</div>
