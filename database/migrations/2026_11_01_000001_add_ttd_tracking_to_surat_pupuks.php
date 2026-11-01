<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_pupuks', function (Blueprint $t) {
            $t->timestamp('ttd_dikirim_at')->nullable();
            $t->timestamp('ttd_kembali_at')->nullable();
            $t->timestamp('diserahkan_at')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('surat_pupuks', fn (Blueprint $t) => $t->dropColumn(['ttd_dikirim_at', 'ttd_kembali_at', 'diserahkan_at']));
    }
};