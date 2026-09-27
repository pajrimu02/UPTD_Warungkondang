<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('poktans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelompok');
            $table->string('nama_ketua');
            $table->unsignedInteger('jumlah_anggota')->default(0);
            $table->decimal('luas_lahan_ha', 8, 2)->nullable();
            $table->string('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poktans');
    }
};
