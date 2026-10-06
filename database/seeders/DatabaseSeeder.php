<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\HariLibur;
use App\Models\Absensi;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Menggunakan Faker dengan lokal Indonesia untuk nama siswa
        $faker = Faker::create('id_ID');

        // 1. Buat 2 Jurusan
        $jurusanPPLG = Jurusan::create(['nama_jurusan' => 'PPLG']);
        $jurusanTKJ = Jurusan::create(['nama_jurusan' => 'TKJ']);

        // 2. Buat 4 Kelas (PIN disetel seragam: 1234)
        $dataKelas = [
            ['jurusan_id' => $jurusanPPLG->id, 'nama_kelas' => 'XI PPLG 1', 'pin_kelas' => Hash::make('1234')],
            ['jurusan_id' => $jurusanPPLG->id, 'nama_kelas' => 'XI PPLG 2', 'pin_kelas' => Hash::make('1234')],
            ['jurusan_id' => $jurusanTKJ->id, 'nama_kelas' => 'XI TKJ 1', 'pin_kelas' => Hash::make('1234')],
            ['jurusan_id' => $jurusanTKJ->id, 'nama_kelas' => 'XI TKJ 2', 'pin_kelas' => Hash::make('1234')],
        ];

        $kelasIds = [];
        foreach ($dataKelas as $dk) {
            $kelas = Kelas::create($dk);
            $kelasIds[] = $kelas->id;
            
            // 3. Buat 25 Siswa untuk masing-masing kelas (Total 100 Siswa)
            for ($i = 1; $i <= 25; $i++) {
                Siswa::create([
                    'kelas_id' => $kelas->id,
                    'nis' => $faker->unique()->numerify('10###'), // Contoh NIS: 10452
                    'nama_siswa' => $faker->name(),
                ]);
            }
        }

        // 4. Buat Hari Libur di bulan September (Sebagai contoh bulan lalu)
        HariLibur::create([
            'tanggal_libur' => '2026-09-16', 
            'keterangan' => 'Maulid Nabi'
        ]);
        HariLibur::create([
            'tanggal_libur' => '2026-09-28', 
            'keterangan' => 'Rapat Kerja Guru'
        ]);
        $tanggalLibur = ['2026-09-16', '2026-09-28'];

        // 5. Buat Data Absensi (1 Bulan Terakhir: 1 Sept - 30 Sept)
        $siswas = Siswa::all();
        $startDate = Carbon::create(2026, 9, 1);
        $endDate = Carbon::create(2026, 9, 30);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Skip hari Sabtu dan Minggu
            if ($date->isWeekend()) {
                continue;
            }

            // Skip jika tanggal tersebut ada di array hari libur
            if (in_array($date->format('Y-m-d'), $tanggalLibur)) {
                continue;
            }

            // Generate absen untuk semua siswa pada tanggal aktif ini
            foreach ($siswas as $siswa) {
                // Logic probabilitas: 90% Hadir, 10% Acak (Sakit, Izin, Alfa, Dispensasi)
                $rand = rand(1, 100);
                if ($rand <= 90) {
                    $status = 'Hadir';
                } elseif ($rand <= 93) {
                    $status = 'Sakit';
                } elseif ($rand <= 96) {
                    $status = 'Izin';
                } elseif ($rand <= 98) {
                    $status = 'Alfa';
                } else {
                    $status = 'Dispensasi';
                }

                Absensi::create([
                    'siswa_id' => $siswa->id,
                    'tanggal' => $date->format('Y-m-d'),
                    'status_kehadiran' => $status,
                ]);
            }
        }
    }
}