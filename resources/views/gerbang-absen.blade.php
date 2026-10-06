@extends('layouts.app')

@section('title', 'Gerbang Absensi')

@section('content')
<div class="flex items-center justify-center min-h-[70vh]">
    <div class="bg-white p-6 md:p-10 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        
        <div class="text-center mb-8">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Absensi Harian</h1>
            <p class="text-gray-500 text-sm mt-1">Pilih kelas yang Anda ampu hari ini</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg" role="alert">
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Alert Error Jika PIN Salah -->
        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
            <p class="font-medium text-sm">{{ session('error') }}</p>
        </div>
        @endif

        <form action="/verify-pin" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kelas</label>
                <select name="kelas_id" class="w-full border-gray-300 rounded-lg shadow-sm p-3.5 bg-gray-50 border focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                    <option value="">-- Ketuk untuk memilih kelas --</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">PIN Keamanan</label>
                <input type="password" name="pin" maxlength="4" pattern="\d{4}" class="w-full border-gray-300 rounded-lg shadow-sm p-3.5 bg-gray-50 border focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center tracking-[1em] text-xl font-bold outline-none transition" placeholder="••••" required>
                <p class="text-xs text-gray-400 mt-2 text-center">Masukkan 4 digit angka PIN kelas</p>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-4 rounded-lg shadow-md hover:shadow-lg transform transition active:scale-95">
                Buka Daftar Hadir
            </button>
        </form>

    </div>
</div>
@endsection