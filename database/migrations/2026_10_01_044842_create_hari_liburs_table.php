<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_liburs', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_libur')->unique(); // Satu tanggal hanya boleh satu entri libur
            $table->string('keterangan'); // Contoh: "Libur Semester", "Idul Fitri"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_liburs');
    }
};
