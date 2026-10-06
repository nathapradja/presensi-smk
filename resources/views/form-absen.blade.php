@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 pb-4 border-b">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Form Absensi Harian</h2>
            <p class="text-gray-500">Kelas: <strong class="text-blue-600">{{ $nama_kelas }}</strong></p>
        </div>
        <div class="mt-4 md:mt-0 text-right">
            <span class="bg-blue-100 text-blue-800 text-sm font-semibold px-4 py-2 rounded-lg">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <form action="/simpan-absen" method="POST">
        @csrf

        <div class="overflow-x-auto rounded-lg border border-gray-200 mb-6">
            <table class="min-w-full bg-white text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 uppercase font-medium border-b">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4 text-center min-w-[300px]">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($siswa as $index => $s)
                    <tr class="hover:bg-blue-50 transition duration-150">
                        <td class="py-3 px-4 text-center text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 font-medium text-gray-800">{{ $s->nama_siswa }}</td>
                        <td class="py-3 px-4">
                            <!-- Radio Button Group (Hadir otomatis checked) -->
                            <div class="flex justify-center space-x-3 md:space-x-6">
                                <label class="cursor-pointer flex items-center space-x-1 text-green-600">
                                    <input type="radio" name="absensi[{{ $s->id }}]" value="Hadir" class="w-4 h-4 text-green-600 focus:ring-green-500 cursor-pointer" checked>
                                    <span class="font-medium">Hadir</span>
                                </label>
                                <label class="cursor-pointer flex items-center space-x-1 text-yellow-500">
                                    <input type="radio" name="absensi[{{ $s->id }}]" value="Sakit" class="w-4 h-4 text-yellow-500 focus:ring-yellow-500 cursor-pointer">
                                    <span class="font-medium">Sakit</span>
                                </label>
                                <label class="cursor-pointer flex items-center space-x-1 text-blue-500">
                                    <input type="radio" name="absensi[{{ $s->id }}]" value="Izin" class="w-4 h-4 text-blue-500 focus:ring-blue-500 cursor-pointer">
                                    <span class="font-medium">Izin</span>
                                </label>
                                <label class="cursor-pointer flex items-center space-x-1 text-red-500">
                                    <input type="radio" name="absensi[{{ $s->id }}]" value="Alfa" class="w-4 h-4 text-red-500 focus:ring-red-500 cursor-pointer">
                                    <span class="font-medium">Alfa</span>
                                </label>
                                <label class="cursor-pointer flex items-center space-x-1 text-purple-500">
                                    <input type="radio" name="absensi[{{ $s->id }}]" value="Dispensasi" class="w-4 h-4 text-purple-500 focus:ring-purple-500 cursor-pointer">
                                    <span class="font-medium">Disp.</span>
                                </label>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition">
                Simpan Absensi
            </button>
        </div>
    </form>

</div>
@endsection