<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_solars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('poktan_id')->nullable()->constrained('poktans')->nullOnDelete();

            // Data pemohon
            $table->string('nama_pemohon')->nullable();
            $table->string('nik', 16)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('status_konsumen')->nullable();
            $table->string('jenis_usaha')->nullable();
            $table->string('nama_kapal')->nullable();

            // Data alat/mesin
            $table->string('jenis_alat_mesin')->nullable();
            $table->string('fungsi_alat_mesin')->nullable();
            $table->unsignedInteger('jumlah_alat_mesin')->default(1);
            $table->string('daya_alat_mesin')->nullable();
            $table->string('lama_penggunaan')->nullable();
            $table->string('lama_operasi')->nullable();

            // Volume BBM
            $table->decimal('usulan_volume_konsumsi', 10, 2)->nullable();
            $table->string('volume_periode')->nullable();
            $table->decimal('estimasi_sisa_liter', 10, 2)->nullable();

            // Berkas
            $table->string('file_ktp')->nullable();
            $table->string('file_sku')->nullable();
            $table->string('file_foto_mesin')->nullable();

            // Status & proses admin
            $table->string('status')->default('diajukan'); // diajukan|diproses|diterima|ditolak
            $table->text('catatan_admin')->nullable();
            $table->string('nomor_surat')->nullable()->unique();
            $table->string('file_surat_resmi')->nullable();

            // SPK
            $table->unsignedTinyInteger('spk_skor')->nullable();
            $table->string('spk_label')->nullable();

            // Persetujuan
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_at')->nullable();

            // Field lama (legacy, dibiarkan nullable untuk jaga-jaga)
            $table->string('nama_kelompok_tani')->nullable();
            $table->decimal('luas_lahan', 10, 2)->nullable();
            $table->integer('jumlah_liter_diajukan')->nullable();
            $table->text('keperluan')->nullable();
            $table->string('file_pendukung')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_solars');
    }
};