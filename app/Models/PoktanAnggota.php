<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoktanAnggota extends Model
{
    protected $table = 'poktan_anggotas';

    protected $fillable = [
        'poktan_id', 'user_id', 'nama', 'nik', 'nomor_kk', 'tempat_lahir', 'tanggal_lahir', 'nama_ibu_kandung',
        'no_hp', 'alamat', 'luas_lahan_ha', 'jabatan', 'status', 'dibantu_petugas',
        'file_ktp', 'file_kk', 'file_sppt',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'dibantu_petugas' => 'boolean',
    ];

    public function poktan()
    {
        return $this->belongsTo(Poktan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}