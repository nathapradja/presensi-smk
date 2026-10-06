@extends('layouts.app')

@section('title', 'Laporan & Analitik Kehadiran')

@section('content')
<!-- CSS Khusus Print (Isolasi Bagian yang Dicetak) -->
<style>
    @media print {
        aside, nav, .no-print { display: none !important; }
        body, main { background-color: white !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .shadow-sm { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        .print-break-avoid { page-break-inside: avoid; }
        
        /* Logika Isolasi Kertas Print */
        body.print-sekolah #sectionJurusan, body.print-sekolah #sectionKelas { display: none !important; }
        body.print-jurusan #sectionSekolah, body.print-jurusan #sectionKelas { display: none !important; }
        body.print-kelas #sectionSekolah, body.print-kelas #sectionJurusan { display: none !important; }
        
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
    }
    .print-header { display: none; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="mb-6 flex justify-between items-center no-print">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pusat Laporan & Analitik Kehadiran</h2>
        <p class="text-gray-500 text-sm mt-1">Pantau kehadiran secara real-time berdasarkan harian, mingguan, atau bulanan</p>
    </div>
</div>

<!-- Header Khusus Print (Hanya muncul saat cetak PDF) -->
<div class="print-header">
    <h1 class="text-2xl font-bold text-gray-900 uppercase" id="printJudul">Laporan Kehadiran Siswa</h1>
    <p class="text-gray-600" id="printPeriodeText">Periode Laporan</p>
    <hr class="mt-4 border-gray-400">
</div>

<!-- Filter Panel (Tingkat Sekolah & Waktu) -->
<div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 mb-6 grid grid-cols-1 md:grid-cols-5 gap-4 items-end no-print">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Periode Laporan</label>
        <select id="filterPeriode" class="w-full border-gray-300 rounded-lg shadow-sm p-2.5 border focus:ring-blue-500" onchange="toggleFilterMode()">
            <option value="harian">Harian (Pilih Tanggal)</option>
            <option value="bulanan" selected>Bulanan</option>
        </select>
    </div>

    <!-- Filter Harian -->
    <div id="divHarian" class="hidden">
        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
        <input type="date" id="filterTanggal" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm p-2 border focus:ring-blue-500">
    </div>

    <!-- Filter Bulanan -->
    <div id="divBulanan" class="col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan & Tahun</label>
        <div class="flex space-x-2">
            <select id="filterBulan" class="w-full border-gray-300 rounded-lg shadow-sm p-2 border focus:ring-blue-500">
                @php $bulanList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                @foreach($bulanList as $i =>$b)
                    <option value="{{ $i+1 }}" {{ ($i+1) == $bulanSekarang ? 'selected' : '' }}>{{ $b }}</option>
                @endforeach
            </select>
            <input type="number" id="filterTahun" value="{{ $tahunSekarang }}" class="w-24 border-gray-300 rounded-lg shadow-sm p-2 border focus:ring-blue-500">
        </div>
    </div>

    <div class="flex gap-2 col-span-2 md:col-span-2">
        <button onclick="loadDataLaporan()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-3 rounded-lg transition shadow-sm text-sm">
            Tarik Data Terbaru
        </button>
    </div>

    <!-- Opsi Cetak PDF Global -->
    <div class="col-span-1 md:col-span-5 border-t pt-4 mt-2 flex flex-wrap items-center gap-3">
        <span class="text-sm font-bold text-gray-700">Cetak Laporan Utama:</span>
        <button onclick="cetakPDF('sekolah')" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg transition shadow-sm text-xs">
            Tingkat 1 Sekolah
        </button>
        <button onclick="cetakPDF('jurusan')" class="bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded-lg transition shadow-sm text-xs">
            Tingkat Jurusan
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- SECTION 1: SEKOLAH                         -->
<!-- ========================================== -->
<div id="sectionSekolah">
    <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2 no-print">Laporan Tingkat Sekolah</h3>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center print-break-avoid">
            <h3 class="font-bold text-gray-700 mb-4 w-full border-b pb-2 text-center">Rasio Kehadiran Global</h3>
            <div class="relative h-56 w-full flex justify-center">
                <canvas id="kehadiranChart"></canvas>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2 print-break-avoid">
            <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">Ringkasan Statistik</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-gray-500 text-sm block">Hari Efektif</span>
                    <span class="text-2xl font-bold text-blue-600" id="txtHariEfektif">0 Hari</span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-gray-500 text-sm block">Persentase Kehadiran</span>
                    <span class="text-2xl font-bold text-green-600" id="txtPersentase">0%</span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-gray-500 text-sm block">Total Hadir</span>
                    <span class="text-xl font-bold text-gray-800" id="txtHadir">0</span>
                </div>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <span class="text-gray-500 text-sm block">Total Tidak Hadir</span>
                    <span class="text-xl font-bold text-red-600" id="txtTidakHadir">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SECTION 2: JURUSAN / UNIT                  -->
<!-- ========================================== -->
<div id="sectionJurusan">
    <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2 no-print">Laporan Tingkat Jurusan</h3>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8 print-break-avoid">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs border-b">
                <tr><th class="p-4">Jurusan / Unit</th><th class="p-4 text-center">Total Hadir</th></tr>
            </thead>
            <tbody id="tabelUnit" class="divide-y divide-gray-100"></tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- SECTION 3: KELAS KHUSUS (SATU PER SATU)    -->
<!-- ========================================== -->
<div id="sectionKelas" class="pt-4 mt-8 border-t-4 border-blue-100">
    <!-- Filter Kelas Spesifik -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 no-print bg-blue-50 p-4 rounded-lg border border-blue-100">
        <div>
            <h3 class="font-bold text-blue-900 text-lg">Laporan Spesifik Per Satu Kelas</h3>
            <p class="text-blue-700 text-sm">Pilih kelas di bawah ini untuk melihat dan mencetak laporannya.</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3 items-center">
            <select id="selectSatuKelas" onchange="renderLaporanSatuKelas()" class="border-gray-300 rounded-lg p-2.5 text-sm font-bold focus:ring-blue-500 shadow-sm min-w-[200px]">
                <option value="all">-- Semua Kelas --</option>
                <!-- Opsi kelas akan dimuat dari JS -->
            </select>
            <button onclick="cetakPDF('kelas')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-sm text-sm whitespace-nowrap">
                Cetak PDF Kelas Ini
            </button>
        </div>
    </div>

    <!-- Tabel Rekap Persentase Kelas yang Dipilih -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6 print-break-avoid">
        <div class="p-3 bg-gray-50 border-b font-bold text-gray-800" id="judulTabelKelasDipilih">Rekap Persentase Kehadiran</div>
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                <tr><th class="p-3 w-1/2">Nama Kelas</th><th class="p-3 text-center">Persentase Kehadiran</th></tr>
            </thead>
            <tbody id="tabelSatuKelas" class="divide-y divide-gray-100">
                <!-- Data Diisi JS -->
            </tbody>
        </table>
    </div>

    <!-- Daftar Siswa Absen Kelas yang Dipilih -->
    <div class="p-3 bg-red-50 border border-red-100 rounded-t-xl font-bold text-red-800 text-center uppercase text-sm">
        Rincian Nama Siswa Tidak Hadir
    </div>
    <div id="containerSiswaSatuKelas" class="bg-white border-x border-b border-red-100 rounded-b-xl p-4 md:p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Rincian Nama Diisi JS -->
    </div>
</div>

<script>
    let chartInstance = null;
    let globalDataLaporan = null; // Menyimpan data tarikan server

    document.addEventListener("DOMContentLoaded", function() {
        loadDataLaporan();
    });

    function toggleFilterMode() {
        const mode = document.getElementById('filterPeriode').value;
        document.getElementById('divHarian').classList.toggle('hidden', mode !== 'harian');
        document.getElementById('divBulanan').classList.toggle('hidden', mode === 'harian');
    }

    function cetakPDF(mode) {
        document.body.className = '';
        document.body.classList.add('print-' + mode);
        
        let judul = "Laporan Kehadiran";
        if(mode === 'sekolah') judul += " (Tingkat Sekolah)";
        if(mode === 'jurusan') judul += " (Tingkat Jurusan)";
        
        // Jika mode kelas, ambil nama kelas yang sedang dipilih di dropdown
        if(mode === 'kelas') {
            const namaKls = document.getElementById('selectSatuKelas').value;
            judul += (namaKls === 'all') ? " (Seluruh Kelas)" : ` - ${namaKls}`;
        }
        
        document.getElementById('printJudul').innerText = judul;
        window.print();
    }

    function loadDataLaporan() {
        const periode = document.getElementById('filterPeriode').value;
        const bulan = document.getElementById('filterBulan').value;
        const tahun = document.getElementById('filterTahun').value;
        const tanggal = document.getElementById('filterTanggal').value;

        let teksPeriode = periode === 'harian' ? `Harian: ${tanggal}` : `Bulanan: Bulan ${bulan} Tahun ${tahun}`;
        document.getElementById('printPeriodeText').innerText = "Periode: " + teksPeriode;

        const url = `/laporan/sekolah/bulanan/${bulan}/${tahun}?periode=${periode}&tanggal=${tanggal}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                globalDataLaporan = data; // Simpan ke variabel global agar bisa difilter ulang

                // 1. Render Bagian Sekolah
                document.getElementById('txtHariEfektif').innerText = (data.hari_efektif || 0) + " Hari";
                document.getElementById('txtPersentase').innerText = data.persentase_sekolah || "0%";
                document.getElementById('txtHadir').innerText = (data.total_siswa_hadir || 0);
                const tidakHadir = (data.kapasitas_kehadiran_sekolah || 0) - (data.total_siswa_hadir || 0);
                document.getElementById('txtTidakHadir').innerText = tidakHadir;

                const ctx = document.getElementById('kehadiranChart').getContext('2d');
                if (chartInstance) chartInstance.destroy();
                chartInstance = new Chart(ctx, {
                    type: 'doughnut',
                    data: { labels: ['Hadir', 'Tidak Hadir'], datasets: [{ data: [data.total_siswa_hadir || 0, tidakHadir], backgroundColor: ['#2563eb', '#ef4444'] }] },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                });

                // 2. Render Bagian Jurusan
                const tbodyUnit = document.getElementById('tabelUnit');
                tbodyUnit.innerHTML = '';
                if(data.per_jurusan && data.per_jurusan.length > 0) {
                    data.per_jurusan.forEach(j => {
                        tbodyUnit.innerHTML += `<tr><td class="p-4 font-bold text-gray-800">${j.nama_jurusan}</td><td class="p-4 text-center font-bold text-blue-600">${j.total_hadir} Hadir</td></tr>`;
                    });
                }

                // 3. Isi Dropdown Pilih Kelas
                const selectKelas = document.getElementById('selectSatuKelas');
                // Simpan kelas yang sedang dipilih saat ini sebelum dropdown di-reset
                const currentSelection = selectKelas.value; 
                selectKelas.innerHTML = '<option value="all">-- Semua Kelas --</option>';
                
                if(data.per_kelas && data.per_kelas.length > 0) {
                    data.per_kelas.forEach(k => {
                        selectKelas.innerHTML += `<option value="${k.nama_kelas}">${k.nama_kelas}</option>`;
                    });
                }
                
                // Kembalikan pilihan sebelumnya jika kelasnya masih ada
                if(Array.from(selectKelas.options).some(opt => opt.value === currentSelection)) {
                    selectKelas.value = currentSelection;
                }

                // Langsung render tampilan kelas berdasarkan pilihan dropdown terbaru
                renderLaporanSatuKelas();
            });
    }

    // Fungsi Khusus untuk mem-filter tampilan Section Kelas berdasarkan Dropdown
    function renderLaporanSatuKelas() {
        if(!globalDataLaporan) return;
        
        const data = globalDataLaporan;
        const pilihanKelas = document.getElementById('selectSatuKelas').value;
        
        // 1. Filter Tabel Persentase Kelas
        const tbodyKelas = document.getElementById('tabelSatuKelas');
        tbodyKelas.innerHTML = ''; 
        let kelasTertentu = data.per_kelas || [];
        
        if (pilihanKelas !== 'all') {
            kelasTertentu = kelasTertentu.filter(k => k.nama_kelas === pilihanKelas);
        }

        if(kelasTertentu.length > 0) {
            kelasTertentu.forEach(k => {
                tbodyKelas.innerHTML += `<tr><td class="p-3 font-bold text-gray-800">${k.nama_kelas} <span class="font-normal text-gray-400">(${k.jurusan})</span></td><td class="p-3 text-center font-bold text-blue-600 text-lg">${k.persentase}</td></tr>`;
            });
        } else {
            tbodyKelas.innerHTML = `<tr><td colspan="2" class="p-4 text-center text-gray-500">Data kelas tidak ditemukan.</td></tr>`;
        }

        // 2. Filter Daftar Nama Siswa Absen
        const wadahSiswa = document.getElementById('containerSiswaSatuKelas');
        wadahSiswa.innerHTML = '';
        let siswaTertentu = data.siswa_tidak_masuk || [];
        
        if (pilihanKelas !== 'all') {
            siswaTertentu = siswaTertentu.filter(s => s.nama_kelas === pilihanKelas);
        }

        if(siswaTertentu.length > 0) {
            const grupPerKelas = {};
            siswaTertentu.forEach(s => {
                if(!grupPerKelas[s.nama_kelas]) grupPerKelas[s.nama_kelas] = [];
                grupPerKelas[s.nama_kelas].push(s);
            });

            for (const namaKelas in grupPerKelas) {
                let htmlTabel = `
                <div class="print-break-avoid border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                    <div class="bg-gray-100 p-2 px-4 border-b font-bold text-gray-800 text-sm">
                        Kelas: ${namaKelas}
                    </div>
                    <table class="w-full text-sm text-left">
                        <thead class="bg-white text-gray-500 text-xs border-b">
                            <tr><th class="px-3 py-2 w-1/4">Tgl</th><th class="px-3 py-2">Nama</th><th class="px-3 py-2 text-center w-1/4">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                `;
                
                grupPerKelas[namaKelas].forEach(siswa => {
                    let warna = 'text-gray-800';
                    if(siswa.status === 'Sakit') warna = 'text-yellow-600 font-bold';
                    if(siswa.status === 'Izin') warna = 'text-blue-600 font-bold';
                    if(siswa.status === 'Alfa') warna = 'text-red-600 font-bold';

                    htmlTabel += `
                        <tr>
                            <td class="px-3 py-2 text-xs">${siswa.tanggal}</td>
                            <td class="px-3 py-2 font-medium">${siswa.nama_siswa}</td>
                            <td class="px-3 py-2 text-center ${warna}">${siswa.status}</td>
                        </tr>
                    `;
                });
                htmlTabel += `</tbody></table></div>`;
                wadahSiswa.innerHTML += htmlTabel;
            }
        } else {
            wadahSiswa.innerHTML = `<div class="col-span-full text-center text-gray-500 py-6 border border-dashed rounded-lg">Seluruh siswa di kelas ini hadir / Nihil.</div>`;
        }
    }
</script>
@endsection