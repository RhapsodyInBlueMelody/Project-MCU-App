<div class="max-w-md mx-auto p-6 bg-white shadow-lg rounded-xl mt-10">
    <h2 class="text-2xl font-bold mb-6 text-blue-700">Ganti Password</h2>

    <!-- Flash messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="bg-green-100 text-green-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 text-red-700 px-4 py-3 mb-4 rounded"><?= session()->getFlashdata('error') ?></div>
    <?php endif ?>

    <form action="<?= base_url('petugas_lab/profile/password') ?>" method="post" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="font-semibold">Password Baru</label>
            <input type="password" name="new_password" class="w-full border rounded px-3 py-2 mt-1" required minlength="6">
        </div>
        <div>
            <label class="font-semibold">Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" class="w-full border rounded px-3 py-2 mt-1" required minlength="6">
        </div>
        <div class="text-right">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700">Ganti Password</button>
        </div>
    </form>
</div>
