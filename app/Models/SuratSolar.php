<?php
// app/Models/SuratSolar.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratSolar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_kelompok_tani',
        'nik',
        'alamat',
        'luas_lahan',
        'jumlah_liter_diajukan',
        'keperluan',
        'status',
        'catatan_admin',
        'nomor_surat',
        'file_pendukung',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}