<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('poktans', function (Blueprint $table) {
            if (! Schema::hasColumn('poktans', 'desa_id')) {
                $table->foreignId('desa_id')->nullable()->constrained('desas')->nullOnDelete();
            }
            // Default 'disetujui' supaya poktan yang sudah ada (diinput admin) tetap aktif
            if (! Schema::hasColumn('poktans', 'status')) {
                $table->string('status', 20)->default('disetujui'); // draft|diajukan|disetujui|ditolak
            }
            if (! Schema::hasColumn('poktans', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable();
            }
            if (! Schema::hasColumn('poktans', 'diajukan_oleh')) {
                $table->foreignId('diajukan_oleh')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('poktans', 'disetujui_at')) {
                $table->timestamp('disetujui_at')->nullable();
            }
        });

        // Draft belum punya ketua, jadi kolom ini harus boleh kosong
        if (Schema::hasColumn('poktans', 'nama_ketua')) {
            DB::statement('ALTER TABLE poktans MODIFY nama_ketua VARCHAR(255) NULL');
        }
    }

    public function down(): void
    {
        //
    }
};