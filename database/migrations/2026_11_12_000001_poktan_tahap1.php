<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Baris anggota nanti dibuat dulu (saat bergabung lewat kode), datanya diisi kemudian
        Schema::table('poktan_anggotas', function (Blueprint $t) {
            $t->string('nama')->nullable()->change();
            $t->string('nik', 16)->nullable()->change();
            $t->string('no_hp', 20)->nullable()->change();
            $t->text('alamat')->nullable()->change();
            $t->decimal('luas_lahan_ha', 8, 2)->nullable()->change();
            $t->string('file_ktp')->nullable()->change();
            $t->string('file_kk')->nullable()->change();
            $t->string('file_sppt')->nullable()->change();
        });

        Schema::table('poktan_anggotas', function (Blueprint $t) {
            $t->string('nomor_kk', 16)->nullable()->after('nik');
            $t->foreignId('user_id')->nullable()->after('poktan_id')->constrained('users')->nullOnDelete();
            $t->string('status', 20)->default('lengkap')->after('jabatan'); // menunggu | lengkap | nonaktif
            $t->boolean('dibantu_petugas')->default(false)->after('status');
        });

        Schema::table('poktans', function (Blueprint $t) {
            $t->string('kode_undangan', 20)->nullable()->unique()->after('alamat');
            $t->timestamp('dikunci_at')->nullable()->after('kode_undangan');
        });
    }

    public function down(): void
    {
        Schema::table('poktans', function (Blueprint $t) {
            $t->dropUnique(['kode_undangan']);
            $t->dropColumn(['kode_undangan', 'dikunci_at']);
        });

        Schema::table('poktan_anggotas', function (Blueprint $t) {
            $t->dropConstrainedForeignId('user_id');
            $t->dropColumn(['nomor_kk', 'status', 'dibantu_petugas']);
            // kolom yang tadi dibuat nullable tidak dikembalikan
        });
    }
};