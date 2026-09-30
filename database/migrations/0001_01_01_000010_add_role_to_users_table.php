<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 16)->nullable()->unique()->after('name');
            $table->string('no_hp', 20)->nullable()->after('nik');
            $table->text('alamat')->nullable()->after('no_hp');
            $table->string('konsumen_pengguna')->nullable()->after('alamat');
            $table->string('jenis_usaha')->nullable()->after('konsumen_pengguna');
            $table->string('nama_kapal')->nullable()->after('jenis_usaha');
            $table->foreignId('poktan_id')->nullable()->after('nama_kapal')
                ->constrained('poktans')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('poktan_id');
            $table->dropUnique(['nik']);
            $table->dropColumn(['nik', 'no_hp', 'alamat', 'konsumen_pengguna', 'jenis_usaha', 'nama_kapal']);
        });
    }
};