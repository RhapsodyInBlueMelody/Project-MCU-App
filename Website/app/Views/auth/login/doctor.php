<!DOCTYPE html>
<html lang="en">
<head>
    <title>Medical Checkup - Login Dokter</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-blue-600 py-4">
                <h2 class="text-center text-2xl font-bold text-white">
                    Medical Checkup
                </h2>
                <p class="text-center text-white text-sm mt-1">
                    Login Dokter
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
                    <input type="hidden" name="role" value="doctor">
                    <div class="mb-4">
                        <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username or Email</label>
                        <input type="text" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="username" id="username" placeholder="Enter username or email" required>
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="password" id="password" placeholder="Enter password" required>
                        <a href="<?= base_url(
                            "auth/forgot-password/doctor"
                        ) ?>" class="text-xs text-blue-600 hover:text-blue-800 mt-1 inline-block">Forgot password?</a>
                    </div>
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Login
                        </button>
                    </div>
                </form>

                <div class="flex items-center justify-between my-4">
                    <hr class="w-full border-t border-gray-300">
                    <span class="px-3 text-gray-500 bg-white">OR</span>
                    <hr class="w-full border-t border-gray-300">
                </div>

                <div class="mb-4">
                    <a href="<?= base_url(
                        "auth/google/login/doctor"
                    ) ?>" class="w-full flex justify-center items-center bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-gray-300 rounded shadow">
                        <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg">
                            <g transform="matrix(1, 0, 0, 1, 27.009001, -39.238998)">
                                <path fill="#4285F4" d="M -3.264 51.509 C -3.264 50.719 -3.334 49.969 -3.454 49.239 L -14.754 49.239 L -14.754 53.749 L -8.284 53.749 C -8.574 55.229 -9.424 56.479 -10.684 57.329 L -10.684 60.329 L -6.824 60.329 C -4.564 58.239 -3.264 55.159 -3.264 51.509 Z"/>
                                <path fill="#34A853" d="M -14.754 63.239 C -11.514 63.239 -8.804 62.159 -6.824 60.329 L -10.684 57.329 C -11.764 58.049 -13.134 58.489 -14.754 58.489 C -17.884 58.489 -20.534 56.379 -21.484 53.529 L -25.464 53.529 L -25.464 56.619 C -23.494 60.539 -19.444 63.239 -14.754 63.239 Z"/>
                                <path fill="#FBBC05" d="M -21.484 53.529 C -21.734 52.809 -21.864 52.039 -21.864 51.239 C -21.864 50.439 -21.724 49.669 -21.484 48.949 L -21.484 45.859 L -25.464 45.859 C -26.284 47.479 -26.754 49.299 -26.754 51.239 C -26.754 53.179 -26.284 54.999 -25.464 56.619 L -21.484 53.529 Z"/>
                                <path fill="#EA4335" d="M -14.754 43.989 C -12.984 43.989 -11.404 44.599 -10.154 45.789 L -6.734 42.369 C -8.804 40.429 -11.514 39.239 -14.754 39.239 C -19.444 39.239 -23.494 41.939 -25.464 45.859 L -21.484 48.949 C -20.534 46.099 -17.884 43.989 -14.754 43.989 Z"/>
                            </g>
                        </svg>
                        Login with Google
                    </a>
                </div>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Belum memiliki akun?
                        <a href="<?= base_url(
                            "auth/register/doctor"
                        ) ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
