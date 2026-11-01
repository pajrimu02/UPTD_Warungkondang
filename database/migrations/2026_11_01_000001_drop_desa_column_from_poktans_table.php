<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('poktans', 'desa')) {
            Schema::table('poktans', function (Blueprint $table) {
                $table->dropColumn('desa');
            });
        }
    }

    public function down(): void
    {
        //
    }
};