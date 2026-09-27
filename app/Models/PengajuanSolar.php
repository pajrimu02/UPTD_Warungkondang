<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanSolar extends Model
{
    protected $fillable = [
        'user_id', 'poktan_id', 'nama_pemohon', 'nik', 'alamat', 'no_hp', 'jenis_alat_mesin',
        'file_ktp', 'file_kk', 'file_surat_keterangan_usaha', 'file_bukti_kepemilikan_alat',
        'file_surat_pernyataan_bbm', 'file_dokumentasi_alat', 'keterangan_tambahan',
        'status', 'catatan_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function poktan()
    {
        return $this->belongsTo(Poktan::class);
    }
}
