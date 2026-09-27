<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pengajuan_solars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poktan_id')->nullable()->constrained('poktans')->nullOnDelete();

            $table->string('nama_pemohon');
            $table->string('nik', 16);
            $table->text('alamat');
            $table->string('no_hp', 20);
            $table->string('jenis_alat_mesin');

            // upload dokumen — simpan path di storage
            $table->string('file_ktp');
            $table->string('file_kk');
            $table->string('file_surat_keterangan_usaha');
            $table->string('file_bukti_kepemilikan_alat');
            $table->string('file_surat_pernyataan_bbm');
            $table->string('file_dokumentasi_alat')->nullable();

            $table->text('keterangan_tambahan')->nullable();

            $table->enum('status', ['diajukan', 'diverifikasi', 'disetujui', 'ditolak'])
                  ->default('diajukan');
            $table->text('catatan_admin')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_solars');
    }
};
