<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->cascadeOnDelete();
            
            $table->date('tanggal');
            
            // Enum sesuai BRD, dengan default Hadir untuk mempercepat proses input guru
            $table->enum('status_kehadiran', ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Dispensasi'])
                  ->default('Hadir');
                  
            $table->timestamps();

            // Proteksi database: 1 Siswa hanya boleh punya 1 record absensi di tanggal yang sama
            $table->unique(['siswa_id', 'tanggal']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
