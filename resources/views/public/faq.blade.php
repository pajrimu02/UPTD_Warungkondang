@extends('layouts.public')

@section('title', 'FAQ - UPTD Pelayanan Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        [data-tab][aria-pressed="true"] { background: #1B4332; color: #fff; border-color: #1B4332; }
        [data-tab][aria-pressed="true"] .count { background: rgba(255,255,255,.2); color: #fff; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
        }
    </style>

    @php
        $faq = collect($faq ?? []);
        $total = $faq->sum(fn ($items) => count($items));

        $icon = fn ($k) => match (strtolower($k)) {
            'pupuk' => 'bi-flower1',
            'solar' => 'bi-fuel-pump',
            'surat' => 'bi-file-earmark-text',
            'akun', 'akun & status' => 'bi-person-circle',
            default => 'bi-question-circle',
        };
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
                    <span class="text-white">FAQ</span>
                </nav>
                <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Pertanyaan Umum</h1>
                <p class="mt-3 text-[#E9C85A] font-medium text-lg">Jawaban cepat untuk pertanyaan yang sering diajukan</p>
                <p class="mt-2 text-white/75 text-sm max-w-lg">Seputar surat rekomendasi solar, pupuk bersubsidi, dan penggunaan layanan online UPTD.</p>
            </div>
        </div>
    </section>

    {{-- ================= FAQ ================= --}}
    <section class="py-16 reveal">
        <div class="max-w-3xl mx-auto">

            {{-- Pencarian --}}
            <div class="relative">
                <i class="bi bi-search absolute left-5 top-1/2 -translate-y-1/2 text-[#5C6B62]"></i>
                <input id="cari" type="search" placeholder="Cari pertanyaan, misalnya: berkas, ditolak, pupuk..." autocomplete="off"
                       class="w-full bg-white border border-[#E1DCC9] rounded-full pl-12 pr-5 py-3.5 text-sm shadow-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20">
            </div>

            {{-- Tab kategori --}}
            @if ($faq->count() > 1)
                <div class="flex flex-wrap gap-2 mt-5" role="group" aria-label="Filter kategori">
                    <button type="button" data-tab="semua" aria-pressed="true"
                            class="inline-flex items-center gap-2 text-sm font-medium bg-white text-[#5C6B62] border border-[#E1DCC9] rounded-full px-4 py-2 transition hover:border-[#2D6A4F]">
                        Semua <span class="count text-xs rounded-full bg-[#F5F3EC] text-[#5C6B62] px-2 py-0.5">{{ $total }}</span>
                    </button>
                    @foreach ($faq as $kategori => $items)
                        <button type="button" data-tab="{{ strtolower($kategori) }}" aria-pressed="false"
                                class="inline-flex items-center gap-2 text-sm font-medium bg-white text-[#5C6B62] border border-[#E1DCC9] rounded-full px-4 py-2 transition hover:border-[#2D6A4F]">
                            <i class="bi {{ $icon($kategori) }}"></i> {{ $kategori }}
                            <span class="count text-xs rounded-full bg-[#F5F3EC] text-[#5C6B62] px-2 py-0.5">{{ count($items) }}</span>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Daftar --}}
            <div class="mt-8 space-y-10">
                @forelse ($faq as $kategori => $items)
                    <div data-section data-kategori="{{ strtolower($kategori) }}">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-10 h-10 rounded-xl bg-[#1B4332] text-white flex items-center justify-center">
                                <i class="bi {{ $icon($kategori) }}"></i>
                            </span>
                            <h2 class="font-serif text-xl text-[#1B4332]">{{ $kategori }}</h2>
                        </div>

                        <div class="space-y-3">
                            @foreach ($items as $item)
                                <details data-item data-teks="{{ strtolower($item['q'].' '.$item['a']) }}"
                                         class="group bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden transition open:shadow-lg open:border-[#2D6A4F]">
                                    <summary class="flex items-center justify-between gap-4 p-5 cursor-pointer select-none">
                                        <span class="font-semibold text-[#14231C] leading-snug">{{ $item['q'] }}</span>
                                        <span class="w-8 h-8 shrink-0 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center transition group-open:bg-[#1B4332] group-open:text-white">
                                            <i class="bi bi-plus-lg group-open:hidden"></i>
                                            <i class="bi bi-dash-lg hidden group-open:inline"></i>
                                        </span>
                                    </summary>
                                    <div class="px-5 pb-5">
                                        <div class="text-sm text-[#5C6B62] leading-relaxed border-t border-[#E1DCC9] pt-4">
                                            {!! nl2br(e($item['a'])) !!}
                                        </div>
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-[#E1DCC9] rounded-3xl px-6 py-16 text-center">
                        <span class="w-16 h-16 mx-auto rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-3xl">
                            <i class="bi bi-question-circle"></i>
                        </span>
                        <h3 class="font-serif text-xl text-[#1B4332] mt-5">Belum ada pertanyaan</h3>
                        <p class="text-sm text-[#5C6B62] mt-2">Daftar pertanyaan akan segera ditambahkan.</p>
                    </div>
                @endforelse
            </div>

            <div id="kosong" class="hidden bg-white border border-[#E1DCC9] rounded-3xl px-6 py-14 text-center mt-8">
                <span class="w-14 h-14 mx-auto rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-2xl">
                    <i class="bi bi-search"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">Pertanyaan tidak ditemukan</p>
                <p class="text-sm text-[#5C6B62] mt-1">Coba kata kunci lain, atau hubungi petugas kami.</p>
            </div>
        </div>
    </section>

    {{-- ================= BELUM KETEMU ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
        <div class="max-w-6xl mx-auto px-6 reveal">
            <div class="text-center max-w-xl mx-auto">
                <p class="text-[#2D6A4F] font-semibold text-sm mb-2">MASIH BUTUH BANTUAN?</p>
                <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Belum ketemu jawabannya?</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-5 mt-10">
                @foreach ([
                    ['bi-geo-alt', 'Datang ke kantor', 'Temui petugas UPTD langsung pada jam layanan.', route('hubungi'), 'Lihat kontak & lokasi'],
                    ['bi-file-earmark-text', 'Lihat pelayanan', 'Pelajari persyaratan dan alur pengajuan lengkap.', route('infopublik.pelayanan'), 'Buka halaman pelayanan'],
                    ['bi-journal-text', 'Baca ketentuan', 'Aturan dan larangan penggunaan BBM bersubsidi.', route('infopublik.informasi'), 'Baca informasi'],
                ] as [$ico, $judul, $isi, $url, $label])
                    <a href="{{ $url }}" class="group block bg-white border border-[#E1DCC9] rounded-2xl p-6 hover:-translate-y-1 hover:shadow-lg hover:border-[#2D6A4F] transition">
                        <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#1B4332] group-hover:bg-[#1B4332] group-hover:text-white flex items-center justify-center text-xl transition-colors">
                            <i class="bi {{ $ico }}"></i>
                        </span>
                        <h3 class="font-semibold text-[#14231C] mt-5">{{ $judul }}</h3>
                        <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">{{ $isi }}</p>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-4">
                            {{ $label }} <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= PENUTUP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <h2 class="font-serif text-3xl md:text-4xl leading-tight">Siap mengajukan surat rekomendasi?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Daftar, lengkapi data, dan pantau statusnya dari mana saja.</p>
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
            if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); }
            else {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
                }, { threshold: 0.12 });
                els.forEach(e => io.observe(e));
            }

            const cari = document.getElementById('cari');
            const tabs = document.querySelectorAll('[data-tab]');
            const sections = document.querySelectorAll('[data-section]');
            const kosong = document.getElementById('kosong');
            let aktif = 'semua';

            function terapkan() {
                const q = cari.value.trim().toLowerCase();
                let total = 0;

                sections.forEach(sec => {
                    const kategoriCocok = aktif === 'semua' || sec.dataset.kategori === aktif;
                    let terlihat = 0;
                    sec.querySelectorAll('[data-item]').forEach(it => {
                        const ok = kategoriCocok && (!q || it.dataset.teks.includes(q));
                        it.classList.toggle('hidden', !ok);
                        if (ok) terlihat++;
                    });
                    sec.classList.toggle('hidden', terlihat === 0);
                    total += terlihat;
                });

                kosong.classList.toggle('hidden', total > 0 || sections.length === 0);
            }

            cari.addEventListener('input', terapkan);
            tabs.forEach(tab => tab.addEventListener('click', () => {
                aktif = tab.dataset.tab;
                tabs.forEach(t => t.setAttribute('aria-pressed', t === tab ? 'true' : 'false'));
                terapkan();
            }));
        })();
    </script>

@endsection