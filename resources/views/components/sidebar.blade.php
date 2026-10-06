<aside class="w-64 bg-white border-r border-gray-200 min-h-[calc(100vh-4rem)] hidden md:block">
    <div class="h-full px-3 py-4 overflow-y-auto">
        
        <div class="mb-5 px-3">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Navigasi Admin</h3>
        </div>
        
        <ul class="space-y-2 font-medium">
            <!-- Link ke Dashboard Utama -->
            <li>
                <a href="/admin/dashboard" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition group {{ request()->is('admin/dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <span class="ml-3">Dashboard (Reset PIN)</span>
                </a>
            </li>
            
            <!-- Link ke Halaman Persentase Grafik (Ini yang kamu cari!) -->
            <li>
                <a href="/admin/laporan" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition group {{ request()->is('admin/laporan') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <span class="ml-3">Grafik Persentase</span>
                </a>
            </li>
        </ul>

        <div class="mt-8 mb-5 px-3">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Data Master</h3>
        </div>
        
        <ul class="space-y-2 font-medium">
            <!-- Link untuk kembali ke mode Guru / Gerbang Absen -->
            <li>
                <a href="/" class="flex items-center p-3 text-gray-900 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition group">
                    <span class="ml-3">Keluar ke Gerbang Absen</span>
                </a>
            </li>
        </ul>
        
    </div>
</aside>