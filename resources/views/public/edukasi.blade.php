@extends('layouts.public')

@section('title', 'Edukasi - UPTD Pelayanan Pertanian Warungkondang')

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
        $semua = collect($data ?? []);

        $ringkas = fn ($a, $n = 130) => \Illuminate\Support\Str::limit(
            trim(preg_replace('/\s+/', ' ', strip_tags($a->konten ?? ''))), $n
        );
        $menit = fn ($a) => max(1, (int) ceil(str_word_count(strip_tags($a->konten ?? '')) / 200));
        $tgl = fn ($a) => $a->created_at ? $a->created_at->translatedFormat('d F Y') : null;

        $grads = [
            'from-[#1B4332] to-[#2D6A4F]',
            'from-[#2D6A4F] to-[#7FA650]',
            'from-[#7A5C12] to-[#C9A227]',
        ];
        $icons = ['bi-flower2', 'bi-gear-wide-connected', 'bi-calendar2-check'];

        // Topik yang akan dibahas (tampil saat belum ada artikel)
        $topik = [
            ['bi-flower2', 'Penggunaan pupuk yang tepat', 'Dosis, waktu, dan cara aplikasi agar pupuk efektif.'],
            ['bi-gear-wide-connected', 'Perawatan alat & mesin', 'Menjaga performa traktor dan mesin agar hemat BBM.'],
            ['bi-calendar2-check', 'Siklus & musim tanam', 'Mengatur jadwal tanam dan pengajuan layanan.'],
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
                    <span>Informasi Publik</span>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span class="text-white">Edukasi</span>
                </nav>
                <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Edukasi Pertanian</h1>
                <p class="mt-3 text-[#E9C85A] font-medium text-lg">Belajar bersama, tumbuh bersama</p>
                <p class="mt-2 text-white/75 text-sm max-w-lg">Artikel dan panduan seputar budi daya, pupuk, dan alat mesin pertanian dari UPTD Warungkondang.</p>
            </div>
        </div>
    </section>

    {{-- ================= DAFTAR ARTIKEL ================= --}}
    <section class="py-16 reveal">

        <div class="flex items-end justify-between flex-wrap gap-4 mb-8">
            <div>
                <p class="text-[#2D6A4F] font-semibold text-sm mb-1">ARTIKEL</p>
                <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">
                    {{ $semua->isEmpty() ? 'Artikel segera hadir' : 'Bacaan untuk petani' }}
                </h2>
            </div>

            @if ($semua->isNotEmpty())
                <div class="relative w-full sm:w-72">
                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-[#5C6B62]"></i>
                    <input id="cari" type="search" placeholder="Cari artikel..." autocomplete="off"
                           class="w-full bg-white border border-[#E1DCC9] rounded-full pl-11 pr-4 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20">
                </div>
            @endif
        </div>

        @if ($semua->isEmpty())
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($topik as $n => [$icon, $judul, $isi])
                    <div class="bg-white border border-dashed border-[#C9C2A3] rounded-2xl overflow-hidden">
                        <div class="h-36 bg-gradient-to-br {{ $grads[$n % 3] }} flex items-center justify-center">
                            <i class="bi {{ $icon }} text-6xl text-white/25"></i>
                        </div>
                        <div class="p-5">
                            <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full bg-[#F6EDC9] text-[#7A5C12] px-2.5 py-1">Segera hadir</span>
                            <h3 class="font-semibold text-[#14231C] mt-3 leading-snug">{{ $judul }}</h3>
                            <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">{{ $isi }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div id="grid" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($semua as $i => $a)
                    @php $utama = $i === 0; @endphp
                    <a href="{{ route('infopublik.edukasi.show', $a->slug) }}"
                       data-item data-teks="{{ strtolower($a->judul.' '.strip_tags($a->konten ?? '')) }}"
                       class="group bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-lg hover:border-[#2D6A4F] transition
                              {{ $utama ? 'sm:col-span-2 lg:col-span-2 md:flex-row' : '' }}">

                        <div class="relative overflow-hidden {{ $utama ? 'md:w-1/2 h-48 md:h-auto md:min-h-[260px]' : 'h-44' }}">
                            @if ($a->gambar)
                                <img src="{{ asset('storage/'.$a->gambar) }}" alt="{{ $a->judul }}" loading="lazy"
                                     class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br {{ $grads[$i % 3] }} flex items-center justify-center">
                                    <i class="bi {{ $icons[$i % 3] }} text-7xl text-white/25 transition-transform duration-500 group-hover:scale-110"></i>
                                </div>
                            @endif
                            @if ($utama)
                                <span class="absolute top-3 left-3 text-[11px] font-semibold rounded-full bg-[#C9A227] text-[#1B4332] px-2.5 py-1">
                                    <i class="bi bi-stars"></i> Terbaru
                                </span>
                            @endif
                        </div>

                        <div class="p-6 flex flex-col flex-1 {{ $utama ? 'md:w-1/2 md:p-8 justify-center' : '' }}">
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-[#5C6B62]">
                                @if ($tgl($a))
                                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-calendar3 text-[#2D6A4F]"></i> {{ $tgl($a) }}</span>
                                @endif
                                <span class="inline-flex items-center gap-1.5"><i class="bi bi-clock text-[#2D6A4F]"></i> {{ $menit($a) }} menit baca</span>
                            </div>

                            <h3 class="font-semibold text-[#14231C] mt-3 leading-snug {{ $utama ? 'font-serif text-2xl' : 'text-lg' }}">{{ $a->judul }}</h3>
                            <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed flex-1 {{ $utama ? '' : 'line-clamp-3' }}">{{ $ringkas($a, $utama ? 190 : 130) }}</p>

                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#2D6A4F] mt-5">
                                Baca artikel <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <div id="kosong" class="hidden bg-white border border-[#E1DCC9] rounded-3xl px-6 py-14 text-center">
                <span class="w-14 h-14 mx-auto rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-2xl">
                    <i class="bi bi-search"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">Artikel tidak ditemukan</p>
                <p class="text-sm text-[#5C6B62] mt-1">Coba kata kunci lain.</p>
            </div>
        @endif
    </section>

    {{-- ================= TOPIK ================= --}}
    @if ($semua->isNotEmpty())
        <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
            <div class="max-w-6xl mx-auto px-6 reveal">
                <div class="text-center max-w-xl mx-auto">
                    <p class="text-[#2D6A4F] font-semibold text-sm mb-2">TOPIK EDUKASI</p>
                    <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Yang kami bahas</h2>
                </div>
                <div class="grid md:grid-cols-3 gap-5 mt-10">
                    @foreach ($topik as [$icon, $judul, $isi])
                        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
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
    @endif

    {{-- ================= PENUTUP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden {{ $semua->isEmpty() ? '' : '' }}">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <h2 class="font-serif text-3xl md:text-4xl leading-tight">Punya pertanyaan seputar pertanian?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Penyuluh dan petugas UPTD siap membantu petani dan kelompok tani di wilayah Anda.</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3 rounded-full transition">
                    Buka FAQ <i class="bi bi-arrow-right"></i>
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
            if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); }
            else {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
                }, { threshold: 0.12 });
                els.forEach(e => io.observe(e));
            }

            const cari = document.getElementById('cari');
            if (!cari) return;
            const items = document.querySelectorAll('[data-item]');
            const kosong = document.getElementById('kosong');
            cari.addEventListener('input', () => {
                const q = cari.value.trim().toLowerCase();
                let n = 0;
                items.forEach(it => {
                    const ok = !q || it.dataset.teks.includes(q);
                    it.classList.toggle('hidden', !ok);
                    if (ok) n++;
                });
                kosong.classList.toggle('hidden', n > 0);
            });
        })();
    </script>

@endsection