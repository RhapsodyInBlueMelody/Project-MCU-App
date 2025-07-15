<div class="mt-3">
    <label for="<?= esc($name) ?>" class="block mb-1 font-semibold text-gray-700">
        <?= esc($label ?? 'Spesialisasi') ?>
    </label>
    <select name="<?= esc($name) ?>" id="<?= esc($name) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
        <option value="">Pilih Spesialisasi</option>
        <?php foreach ($list as $spesialisasi): ?>
            <option value="<?= esc($spesialisasi['id']) ?>" <?= old($name) === $spesialisasi['id'] ? 'selected' : '' ?>>
                <?= esc($spesialisasi['nama']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
