<?php
// 2026_10_29_000002_add_biodata_to_poktan_anggotas.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('poktan_anggotas', function (Blueprint $t) {
            $t->string('tempat_lahir')->nullable()->after('nik');
            $t->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $t->string('nama_ibu_kandung')->nullable()->after('tanggal_lahir');
        });
    }
    public function down(): void
    {
        Schema::table('poktan_anggotas', fn (Blueprint $t) => $t->dropColumn(['tempat_lahir', 'tanggal_lahir', 'nama_ibu_kandung']));
    }
};