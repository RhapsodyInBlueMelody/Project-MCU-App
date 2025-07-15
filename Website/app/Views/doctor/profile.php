<div class="max-w-3xl mx-auto p-6 bg-white shadow-lg rounded-xl mt-8">
    <h2 class="text-2xl font-bold mb-6 text-blue-700">Profil Dokter</h2>
    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 text-green-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('error') ?></div>
    <?php endif ?>
    <form action="<?= base_url('dokter/update-profile') ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="font-semibold">Nama</label>
                <input type="text" name="nama_dokter" value="<?= esc($doctor['nama_dokter']) ?>"
                    class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border rounded px-3 py-2 mt-1">
                    <option value="L" <?= $doctor['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="P" <?= $doctor['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="font-semibold">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="<?= esc($doctor['tanggal_lahir']) ?>"
                    class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">No. Lisensi</label>
                <input type="text" name="no_lisensi" value="<?= esc($doctor['no_lisensi']) ?>"
                    class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">Telepon</label>
                <input type="text" name="telepon_dokter" value="<?= esc($doctor['telepon_dokter']) ?>"
                    class="w-full border rounded px-3 py-2 mt-1">
            </div>
            <div>
                <label class="font-semibold">Lokasi Kerja</label>
                <select name="lokasi_kerja" class="w-full border rounded px-3 py-2 mt-1">
                    <option value="Jakarta" <?= $doctor['lokasi_kerja'] == 'Jakarta' ? 'selected' : '' ?>>Jakarta</option>
                    <option value="Bandung" <?= $doctor['lokasi_kerja'] == 'Bandung' ? 'selected' : '' ?>>Bandung</option>
                    <option value="Surabaya" <?= $doctor['lokasi_kerja'] == 'Surabaya' ? 'selected' : '' ?>>Surabaya</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="font-semibold">Alamat</label>
                <textarea name="alamat_dokter" rows="2"
                    class="w-full border rounded px-3 py-2 mt-1"><?= esc($doctor['alamat_dokter']) ?></textarea>
            </div>
        </div>
        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">Simpan Profil</button>
        </div>
    </form>

    <hr class="my-8">

    <h3 class="text-xl font-semibold mb-4 text-blue-700">Jadwal Praktek</h3>
    <!-- Show schedule here, and add/edit/delete buttons -->
    <div>
        <?php if (!empty($schedules)): ?>
            <table class="w-full text-left border mb-4">
                <thead>
                    <tr>
                        <th class="p-2 font-semibold">Hari</th>
                        <th class="p-2 font-semibold">Jam Mulai</th>
                        <th class="p-2 font-semibold">Jam Selesai</th>
                        <th class="p-2 font-semibold">Lokasi</th>
                        <th class="p-2 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $jadwal): ?>
                        <tr>
                            <td class="p-2"><?= esc($jadwal['hari']) ?></td>
                            <td class="p-2"><?= esc($jadwal['jam_mulai']) ?></td>
                            <td class="p-2"><?= esc($jadwal['jam_selesai']) ?></td>
                            <td class="p-2"><?= esc($jadwal['lokasi']) ?></td>
                            <td class="p-2">
                                <!-- For now, you can just show a delete link. You can do edit later. -->
                                <form action="<?= base_url('dokter/schedule/delete/' . $jadwal['id_jadwal']) ?>" method="post" class="inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('Hapus jadwal ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="text-gray-500 mb-4">Belum ada jadwal tersedia.</div>
        <?php endif; ?>
        <a href="<?= base_url('dokter/schedule/add') ?>"
            class="inline-block bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow">Tambah Jadwal</a>
    </div>
</div>
