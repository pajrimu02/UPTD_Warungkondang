<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poktan extends Model
{
    protected $fillable = [
    'nama_kelompok', 'nama_ketua', 'jumlah_anggota', 'luas_lahan_ha', 'alamat',
    'desa_id', 'status', 'catatan_admin', 'diajukan_oleh', 'disetujui_at',
    'kode_undangan', 'dikunci_at',
    ];

    protected $casts = ['disetujui_at' => 'datetime', 'dikunci_at' => 'datetime'];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function anggota()
    {
        return $this->hasMany(PoktanAnggota::class);
    }

    public function pengaju()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function suratSolars()
    {
        return $this->hasMany(SuratSolar::class);
    }
    public function suratPupuks() 
    { 
        return $this->hasMany(SuratPupuk::class); 
    }

    public static function buatKode(): string
    {
        $huruf = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';  

        do {
            $kode = '';
            for ($i = 0; $i < 6; $i++) {
                $kode .= $huruf[random_int(0, strlen($huruf) - 1)];
            }
        } while (static::where('kode_undangan', $kode)->exists());

        return $kode;
    }
}