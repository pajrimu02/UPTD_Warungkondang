<?php
 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_solars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('nama_kelompok_tani')->nullable();
            $table->string('nik', 16);
            $table->string('alamat');
            $table->decimal('luas_lahan', 8, 2)->comment('dalam hektar');
            $table->unsignedInteger('jumlah_liter_diajukan');
            $table->text('keperluan');

            $table->enum('status', ['pending', 'diproses', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('file_pendukung')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_solars');
    }
};