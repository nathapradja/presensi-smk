<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Siswa;
use Carbon\Carbon;

class ReportController extends Controller
{
    // 1. Laporan Harian Kelas (JSON)
    public function harianKelas($kelas_id, $tanggal)
    {
        $absensi = Absensi::with('siswa')
            ->whereHas('siswa', function($q) use ($kelas_id) {
                $q->where('kelas_id', $kelas_id);
            })
            ->whereDate('tanggal', $tanggal)
            ->get();

        return response()->json([
            'kelas_id' => $kelas_id,
            'tanggal' => $tanggal,
            'data_absensi' => $absensi
        ]);
    }

    // 2. Laporan Bulanan Individu Siswa
    public function individuBulan($siswa_id, $bulan, $tahun)
    {
        $absensi = Absensi::where('siswa_id', $siswa_id)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->get();

        return response()->json([
            'siswa_id' => $siswa_id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'riwayat' => $absensi
        ]);
    }

    // 3. Laporan Rekap Sekolah & Detail (Versi Tahan Banting)
    public function sekolahBulan(Request $request, $bulan, $tahun)
    {
        $periode = $request->get('periode', 'bulanan');
        $tanggalInput = $request->get('tanggal', date('Y-m-d'));

        // Query Absensi dasar langsung tarik semua relasi yang dibutuhkan
        $query = Absensi::with(['siswa.kelas.jurusan']);

        if ($periode === 'harian') {
            $query->whereDate('tanggal', $tanggalInput);
            $hariEfektif = 1;
        } else {
            $query->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan);
            $hariEfektif = 20; // Asumsi 20 hari belajar efektif sebulan
        }

        $semuaAbsensi = $query->get();
        $totalSiswaAktif = Siswa::count();
        
        $kapasitasTotal = $totalSiswaAktif * $hariEfektif;
        $totalHadir = $semuaAbsensi->where('status_kehadiran', 'Hadir')->count();
        
        $persentaseSekolah = $kapasitasTotal > 0 ? round(($totalHadir / $kapasitasTotal) * 100, 1) : 0;

        // 1. Ambil Siswa Tidak Masuk
        $siswaTidakMasuk = $semuaAbsensi->where('status_kehadiran', '!=', 'Hadir')->map(function($item) {
            return [
                'tanggal' => $item->tanggal,
                'nama_siswa' => $item->siswa->nama_siswa ?? 'Siswa Dihapus',
                'nama_kelas' => $item->siswa->kelas->nama_kelas ?? 'Tanpa Kelas',
                'status' => $item->status_kehadiran
            ];
        })->values();

        // 2. Rekap Per Kelas (Filter manual agar tidak error relasi)
        $kelasDb = Kelas::with('jurusan')->get();
        $rekapKelas = [];
        
        foreach($kelasDb as $k) {
            $jumlahSiswaDiKelas = Siswa::where('kelas_id', $k->id)->count();
            
            // Cari data absen dari koleksi $semuaAbsensi yang id kelasnya cocok
            $absenKelasIni = $semuaAbsensi->filter(function($a) use ($k) {
                return $a->siswa && $a->siswa->kelas_id == $k->id;
            });
            
            $hadirKelas = $absenKelasIni->where('status_kehadiran', 'Hadir')->count();
            $kapasitasKelas = $jumlahSiswaDiKelas > 0 ? ($jumlahSiswaDiKelas * $hariEfektif) : 1;
            $persen = round(($hadirKelas / max($kapasitasKelas, 1)) * 100);

            $rekapKelas[] = [
                'nama_kelas' => $k->nama_kelas,
                'jurusan' => $k->jurusan->nama_jurusan ?? '-',
                'persentase' => $persen . '%'
            ];
        }

        // 3. Rekap Per Jurusan
        $jurusanDb = Jurusan::all();
        $rekapJurusan = [];
        
        foreach($jurusanDb as $j) {
            // Cari data absen dari koleksi $semuaAbsensi yang id jurusannya cocok
            $absenJurusanIni = $semuaAbsensi->filter(function($a) use ($j) {
                return $a->siswa && $a->siswa->kelas && $a->siswa->kelas->jurusan_id == $j->id;
            });
            
            $hadirJurusan = $absenJurusanIni->where('status_kehadiran', 'Hadir')->count();
            
            $rekapJurusan[] = [
                'nama_jurusan' => $j->nama_jurusan,
                'total_hadir' => $hadirJurusan
            ];
        }

        return response()->json([
            'hari_efektif' => $hariEfektif,
            'kapasitas_kehadiran_sekolah' => $kapasitasTotal,
            'total_siswa_hadir' => $totalHadir,
            'persentase_sekolah' => $persentaseSekolah . '%',
            'siswa_tidak_masuk' => $siswaTidakMasuk,
            'per_kelas' => $rekapKelas,
            'per_jurusan' => $rekapJurusan
        ]);
    }
}