<div>
    <label for="email" class="block mb-1 font-semibold text-gray-700">Email</label>
    <input type="email" id="email" name="email" value="<?= esc($email ?? old('email')) ?>"
        class="bg-gray-100 border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
</div>

<div>
    <label for="username" class="block mb-1 font-semibold text-gray-700">Username</label>
    <input type="text" id="username" name="username" value="<?= esc($username ?? old('username')) ?>"
        class="border border-gray-300 rounded px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-200" required>
</div>
