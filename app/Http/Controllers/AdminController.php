<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\HariLibur;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // 1. Tampilkan Halaman Login
    public function loginForm()
    {
        return view('admin.login');
    }

    // 2. Proses Login Admin
    public function authenticate(Request $request)
    {
        // Hardcode admin kredensial untuk mempermudah testing tanpa tabel Users
        if ($request->username === 'admin' && $request->password === 'rahasia123') {
            session(['admin_logged_in' => true]);
            return redirect('/admin/dashboard');
        }

        return back()->with('error', 'Username atau Password salah!');
    }

    // 3. Proses Logout
    public function logout()
    {
        session()->forget('admin_logged_in');
        return redirect('/admin/login');
    }

    // 4. Tampilkan Dashboard & Tabel Data
    public function dashboard()
    {
        // Proteksi Halaman
        if (!session('admin_logged_in')) {
            return redirect('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil data untuk ditampilkan di tabel
        $kelas = Kelas::with('jurusan')->get();
        $totalSiswa = Siswa::count();
        $totalJurusan = Jurusan::count();

        // Mengirim isAdmin = true agar sidebar muncul
        return view('admin.dashboard', compact('kelas', 'totalSiswa', 'totalJurusan'))->with('isAdmin', true);
    }

    // 5. Proses Reset PIN Kelas
    public function resetPinKelas(Request $request, $id)
    {
        $request->validate(['pin_baru' => 'required|digits:4']);
        
        $kelas = Kelas::findOrFail($id);
        $kelas->update([
            'pin_kelas' => Hash::make($request->pin_baru)
        ]);

        return back()->with('success', "PIN Kelas {$kelas->nama_kelas} berhasil direset!");
    }

    // ==========================================
    // TAMBAHAN FASE 2.5 (Halaman Laporan)
    // ==========================================
    public function halamanLaporan()
    {
        // Proteksi Halaman
        if (!session('admin_logged_in')) {
            return redirect('/admin/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $bulanSekarang = date('n'); // Format 1-12
        $tahunSekarang = date('Y');

        return view('admin.laporan', compact('bulanSekarang', 'tahunSekarang'))->with('isAdmin', true);
    }

        public function storeKelas(Request $request)
    {
        // Validasi input form tambah kelas
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'jurusan_id' => 'required|exists:jurusans,id',
            'pin_kelas' => 'required|digits:4'
        ]);

        // Simpan ke database dengan PIN yang di-hash
        Kelas::create([
            'nama_kelas' => $request->nama_kelas,
            'jurusan_id' => $request->jurusan_id,
            'pin_kelas' => Hash::make($request->pin_kelas)
        ]);

        return back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }
    
}