<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    public function run(): void
    { 
        $desas = [
            ['nama' => 'Bunikasih',  'kode_desa' => '32.03.02.2008', 'luas_baku_sawah_ha' => 142.50],
            ['nama' => 'Bunisari',   'kode_desa' => '32.03.02.2014', 'luas_baku_sawah_ha' => 168.75],
            ['nama' => 'Cieundeur',  'kode_desa' => '32.03.02.2017', 'luas_baku_sawah_ha' => 195.20],
            ['nama' => 'Cikaroya',   'kode_desa' => '32.03.02.2007', 'luas_baku_sawah_ha' => 134.00],
            ['nama' => 'Cisarandi',  'kode_desa' => '32.03.02.2006', 'luas_baku_sawah_ha' => 210.40],
            ['nama' => 'Ciwalen',    'kode_desa' => '32.03.02.2009', 'luas_baku_sawah_ha' => 184.60],
            ['nama' => 'Jambudipa',  'kode_desa' => '32.03.02.2002', 'luas_baku_sawah_ha' => 156.30],
            ['nama' => 'Mekarwangi', 'kode_desa' => '32.03.02.2018', 'luas_baku_sawah_ha' => 121.80],
            ['nama' => 'Sukamulya',  'kode_desa' => '32.03.02.2016', 'luas_baku_sawah_ha' => 176.90],
            ['nama' => 'Sukawangi',  'kode_desa' => '32.03.02.2011', 'luas_baku_sawah_ha' => 149.25],
            ['nama' => 'Tegallega',  'kode_desa' => '32.03.02.2010', 'luas_baku_sawah_ha' => 163.10],
        ];

        foreach ($desas as $desa) {
            Desa::updateOrCreate(
                ['nama' => $desa['nama']],
                [
                    'kode_desa' => $desa['kode_desa'],
                    'luas_baku_sawah_ha' => $desa['luas_baku_sawah_ha'],
                ]
            );
        }
    }
}