<h4 class="mt-6 text-lg font-semibold text-blue-600">Data Diri Pasien</h4>

<div class="mt-3">
    <label for="nama_pasien" class="block mb-1 font-semibold text-gray-700">Nama Lengkap</label>
    <input type="text" name="nama_pasien" id="nama_pasien"
        value="<?= esc($nama_pasien ?? old('nama_pasien')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
</div>

<div class="mt-3">
    <label for="no_identitas" class="block mb-1 font-semibold text-gray-700">No Identitas (KTP/SIM)</label>
    <input type="text" name="no_identitas" id="no_identitas"
        value="<?= esc($no_identitas ?? old('no_identitas')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
</div>

<div class="mt-3">
    <label for="jenis_kelamin" class="block mb-1 font-semibold text-gray-700">Jenis Kelamin</label>
    <select name="jenis_kelamin" id="jenis_kelamin"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
        <option value="">Pilih Gender</option>
        <option value="L" <?= (old('jenis_kelamin', $jenis_kelamin ?? '') === 'L') ? 'selected' : '' ?>>Laki-laki</option>
        <option value="P" <?= (old('jenis_kelamin', $jenis_kelamin ?? '') === 'P') ? 'selected' : '' ?>>Perempuan</option>
    </select>
</div>

<div class="mt-3">
    <label for="no_telp_pasien" class="block mb-1 font-semibold text-gray-700">Nomor Telepon</label>
    <input type="text" name="no_telp_pasien" id="no_telp_pasien"
        value="<?= esc($no_telp_pasien ?? old('no_telp_pasien')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
</div>

<div class="mt-3">
    <label for="tempat_lahir" class="block mb-1 font-semibold text-gray-700">Tempat Lahir</label>
    <input type="text" name="tempat_lahir" id="tempat_lahir"
        value="<?= esc($tempat_lahir ?? old('tempat_lahir')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
</div>

<div class="mt-3">
    <label for="tanggal_lahir" class="block mb-1 font-semibold text-gray-700">Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" id="tanggal_lahir"
        value="<?= esc($tanggal_lahir ?? old('tanggal_lahir')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
</div>

<div class="mt-3">
    <label for="lokasi" class="block mb-1 font-semibold text-gray-700">Lokasi Rumah Sakit</label>
    <select name="lokasi" id="lokasi"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required>
        <option value="">Pilih Lokasi</option>
        <option value="JKT" <?= (old('lokasi', $lokasi ?? '') === 'JKT') ? 'selected' : '' ?>>Cabang Jakarta</option>
        <option value="BDG" <?= (old('lokasi', $lokasi ?? '') === 'BDG') ? 'selected' : '' ?>>Cabang Bandung</option>
        <option value="SBY" <?= (old('lokasi', $lokasi ?? '') === 'SBY') ? 'selected' : '' ?>>Cabang Surabaya</option>
    </select>
</div>

<div class="mt-3">
    <label for="alamat" class="block mb-1 font-semibold text-gray-700">Alamat</label>
    <textarea name="alamat" id="alamat" rows="2"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200"
        required><?= esc($alamat ?? old('alamat')) ?></textarea>
</div>
