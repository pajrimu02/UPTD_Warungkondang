<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poktan extends Model
{
    protected $fillable = [
        'nama_kelompok', 'nama_ketua', 'jumlah_anggota', 'luas_lahan_ha', 'alamat',
        'desa_id', 'status', 'nomor_sk', 'file_sk_resmi', 'catatan_admin',
        'diajukan_oleh', 'disetujui_oleh', 'disetujui_at',
        'ttd_dikirim_at', 'ttd_kembali_at', 'diserahkan_at',
        'kode_undangan', 'dikunci_at',
    ];

    protected $casts = [
        'disetujui_at' => 'datetime',
        'ttd_dikirim_at' => 'datetime',
        'ttd_kembali_at' => 'datetime',
        'diserahkan_at' => 'datetime',
        'dikunci_at' => 'datetime',
    ];

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

    public function disetujuiOleh()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
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

    public static function generateNomorSK(): string
    {
        $romawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $urutan = self::whereNotNull('nomor_sk')
            ->whereYear('disetujui_at', now()->year)
            ->whereMonth('disetujui_at', now()->month)
            ->count() + 1;

        return str_pad($urutan, 3, '0', STR_PAD_LEFT) . '/SK-POKTAN/UPTD-WRK/' . $romawi[now()->month] . '/' . now()->year;
    }
}