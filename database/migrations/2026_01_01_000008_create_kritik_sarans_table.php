<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kritik_sarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // wajib login (sesuai keputusan terbaru)
            $table->enum('kategori', ['surat', 'pupuk_solar', 'lainnya']);
            $table->text('isi');
            $table->enum('status', ['baru', 'ditanggapi'])->default('baru');
            $table->text('tanggapan_admin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kritik_sarans');
    }
};
