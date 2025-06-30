<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medical Checkup - Admin Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-indigo-600 py-4">
                <h2 class="text-center text-2xl font-bold text-white">
                    Medical Checkup
                </h2>
                <p class="text-center text-white text-sm mt-1">
                    Admin Login
                </p>
            </div>

            <div class="p-6">
                <?php if (session()->getFlashdata("msg")): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= session()->getFlashdata(
                            "msg"
                        ) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata("success")): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= session()->getFlashdata(
                            "success"
                        ) ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url(
                    "auth/authenticate"
                ) ?>" method="post" class="mt-4">
                    <input type="hidden" name="role" value="admin">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username or Email</label>
                        <input type="text" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="username" id="username" placeholder="Enter username or email" required>
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password" id="password" placeholder="Enter password" required>
                    </div>
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Login
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        <a href="<?= base_url(
                            "auth/forgot-password/admin"
                        ) ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Forgot Password?
                        </a>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        <a href="<?= base_url() ?>" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Back to Home
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
