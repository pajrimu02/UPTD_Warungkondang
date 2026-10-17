<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
{
    $faq = [
        'Solar' => [
            ['q' => 'Siapa yang bisa mengajukan surat rekomendasi solar?',
             'a' => 'Petani, kelompok tani, dan pelaku usaha pertanian yang memiliki alat/mesin pertanian dan sudah memiliki akun di website ini.'],
            ['q' => 'Berkas apa saja yang harus disiapkan?',
             'a' => "KTP pemohon, Surat Keterangan Usaha (SKU), dan foto alat/mesin pertanian yang digunakan.\nFormat JPG, PNG, atau PDF (foto mesin JPG atau PNG), maksimal 2 MB per berkas."],
            ['q' => 'Apakah ada biaya pengajuan?',
             'a' => 'Tidak ada. Pelayanan di UPTD dilaksanakan tanpa pungutan.'],
            ['q' => 'Bagaimana cara memantau status pengajuan saya?',
             'a' => "Masuk ke akun Anda, buka menu Surat Solar di dashboard. Status pengajuan akan tampil di sana: diajukan, diproses, diterima, atau ditolak."],
            ['q' => 'Kenapa pengajuan saya langsung ditolak?',
             'a' => "Sistem menyaring kelayakan awal berdasarkan kelengkapan berkas dan riwayat pengajuan. Jika belum memenuhi ketentuan, pengajuan ditolak otomatis dan alasannya tertulis di halaman Surat Resmi.\nAnda dapat melengkapi data lalu mengajukan kembali, atau menghubungi petugas UPTD."],
            ['q' => 'Apa itu fitur Ajukan Cepat?',
             'a' => 'Setelah sekitar 3 bulan (satu musim tanam) sejak pengajuan terakhir, Anda dapat mengirim ulang permohonan memakai data sebelumnya tanpa mengisi form dari awal.'],
            ['q' => 'Setelah disetujui, surat dipakai untuk apa?',
             'a' => 'Surat rekomendasi yang sudah terbit dapat diunduh di halaman Surat Resmi, dan digunakan sebagai syarat pembelian solar bersubsidi sesuai ketentuan yang berlaku.'],
        ],
        'Pupuk' => [
            ['q' => 'Apakah pengajuan pupuk bersubsidi bisa lewat website ini?',
             'a' => 'Tidak. Kebutuhan pupuk disampaikan melalui kelompok tani dan penyuluh pertanian lapangan (PPL), lalu tercatat di sistem e-RDKK resmi Kementerian Pertanian.'],
            ['q' => 'Di mana saya bisa melihat kuota pupuk?',
             'a' => 'Informasi kuota dan jadwal distribusi tersedia di halaman Stok & Kuota yang diperbarui oleh admin UPTD.'],
        ],
        'Akun' => [
            ['q' => 'Bagaimana cara mendaftar akun?',
             'a' => 'Klik Daftar, lalu isi nama, NIK, nomor HP, pilih kelompok tani, serta email dan password.'],
            ['q' => 'Kelompok tani saya tidak ada di daftar saat mendaftar.',
             'a' => 'Hubungi petugas UPTD agar kelompok tani Anda ditambahkan oleh admin terlebih dahulu.'],
        ],
    ];

    return view('public.faq', compact('faq'));
}
}
