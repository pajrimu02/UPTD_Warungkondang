<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_solars', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_solars', 'spk_skor')) {
                $table->unsignedTinyInteger('spk_skor')->nullable()->after('status');
            }
            if (! Schema::hasColumn('surat_solars', 'spk_label')) {
                $table->string('spk_label')->nullable()->after('spk_skor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surat_solars', function (Blueprint $table) {
            $table->dropColumn(['spk_skor', 'spk_label']);
        });
    }
};