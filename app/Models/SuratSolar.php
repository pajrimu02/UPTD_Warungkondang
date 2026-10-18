<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratSolar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'poktan_id', 'nama_pemohon', 'nik', 'alamat', 'no_hp',
        'status_konsumen', 'jenis_usaha', 'nama_kapal',
        'jenis_alat_mesin', 'fungsi_alat_mesin', 'jumlah_alat_mesin', 'daya_alat_mesin',
        'lama_penggunaan', 'lama_operasi',
        'usulan_volume_konsumsi', 'volume_periode', 'estimasi_sisa_liter',
        'file_ktp', 'file_sku', 'file_foto_mesin',
        'status', 'catatan_admin', 'nomor_surat', 'file_surat_resmi',
        'spk_skor', 'spk_label',
        'disetujui_oleh', 'disetujui_at',
    ];

    protected $casts = [
        'disetujui_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function poktan(): BelongsTo
    {
        return $this->belongsTo(Poktan::class);
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public static function hitungSkorKelayakan(int $userId, array $fileFields): array
{
    $total = count($fileFields);
    $terisi = count(array_filter($fileFields));
    $skorDokumen = $total > 0 ? ($terisi / $total) * 100 : 0;

    $jumlahDiterima = self::where('user_id', $userId)->where('status', 'diterima')->count();
    $skorRiwayat = match (true) {
        $jumlahDiterima === 0 => 100,
        $jumlahDiterima === 1 => 70,
        $jumlahDiterima === 2 => 40,
        default => 10,
    };

    $totalSkor = (int) round(($skorDokumen * 0.4) + ($skorRiwayat * 0.6));

    $label = match (true) {
        $totalSkor >= 80 => 'layak',
        $totalSkor >= 50 => 'perlu_ditinjau',
        default => 'tidak_layak',
    };

    return ['skor' => $totalSkor, 'label' => $label];
}

/**
 * Nomor surat otomatis, urut per bulan. Format: 001/REK-SOLAR/UPTD-WRK/X/2026
 */
public static function generateNomorSurat(): string
{
    $bulanRomawi = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'];
    $bulan = $bulanRomawi[now()->month];
    $tahun = now()->year;

    $urutan = self::whereNotNull('nomor_surat')
        ->whereYear('disetujui_at', $tahun)
        ->whereMonth('disetujui_at', now()->month)
        ->count() + 1;

    $nomorUrut = str_pad($urutan, 3, '0', STR_PAD_LEFT);

    return "{$nomorUrut}/REK-SOLAR/UPTD-WRK/{$bulan}/{$tahun}";
}
}