<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    // Hati-hati dengan nama tabel, Laravel mungkin mengira jamaknya "kelas", kita pastikan nama tabelnya.
    protected $table = 'kelas'; 
    protected $fillable = ['jurusan_id', 'nama_kelas', 'pin_kelas'];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }
}
