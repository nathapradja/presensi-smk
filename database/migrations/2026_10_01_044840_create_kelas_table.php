<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel jurusans
            $table->foreignId('jurusan_id')->constrained('jurusans')->cascadeOnDelete();
            
            $table->string('nama_kelas'); // Contoh: XI PPLG 1
            
            // Disiapkan panjang untuk hash (bcrypt di Laravel menghasilkan 60 karakter)
            $table->string('pin_kelas'); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};