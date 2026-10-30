<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_solars', function (Blueprint $t) {
            $t->decimal('alokasi_volume', 10, 2)->nullable();
            $t->string('tempat_pengambilan')->nullable();
            $t->string('nomor_penyalur')->nullable();
            $t->text('alamat_penyalur')->nullable();
            $t->string('alat_pembelian')->nullable();
            $t->date('berlaku_sampai')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('surat_solars', fn (Blueprint $t) => $t->dropColumn([
            'alokasi_volume', 'tempat_pengambilan', 'nomor_penyalur', 'alamat_penyalur', 'alat_pembelian', 'berlaku_sampai',
        ]));
    }
};