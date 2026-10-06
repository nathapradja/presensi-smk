@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard & Data Master</h2>
    <a href="/admin/logout" class="bg-red-50 text-red-600 px-4 py-2 rounded font-medium hover:bg-red-100 transition">Logout</a>
</div>

<!-- Card Statistik -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Kelas</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2">{{ count($kelas) }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Siswa</h3>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ $totalSiswa }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-gray-500 text-sm font-semibold uppercase">Total Jurusan</h3>
        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalJurusan }}</p>
    </div>
</div>

<!-- Notifikasi Sukses -->
@if(session('success'))
<div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm font-medium border border-green-200 mb-6 shadow-sm">
    {{ session('success') }}
</div>
@endif

<!-- Form Tambah Kelas Baru -->
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
    <h3 class="font-bold text-gray-800 mb-4 pb-2 border-b">Tambah Kelas Baru</h3>

    <form action="/admin/kelas/store" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
            <input type="text" name="nama_kelas" placeholder="Contoh: XI PPLG 3" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
            <select name="jurusan_id" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">-- Pilih Jurusan --</option>
                @foreach(\App\Models\Jurusan::all() as $j)
                    <option value="{{ $j->id }}">{{ $j->nama_jurusan }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">PIN Kelas (4 Digit)</label>
            <input type="text" name="pin_kelas" maxlength="4" placeholder="1234" class="w-full border border-gray-300 rounded-lg p-2.5 text-center tracking-widest font-bold focus:ring-blue-500 focus:border-blue-500" required pattern="\d{4}">
        </div>
        <div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-sm">
                Simpan Kelas
            </button>
        </div>
    </form>
</div>

<!-- Tabel Manajemen Kelas & PIN -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Manajemen PIN Kelas</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3">Jurusan</th>
                    <th class="px-6 py-3">Nama Kelas</th>
                    <th class="px-6 py-3">Reset PIN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($kelas as $k)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium">{{ $k->jurusan->nama_jurusan }}</td>
                    <td class="px-6 py-4">{{ $k->nama_kelas }}</td>
                    <td class="px-6 py-4">
                        <form action="/admin/kelas/{{ $k->id }}/reset-pin" method="POST" class="flex items-center space-x-2">
                            @csrf
                            <input type="text" name="pin_baru" maxlength="4" placeholder="PIN Baru (4 Digit)" class="border border-gray-300 rounded px-3 py-1.5 w-36 focus:ring-blue-500 focus:border-blue-500" required pattern="\d{4}">
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded text-xs font-bold transition">
                                Update PIN
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection