<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('poktan_anggotas')) {
            Schema::create('poktan_anggotas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('poktan_id')->constrained('poktans')->cascadeOnDelete();
                $table->string('nama');
                $table->string('nik', 16)->index();
                $table->string('no_hp', 20);
                $table->text('alamat');
                $table->decimal('luas_lahan_ha', 8, 2); // dari SPPT sawah
                $table->string('jabatan', 20)->default('anggota'); // ketua|sekretaris|bendahara|anggota
                $table->string('file_ktp');
                $table->string('file_kk');
                $table->string('file_sppt');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('poktan_anggotas');
    }
};