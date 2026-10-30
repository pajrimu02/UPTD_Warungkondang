<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoktanAnggota extends Model
{
    protected $table = 'poktan_anggotas';

    protected $fillable = [
    'poktan_id', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'nama_ibu_kandung',
    'no_hp', 'alamat', 'luas_lahan_ha', 'jabatan', 'file_ktp', 'file_kk', 'file_sppt',
    ];
    protected $casts = ['tanggal_lahir' => 'date'];

    public function poktan()
    {
        return $this->belongsTo(Poktan::class);
    }
}