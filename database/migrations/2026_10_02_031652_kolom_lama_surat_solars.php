<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $kolomLama = [
            'luas_lahan' => 'DECIMAL(10,2) NULL',
            'jumlah_liter_diajukan' => 'INT NULL',
            'keperluan' => 'TEXT NULL',
            'nomor_surat' => 'VARCHAR(255) NULL',
            'file_pendukung' => 'VARCHAR(255) NULL',
            'nama_kelompok_tani' => 'VARCHAR(255) NULL',
        ];

        foreach ($kolomLama as $kolom => $tipe) {
            if (Schema::hasColumn('surat_solars', $kolom)) {
                DB::statement("ALTER TABLE surat_solars MODIFY `$kolom` $tipe");
            }
        }
    }

    public function down(): void
    {
        //
    }
};