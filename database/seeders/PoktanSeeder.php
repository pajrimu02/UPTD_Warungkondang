<?php

namespace Database\Seeders;

use App\Models\Poktan;
use Illuminate\Database\Seeder;

class PoktanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kelompok' => 'Poktan Tani Makmur',    'nama_ketua' => 'Bapak Ahmad'],
            ['nama_kelompok' => 'Poktan Sumber Rejeki',  'nama_ketua' => 'Bapak Budi'],
            ['nama_kelompok' => 'Poktan Harapan Jaya',   'nama_ketua' => 'Bapak Cecep'],
        ];

        foreach ($data as $row) {
            Poktan::firstOrCreate(
                ['nama_kelompok' => $row['nama_kelompok']],
                $row
            );
        }
    }
}