<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('poktans', function (Blueprint $table) {
            $table->string('nomor_sk')->nullable()->unique()->after('status');
            $table->string('file_sk_resmi')->nullable()->after('nomor_sk');
            $table->foreignId('disetujui_oleh')->nullable()->after('diajukan_oleh')->constrained('users')->nullOnDelete();
            $table->timestamp('ttd_dikirim_at')->nullable()->after('disetujui_at');
            $table->timestamp('ttd_kembali_at')->nullable()->after('ttd_dikirim_at');
            $table->timestamp('diserahkan_at')->nullable()->after('ttd_kembali_at');
        });
    }

    public function down(): void
    {
        Schema::table('poktans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh');
            $table->dropColumn([
                'nomor_sk', 'file_sk_resmi',
                'ttd_dikirim_at', 'ttd_kembali_at', 'diserahkan_at',
            ]);
        });
    }
};