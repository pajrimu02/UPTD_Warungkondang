<?php

return [
    'nama' => 'UPTD Pelayanan Pertanian Warungkondang',
    'tempat' => 'Warungkondang',
    'jabatan_penandatangan' => 'Kepala Sub Bagian Tata Usaha',
    'nama_penandatangan' => 'Jamaludin Murdani, S.P.',
    'nip_penandatangan' => '198811212017061001',

    // Penyalur BBM bawaan (SPBU)
    'default_tempat_pengambilan' => 'SPBU Pertamina 34.43206 Ciwalen',
    'default_nomor_penyalur' => '34.43206',
    'default_alamat_penyalur' => 'Jl. Raya Sukabumi, Ciwalen, Kec. Warungkondang, Kabupaten Cianjur, Jawa Barat 43261',
    'default_alat_pembelian' => 'Jerigen',
    'masa_berlaku_bulan' => 3,

    // Aturan poktan (dari pihak berwenang)
    'min_luas_poktan_ha' => 10,      
    'maks_anggota_poktan' => 35,     
    'default_lbs_desa_ha' => 250,      

    'kecamatan' => 'Warungkondang',
    'kabupaten' => 'Cianjur',

    // Penandatangan SK Pengukuhan Poktan  
    'sk_jabatan_penandatangan' => 'Kepala UPTD Pelayanan Pertanian',
    'sk_nama_penandatangan' => 'Yani Hendriani, S.P.',
    'sk_nip_penandatangan' => 'NIP. 197508142009012001',

    // Daftar Penyuluh Pertanian Lapangan (PPL) UPTD Warungkondang
    // Daftar Penyuluh Pertanian Lapangan (PPL) UPTD Warungkondang (11 Desa)
    'penyuluh' => [
        [
            'nama' => 'Dedi Supriadi, S.P.',
            'nip' => '198204122010011005',
            'wilayah_desa' => ['Jambudipa', 'Ciwalen', 'Bunisari'],
        ],
        [
            'nama' => 'Rina Marlina, S.P.',
            'nip' => '198609182014022002',
            'wilayah_desa' => ['Cikaroya', 'Tegallega'],
        ],
        [
            'nama' => 'Asep Saepuloh, A.Md.',
            'nip' => '199002052019031004',
            'wilayah_desa' => ['Mekarsari', 'Sukamulya'],
        ],
        [
            'nama' => 'Hendra Gunawan, S.P.',
            'nip' => '198407152011011003',
            'wilayah_desa' => ['Cieundeur', 'Cisarandi'],
        ],
        [
            'nama' => 'Siti Nurjanah, S.P.',
            'nip' => '199111102020122008',
            'wilayah_desa' => ['Sukawangi', 'Bunikasih'],
        ],
    ],
];