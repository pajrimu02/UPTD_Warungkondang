<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nik')) {
                $table->string('nik', 16)->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('users', 'no_hp')) {
                $table->string('no_hp', 20)->nullable()->after('nik');
            }
            if (! Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('no_hp');
            }
            if (! Schema::hasColumn('users', 'konsumen_pengguna')) {
                $table->string('konsumen_pengguna')->nullable()->after('alamat');
            }
            if (! Schema::hasColumn('users', 'jenis_usaha')) {
                $table->string('jenis_usaha')->nullable()->after('konsumen_pengguna');
            }
            if (! Schema::hasColumn('users', 'nama_kapal')) {
                $table->string('nama_kapal')->nullable()->after('jenis_usaha');
            }
            if (! Schema::hasColumn('users', 'poktan_id')) {
                $table->foreignId('poktan_id')->nullable()->after('nama_kapal')
                    ->constrained('poktans')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'poktan_id')) {
                $table->dropConstrainedForeignId('poktan_id');
            }
            foreach (['nik', 'no_hp', 'alamat', 'konsumen_pengguna', 'jenis_usaha', 'nama_kapal'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    if ($col === 'nik') {
                        $table->dropUnique(['nik']);
                    }
                    $table->dropColumn($col);
                }
            }
        });
    }
};