<?php
// app/Models/SuratPupuk.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuratPupuk extends Model
{
    protected $fillable = [
        'user_id', 'poktan_id', 'nama_penyuluh', 'kode_desa', 'kode_kios', 'nama_kios', 'gapoktan',
        'status', 'catatan_admin', 'nomor_surat', 'file_surat_resmi', 'disetujui_oleh', 'disetujui_at',
    ];

    protected $casts = ['disetujui_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function poktan(): BelongsTo { return $this->belongsTo(Poktan::class); }
    public function disetujuiOleh(): BelongsTo { return $this->belongsTo(User::class, 'disetujui_oleh'); }
    public function items(): HasMany { return $this->hasMany(SuratPupukItem::class); }

    public static function generateNomorSurat(): string
    {
        $romawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];

        $urutan = self::whereNotNull('nomor_surat')
            ->whereYear('disetujui_at', now()->year)
            ->whereMonth('disetujui_at', now()->month)
            ->count() + 1;

        return str_pad($urutan, 3, '0', STR_PAD_LEFT) . '/REK-PUPUK/UPTD-WRK/' . $romawi[now()->month] . '/' . now()->year;
    }
    
}