<?php
// 2026_10_29_000003_create_surat_pupuks.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_pupuks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('poktan_id')->constrained('poktans')->cascadeOnDelete();
            $t->string('nama_penyuluh');
            $t->string('kode_desa', 20)->nullable();
            $t->string('kode_kios')->nullable();
            $t->string('nama_kios')->nullable();
            $t->string('gapoktan')->nullable();
            $t->string('status', 20)->default('diajukan'); // diajukan|diproses|diterima|ditolak
            $t->text('catatan_admin')->nullable();
            $t->string('nomor_surat')->nullable()->unique();
            $t->string('file_surat_resmi')->nullable();
            $t->foreignId('disetujui_oleh')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamp('disetujui_at')->nullable();
            $t->timestamps();
        });

        Schema::create('surat_pupuk_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('surat_pupuk_id')->constrained('surat_pupuks')->cascadeOnDelete();
            $t->foreignId('poktan_anggota_id')->nullable()->constrained('poktan_anggotas')->nullOnDelete();
            $t->string('nama_petani');
            $t->string('nik', 16);
            $t->string('tempat_lahir');
            $t->date('tanggal_lahir');
            $t->string('nama_ibu_kandung');
            $t->text('alamat');
            $t->string('subsektor');
            $t->json('musim'); // 3 elemen MT1..MT3
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('surat_pupuk_items');
        Schema::dropIfExists('surat_pupuks');
    }
};