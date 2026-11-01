<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    public function run(): void
    {
     
        $desas = [
            'Bunikasih'  => '32.03.02.2008',
            'Bunisari'   => '32.03.02.2014',
            'Cieundeur'  => '32.03.02.2017',
            'Cikaroya'   => '32.03.02.2007',
            'Cisarandi'  => '32.03.02.2006',
            'Ciwalen'    => '32.03.02.2009',
            'Jambudipa'  => '32.03.02.2002',
            'Mekarwangi' => '32.03.02.2018',
            'Sukamulya'  => '32.03.02.2016',
            'Sukawangi'  => '32.03.02.2011',
            'Tegallega'  => '32.03.02.2010',
        ];

        foreach ($desas as $nama => $kode) {
            
            Desa::updateOrCreate(['nama' => $nama], ['kode_desa' => $kode]);
        }
    }
}