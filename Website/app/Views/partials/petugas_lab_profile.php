<h4 class="mt-6 text-lg font-semibold text-blue-600">Data Diri Petugas Lab</h4>
<div class="mt-3">
    <label for="nama_petugas_lab" class="block mb-1 font-semibold text-gray-700">Nama Lengkap</label>
    <input type="text" name="nama_petugas_lab" id="nama_petugas_lab" value="<?= esc($nama_petugas_lab ?? old('nama_petugas_lab')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
</div>
<div class="mt-3">
    <label for="jenis_kelamin" class="block mb-1 font-semibold text-gray-700">Jenis Kelamin</label>
    <select name="jenis_kelamin" id="jenis_kelamin"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
        <option value="">Pilih Gender</option>
        <option value="L" <?= old('jenis_kelamin') === 'L' ? 'selected' : '' ?>>Laki-Laki</option>
        <option value="P" <?= old('jenis_kelamin') === 'P' ? 'selected' : '' ?>>Perempuan</option>
    </select>
</div>
<div class="mt-3">
    <label for="tanggal_lahir" class="block mb-1 font-semibold text-gray-700">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" id="tanggal_lahir" value="<?= esc($tanggal_lahir ?? old('tanggal_lahir')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
</div>
<div class="mt-3">
    <label for="telepon_petugas_lab" class="block mb-1 font-semibold text-gray-700">Nomor Telepon</label>
    <input type="text" name="telepon_petugas_lab" id="telepon_petugas_lab" value="<?= esc($telepon_petugas_lab ?? old('telepon_petugas_lab')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
</div>
<div class="mt-3">
    <label for="lokasi_kerja" class="block mb-1 font-semibold text-gray-700">Lokasi Kerja</label>
    <select name="lokasi_kerja" id="lokasi_kerja"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
        <option value="">Pilih Lokasi</option>
        <option value="Jakarta" <?= old('lokasi_kerja') === 'Jakarta' ? 'selected' : '' ?>>Cabang Jakarta</option>
        <option value="Bandung" <?= old('lokasi_kerja') === 'Bandung' ? 'selected' : '' ?>>Cabang Bandung</option>
        <option value="Surabaya" <?= old('lokasi_kerja') === 'Surabaya' ? 'selected' : '' ?>>Cabang Surabaya</option>
    </select>
</div>
<div class="mt-3">
    <label for="no_lisensi" class="block mb-1 font-semibold text-gray-700">Nomor Lisensi</label>
    <input type="text" name="no_lisensi" id="no_lisensi" value="<?= esc($no_lisensi ?? old('no_lisensi')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
</div>
<div class="mt-3">
    <label for="alamat_petugas_lab" class="block mb-1 font-semibold text-gray-700">Alamat</label>
    <textarea name="alamat_petugas_lab" id="alamat_petugas_lab" rows="2"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required><?= esc($alamat_petugas_lab ?? old('alamat_petugas_lab')) ?></textarea>
</div>
