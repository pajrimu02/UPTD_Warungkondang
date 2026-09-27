<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poktan extends Model
{
    protected $fillable = ['nama_kelompok', 'nama_ketua', 'jumlah_anggota', 'luas_lahan_ha', 'alamat'];

    public function pengajuanSolar()
    {
        return $this->hasMany(PengajuanSolar::class);
    }
}
