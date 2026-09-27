<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class FaqController extends Controller
{
    public function index()
    {
        // sementara statis, bisa dipindah ke tabel `faqs` belakangan kalau perlu dikelola admin
        $faq = [
            'Pupuk' => [
                ['q' => 'Bagaimana cara mengajukan RDKK pupuk?', 'a' => 'Pengajuan RDKK dilakukan lewat PPL/kelompok tani, lalu direkap dan diinput ke sistem e-RDKK resmi Kementan.'],
            ],
            'Solar' => [
                ['q' => 'Siapa yang boleh mengajukan surat rekomendasi solar?', 'a' => 'Anggota kelompok tani terdaftar yang memiliki alat/mesin pertanian.'],
            ],
            'Surat' => [
                ['q' => 'Berapa lama proses surat rekomendasi?', 'a' => 'Menyusul konfirmasi dari UPTD.'],
            ],
        ];

        return view('public.faq', compact('faq'));
    }
}
