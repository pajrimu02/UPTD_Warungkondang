<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stok_kuotas', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis', ['pupuk', 'solar']);
            $table->string('judul');
            $table->text('isi'); // kuota, jadwal distribusi, dll
            $table->string('periode')->nullable(); // mis. "Triwulan I 2026"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_kuotas');
    }
};
