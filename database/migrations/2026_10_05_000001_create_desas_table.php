<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('desas')) {
            Schema::create('desas', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->decimal('luas_baku_sawah_ha', 10, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};