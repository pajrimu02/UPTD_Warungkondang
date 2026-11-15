<?php

namespace Database\Seeders;

use App\Models\Poktan;
use Illuminate\Database\Seeder;

class PoktanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            
        ];

        foreach ($data as $row) {
            Poktan::firstOrCreate(
                ['nama_kelompok' => $row['nama_kelompok']],
                $row
            );
        }
    }
}