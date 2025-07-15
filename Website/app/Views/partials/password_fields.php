<div x-data="{ show: false }">
    <label for="password" class="block mb-1 font-semibold text-gray-700">Kata Sandi</label>
    <div class="relative">
        <input :type="show ? 'text' : 'password'" id="password" name="password" minlength="8"
            class="border border-gray-300 rounded px-3 py-2 w-full pr-10 focus:outline-none focus:ring-2 focus:ring-blue-200" required>
        <button type="button" @click="show = !show"
            class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 hover:text-blue-600 focus:outline-none">
            <i :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
        </button>
    </div>
    <small class="text-xs text-gray-500 block mt-1">
        Kata sandi minimal 8 karakter dan mengandung huruf, angka, dan karakter khusus.
    </small>
</div>

<div x-data="{ show: false }">
    <label for="confirm_password" class="block mb-1 font-semibold text-gray-700">Konfirmasi Kata Sandi</label>
    <div class="relative">
        <input :type="show ? 'text' : 'password'" id="confirm_password" name="confirm_password" minlength="8"
            class="border border-gray-300 rounded px-3 py-2 w-full pr-10 focus:outline-none focus:ring-2 focus:ring-blue-200" required>
        <button type="button" @click="show = !show"
            class="absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 hover:text-blue-600 focus:outline-none">
            <i :class="show ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
        </button>
    </div>
</div>
