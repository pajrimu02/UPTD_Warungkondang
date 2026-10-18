<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('surat_solars', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_solars', 'nama_pemohon')) {
                $table->string('nama_pemohon')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('surat_solars', 'no_hp')) {
                $table->string('no_hp', 20)->nullable()->after('alamat');
            }
            if (! Schema::hasColumn('surat_solars', 'poktan_id')) {
                $table->foreignId('poktan_id')->nullable()->after('user_id')->constrained('poktans')->nullOnDelete();
            }
            if (! Schema::hasColumn('surat_solars', 'jenis_usaha')) {
                $table->string('jenis_usaha')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'nama_kapal')) {
                $table->string('nama_kapal')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'jenis_alat_mesin')) {
                $table->string('jenis_alat_mesin')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'fungsi_alat_mesin')) {
                $table->string('fungsi_alat_mesin')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'jumlah_alat_mesin')) {
                $table->unsignedInteger('jumlah_alat_mesin')->default(1);
            }
            if (! Schema::hasColumn('surat_solars', 'daya_alat_mesin')) {
                $table->string('daya_alat_mesin')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'lama_penggunaan')) {
                $table->string('lama_penggunaan')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'lama_operasi')) {
                $table->string('lama_operasi')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'usulan_volume_konsumsi')) {
                $table->decimal('usulan_volume_konsumsi', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'volume_periode')) {
                $table->string('volume_periode')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'estimasi_sisa_liter')) {
                $table->decimal('estimasi_sisa_liter', 10, 2)->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'file_ktp')) {
                $table->string('file_ktp')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'file_sku')) {
                $table->string('file_sku')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'file_foto_mesin')) {
                $table->string('file_foto_mesin')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable();
            }
            if (! Schema::hasColumn('surat_solars', 'status_konsumen')) {
                $table->string('status_konsumen')->nullable();
            }
        });

        if (Schema::hasColumn('surat_solars', 'status')) {
            DB::statement("ALTER TABLE surat_solars MODIFY status VARCHAR(30) DEFAULT 'diajukan'");
        }
    }

    public function down(): void
    {
        //
    }
};