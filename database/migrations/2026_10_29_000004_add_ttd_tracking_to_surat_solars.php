<?php
// 2026_10_29_000004_add_ttd_tracking_to_surat_solars.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_solars', function (Blueprint $t) {
            $t->timestamp('ttd_dikirim_at')->nullable();  // surat dikirim ke ketua
            $t->timestamp('ttd_kembali_at')->nullable();  // surat bertanda tangan kembali ke admin
            $t->timestamp('diserahkan_at')->nullable();   // surat diserahkan ke petani
        });
    }
    public function down(): void
    {
        Schema::table('surat_solars', fn (Blueprint $t) => $t->dropColumn(['ttd_dikirim_at', 'ttd_kembali_at', 'diserahkan_at']));
    }
};