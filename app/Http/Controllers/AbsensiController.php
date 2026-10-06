<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // 1. Menampilkan halaman gerbang absen (Pilih Kelas & PIN)
    public function index()
    {
        $kelas = Kelas::all();
        
        // Mengirim isAdmin = false agar komponen sidebar tidak muncul di halaman guru
        return view('gerbang-absen', [
            'kelas' => $kelas,
            'isAdmin' => false 
        ]);
    }

    // 2. Memproses form verifikasi PIN
    public function verifyPinWeb(Request $request)
    {
        // Validasi input form
        $request->validate([
            'kelas_id' => 'required',
            'pin' => 'required|digits:4'
        ]);

        $kelas = Kelas::findOrFail($request->kelas_id);

        // Cek kesesuaian PIN yang diketik dengan di database
        if (Hash::check($request->pin, $kelas->pin_kelas)) {
            
            // Simpan sesi login kelas (agar tidak sembarang orang bisa buka URL form-absen)
            session([
                'kelas_id' => $kelas->id, 
                'nama_kelas' => $kelas->nama_kelas
            ]);
            
            // Lemparkan ke form tabel daftar hadir
            return redirect('/form-absen');
        }

        // Jika salah, tendang kembali ke depan beserta notifikasi error
        return back()->with('error', 'PIN yang Anda masukkan salah!');
    }

    // 3. Menampilkan form daftar siswa untuk dicentang
    public function formAbsen()
    {
        // Keamanan: Tendang ke depan jika mencoba buka URL ini tanpa login PIN
        if (!session()->has('kelas_id')) {
            return redirect('/')->with('error', 'Silakan pilih kelas dan masukkan PIN terlebih dahulu.');
        }

        $kelas_id = session('kelas_id');
        $nama_kelas = session('nama_kelas');
        
        // Ambil data siswa sesuai kelas yang login, urutkan berdasarkan nama (abjad)
        $siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama_siswa', 'asc')->get();
        
        return view('form-absen', compact('siswa', 'nama_kelas'))->with('isAdmin', false);
    }

    // 4. Memproses penyimpanan data absensi ke Database
    public function storeWeb(Request $request)
    {
        // Ambil tanggal hari ini berjalan
        $tanggal = Carbon::today()->format('Y-m-d');
        $dataAbsensi = $request->absensi; // Data dari radio button

        // Looping untuk menyimpan status absen setiap anak (updateOrCreate mencegah double data)
        if ($dataAbsensi) {
            foreach ($dataAbsensi as $siswa_id => $status) {
                Absensi::updateOrCreate(
                    ['siswa_id' => $siswa_id, 'tanggal' => $tanggal],
                    ['status_kehadiran' => $status]
                );
            }
        }

        // Hapus sesi agar halaman terkunci kembali setelah guru selesai absen
        session()->forget(['kelas_id', 'nama_kelas']);

        // Arahkan kembali ke halaman awal dengan pesan sukses
        return redirect('/')->with('success', 'Data absensi harian berhasil disimpan!');
    }
}