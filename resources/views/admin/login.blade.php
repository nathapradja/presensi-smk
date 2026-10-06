<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - Sistem Absensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Login Administrator</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data presensi sekolah</p>
        </div>

        @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm text-center">
            {{ session('error') }}
        </div>
        @endif

        <form action="/admin/login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" class="w-full mt-1 border-gray-300 rounded p-2.5 border focus:ring-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="w-full mt-1 border-gray-300 rounded p-2.5 border focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-800 hover:bg-blue-900 text-white font-bold py-2.5 rounded transition">
                Masuk ke Dashboard
            </button>
        </form>
    </div>

</body>
</html>