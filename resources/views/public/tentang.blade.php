@extends('layouts.public')

@section('title', 'Tentang Kami - UPTD Pelayanan Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
        }
    </style>

    @php
        // Ambil inisial dari nama (abaikan gelar setelah koma)
        $inisial = fn ($nama) => collect(explode(' ', trim(explode(',', $nama)[0])))
            ->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

        // ====== DATA STRUKTUR ORGANISASI (edit di sini kalau ada pergantian pejabat) ======
        $kepala = [
            'nama' => 'Yani Hendriani, S.P.',
            'jabatan' => 'Kepala UPTD',
            'nip' => '197508142009012001',
        ];
        $kasubag = [
            'nama' => 'Jamaludin Murdani, S.P.',
            'jabatan' => 'Kepala Sub Bagian Tata Usaha',
            'nip' => '198811212017061001',
        ];
        $staf = [
            ['nama' => 'Yana Suryana', 'jabatan' => 'Staf / Operator Layanan Operasional', 'nip' => '197706112025211007'],
            ['nama' => 'Agus Mustar', 'jabatan' => 'Staf / Operator Layanan Operasional', 'nip' => '199603262025211050'],
        ];
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10">
        <div class="relative h-[300px] md:h-[360px] overflow-hidden bg-[#1B4332]">
            <img src="{{ asset('storage/home/uptd.png') }}" alt="Kantor UPTD Pelayanan Pertanian Warungkondang"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0F1F17]/95 via-[#0F1F17]/70 to-[#0F1F17]/30"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F1F17]/70 via-transparent to-transparent"></div>

            <div class="relative z-10 h-full max-w-6xl mx-auto px-6 flex flex-col justify-center">
                <nav class="flex items-center gap-2 text-xs text-white/70 mb-4" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span class="text-white">Tentang Kami</span>
                </nav>
                <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Tentang Kami</h1>
                <p class="mt-3 text-[#E9C85A] font-medium text-lg">Melayani Petani, Mendukung Pertanian</p>
                <p class="mt-2 text-white/75 text-sm max-w-lg">UPTD Pelayanan Pertanian Warungkondang, Kabupaten Cianjur, Jawa Barat.</p>
            </div>
        </div>
    </section>

    {{-- ================= PROFIL ================= --}}
    <section class="py-16 grid md:grid-cols-5 gap-10 items-start reveal">
        <div class="md:col-span-3">
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">PROFIL</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Pertanian Maju, Petani Sejahtera</h2>

            <div class="mt-5 space-y-4 text-[#5C6B62] leading-relaxed">
                <p>
                    UPTD Pelayanan Pertanian Warungkondang adalah unit pelaksana teknis di Kabupaten Cianjur yang memberikan
                    pelayanan kepada petani dan masyarakat di bidang pertanian. Salah satu layanan utamanya adalah penerbitan
                    surat rekomendasi BBM solar bersubsidi untuk kegiatan pertanian, seperti traktor, penggilingan padi,
                    dan alat/mesin pertanian lainnya.
                </p>
                <p>
                    Selain itu, UPTD mengawasi dan mengoordinasikan kinerja Balai Penyuluhan Pertanian (BPP) di wilayah
                    Warungkondang, Gekbrong, dan Cugenang, agar pelayanan dan kegiatan penyuluhan kepada petani berjalan dengan baik.
                </p>
                <p>
                    Seluruh pelayanan dijalankan dengan prinsip <span class="font-semibold text-[#1B4332]">cepat, tepat, transparan,
                    dan tanpa pungutan</span>.
                </p>
            </div>
        </div>

        <div class="md:col-span-2 space-y-4">
            @foreach ([
                ['bi-geo-alt', 'Wilayah Binaan BPP', 'Warungkondang · Gekbrong · Cugenang'],
                ['bi-fuel-pump', 'Layanan Utama', 'Surat rekomendasi BBM solar bersubsidi untuk alat dan mesin pertanian'],
                ['bi-flag', 'Motto', 'Bersama Petani, Kita Wujudkan Pertanian yang Maju'],
            ] as [$icon, $judul, $isi])
                <div class="flex gap-4 items-start bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <span class="w-11 h-11 shrink-0 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#2D6A4F]">{{ $judul }}</p>
                        <p class="text-sm text-[#14231C] mt-1 leading-relaxed">{{ $isi }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= TUGAS & LAYANAN ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
        <div class="max-w-6xl mx-auto px-6 reveal">
            <div class="text-center max-w-xl mx-auto">
                <p class="text-[#2D6A4F] font-semibold text-sm mb-2">TUGAS & PERAN</p>
                <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Apa yang kami kerjakan</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-5 mt-10">
                @foreach ([
                    ['bi-file-earmark-check', 'Pelayanan Surat Rekomendasi', 'Memverifikasi berkas dan menerbitkan surat rekomendasi BBM solar bersubsidi bagi petani, kelompok tani, dan pelaku usaha pertanian.'],
                    ['bi-diagram-3', 'Pengawasan & Koordinasi BPP', 'Mengawasi dan mengoordinasikan kinerja BPP di tiga wilayah agar pelayanan dan penyuluhan berjalan baik.'],
                    ['bi-people', 'Pendampingan Petani', 'Bersama koordinator penyuluh dan penyuluh pertanian, memberikan pendampingan, informasi, dan penyuluhan kepada petani.'],
                ] as [$icon, $judul, $isi])
                    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 hover:-translate-y-1 hover:shadow-lg transition">
                        <span class="w-12 h-12 rounded-xl bg-[#1B4332] text-white flex items-center justify-center text-xl">
                            <i class="bi {{ $icon }}"></i>
                        </span>
                        <h3 class="font-semibold text-[#14231C] mt-5">{{ $judul }}</h3>
                        <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">{{ $isi }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= STRUKTUR ORGANISASI ================= --}}
    <section class="py-16 reveal">
        <div class="text-center max-w-xl mx-auto">
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">STRUKTUR ORGANISASI</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">UPTD Pelayanan Pertanian Warungkondang</h2>
            <p class="text-sm text-[#5C6B62] mt-2">Kabupaten Cianjur, Jawa Barat</p>
        </div>

        <div class="mt-12 flex flex-col items-center">

            {{-- Level 1: Kepala UPTD --}}
            <div class="w-full max-w-sm bg-[#1B4332] text-white rounded-3xl p-7 text-center shadow-xl shadow-[#1B4332]/20 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-[#2D6A4F]/50"></div>
                <div class="relative">
                    <div class="mx-auto w-20 h-20 rounded-full bg-[#2D6A4F] ring-4 ring-[#C9A227] flex items-center justify-center font-serif text-2xl">
                        {{ $inisial($kepala['nama']) }}
                    </div>
                    <p class="text-[11px] font-semibold uppercase tracking-widest text-[#E9C85A] mt-5">{{ $kepala['jabatan'] }}</p>
                    <p class="font-serif text-xl mt-1.5">{{ $kepala['nama'] }}</p>
                    <p class="text-xs text-white/65 mt-2">NIP. {{ $kepala['nip'] }}</p>
                </div>
            </div>

            <div class="w-0.5 h-8 bg-[#C9C2A3]"></div>

            {{-- Level 2: Kasubag TU --}}
            <div class="w-full max-w-sm bg-white border border-[#E1DCC9] border-t-4 border-t-[#C9A227] rounded-3xl p-6 text-center shadow-lg shadow-black/5">
                <div class="mx-auto w-16 h-16 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center font-serif text-xl">
                    {{ $inisial($kasubag['nama']) }}
                </div>
                <p class="text-[11px] font-semibold uppercase tracking-widest text-[#2D6A4F] mt-4">{{ $kasubag['jabatan'] }}</p>
                <p class="font-serif text-lg text-[#14231C] mt-1.5">{{ $kasubag['nama'] }}</p>
                <p class="text-xs text-[#8C9086] mt-2">NIP. {{ $kasubag['nip'] }}</p>
            </div>

            {{-- Penghubung ke level 3 (hanya desktop; di HP garis ada di tiap kartu) --}}
            <div class="hidden sm:block w-0.5 h-8 bg-[#C9C2A3]"></div>

            {{-- Level 3: Staf --}}
            <div class="relative w-full max-w-2xl">
                <div class="hidden sm:block absolute top-0 left-1/4 right-1/4 border-t-2 border-[#C9C2A3]"></div>

                <div class="grid sm:grid-cols-2 gap-x-6">
                    @foreach ($staf as $s)
                        <div class="flex flex-col items-center">
                            <div class="w-0.5 h-8 bg-[#C9C2A3]"></div>
                            <div class="w-full bg-white border border-[#E1DCC9] rounded-3xl p-6 text-center shadow-md shadow-black/5 hover:-translate-y-1 hover:shadow-lg transition">
                                <div class="mx-auto w-14 h-14 rounded-full bg-[#F6EDC9] text-[#7A5C12] flex items-center justify-center font-serif text-lg">
                                    {{ $inisial($s['nama']) }}
                                </div>
                                <p class="text-[11px] font-semibold uppercase tracking-widest text-[#2D6A4F] mt-4 leading-snug">{{ $s['jabatan'] }}</p>
                                <p class="font-serif text-lg text-[#14231C] mt-1.5">{{ $s['nama'] }}</p>
                                <p class="text-xs text-[#8C9086] mt-2">NIP. {{ $s['nip'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ================= PENUTUP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <h2 class="font-serif text-3xl md:text-4xl leading-tight">Bersama Petani, Kita Wujudkan Pertanian yang Maju</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Butuh layanan atau informasi dari UPTD Warungkondang? Kami siap membantu.</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('infopublik.pelayanan') }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3 rounded-full transition">
                    Lihat Pelayanan <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('hubungi') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-7 py-3 rounded-full transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <script>
        (function () {
            const els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); return; }
            const io = new IntersectionObserver((entries) => {
                entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
            }, { threshold: 0.12 });
            els.forEach(e => io.observe(e));
        })();
    </script>

@endsection