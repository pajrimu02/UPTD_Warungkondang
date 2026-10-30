<?php
// 2026_10_29_000001_add_kode_desa_to_desas.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('desas', fn (Blueprint $t) => $t->string('kode_desa', 20)->nullable()->after('nama'));
    }
    public function down(): void
    {
        Schema::table('desas', fn (Blueprint $t) => $t->dropColumn('kode_desa'));
    }
};