<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi SMK')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tambahan font Inter biar rapi -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased h-screen flex flex-col overflow-hidden">

    <!-- Memanggil Navbar -->
    @include('components.navbar')

    <div class="flex flex-1 overflow-hidden">
        
        <!-- Sidebar hanya dipanggil jika variabel $isAdmin dikirim dan bernilai true -->
        @if(isset($isAdmin) && $isAdmin)
            @include('components.sidebar')
        @endif

        <!-- Area Konten Utama -->
        <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-gray-50">
            <div class="max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
        
    </div>

</body>
</html>