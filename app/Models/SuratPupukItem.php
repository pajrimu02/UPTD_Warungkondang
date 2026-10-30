<?php
// app/Models/SuratPupukItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPupukItem extends Model
{
    protected $fillable = [
        'surat_pupuk_id', 'poktan_anggota_id', 'nama_petani', 'nik', 'tempat_lahir',
        'tanggal_lahir', 'nama_ibu_kandung', 'alamat', 'subsektor', 'musim',
    ];

    protected $casts = ['musim' => 'array', 'tanggal_lahir' => 'date'];

    public function suratPupuk(): BelongsTo { return $this->belongsTo(SuratPupuk::class); }
}