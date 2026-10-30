<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('poktans', function (Blueprint $table) {
            if (! Schema::hasColumn('poktans', 'alamat')) {
                $table->text('alamat')->nullable();
            }
            if (! Schema::hasColumn('poktans', 'luas_lahan_ha')) {
                $table->decimal('luas_lahan_ha', 10, 2)->default(0);
            }
            if (! Schema::hasColumn('poktans', 'jumlah_anggota')) {
                $table->unsignedInteger('jumlah_anggota')->default(0);
            }
        });
    }

    public function down(): void
    {
        //
    }
};