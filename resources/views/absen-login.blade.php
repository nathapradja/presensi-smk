<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerbang Absensi SMK</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Absensi Harian</h1>
            <p class="text-gray-500 text-sm">Silakan pilih kelas dan masukkan PIN</p>
        </div>

        <!-- Perhatikan action-nya mengarah ke route POST yang kita buat -->
        <form action="/wali-kelas/verify-pin" method="POST" class="space-y-5">
            <!-- Ini wajib ada di Laravel agar aman dari serangan form -->
            @csrf 

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kelas</label>
                <select name="kelas_id" class="w-full border-gray-300 rounded-lg shadow-sm p-3 border focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Pilih Kelas Anda --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">PIN Kelas (4 Digit)</label>
                <input type="password" name="pin" maxlength="4" class="w-full border-gray-300 rounded-lg shadow-sm p-3 border focus:ring-blue-500 focus:border-blue-500 text-center tracking-widest text-lg font-bold" placeholder="••••" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200">
                Buka Absensi
            </button>
        </form>
    </div>

</body>
</html>