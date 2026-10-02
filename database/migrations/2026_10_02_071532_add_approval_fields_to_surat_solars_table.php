<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_solars', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_solars', 'disetujui_oleh')) {
                $table->foreignId('disetujui_oleh')->nullable()->after('catatan_admin')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('surat_solars', 'disetujui_at')) {
                $table->timestamp('disetujui_at')->nullable()->after('disetujui_oleh');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surat_solars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh');
            $table->dropColumn('disetujui_at');
        });
    }
};