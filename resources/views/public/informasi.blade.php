@extends('layouts.public')

@section('title', 'Informasi - UPTD Pelayanan Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        [data-filter][aria-pressed="true"] { background: #1B4332; color: #fff; border-color: #1B4332; }
        [data-filter][aria-pressed="true"] .count { background: rgba(255,255,255,.2); color: #fff; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
        }
    </style>

    @php
        $semua = collect($data ?? []);

        // Kategori dinamis dari data yang diisi admin
        $kategoriList = $semua->map(fn ($i) => trim((string) $i->kategori))
            ->filter()->unique(fn ($k) => strtolower($k))->values();

        $hitung = fn ($k) => $semua->filter(fn ($i) => strtolower(trim((string) $i->kategori)) === strtolower($k))->count();

        // Warna & ikon per kategori
        $meta = fn ($k) => match (strtolower(trim((string) $k))) {
            'solar' => ['icon' => 'bi-fuel-pump', 'badge' => 'bg-amber-50 text-amber-700', 'ico' => 'bg-amber-50 text-amber-700'],
            'pupuk' => ['icon' => 'bi-flower1', 'badge' => 'bg-emerald-50 text-emerald-700', 'ico' => 'bg-emerald-50 text-emerald-700'],
            'larangan', 'sanksi' => ['icon' => 'bi-exclamation-triangle', 'badge' => 'bg-red-50 text-red-700', 'ico' => 'bg-red-50 text-red-700'],
            default => ['icon' => 'bi-info-circle', 'badge' => 'bg-[#EAF2EC] text-[#2D6A4F]', 'ico' => 'bg-[#EAF2EC] text-[#1B4332]'],
        };

        // ------------------------------------------------------------------
        // KETENTUAN UMUM (statis). TODO: cocokkan dengan brosur resmi UPTD
        // sebelum launching, atau pindahkan ke admin lewat Kelola Informasi.
        // ------------------------------------------------------------------
        $larangan = [
            'Menjual kembali atau memperjualbelikan solar bersubsidi kepada pihak lain.',
            'Menggunakan solar bersubsidi di luar peruntukan kegiatan pertanian sesuai surat rekomendasi.',
            'Menimbun atau menyimpan solar melebihi kebutuhan kegiatan pertanian.',
            'Memalsukan, mengubah, atau meminjamkan surat rekomendasi kepada orang lain.',
            'Membeli solar melebihi volume yang tercantum dalam surat rekomendasi.',
        ];

        $perhatian = [
            ['bi-wrench-adjustable', 'Sesuai alat/mesin', 'Solar hanya untuk alat dan mesin yang tercantum dalam pengajuan.'],
            ['bi-file-earmark-check', 'Bawa surat rekomendasi', 'Tunjukkan surat rekomendasi resmi saat membeli di SPBU.'],
            ['bi-speedometer2', 'Patuhi volume', 'Pembelian tidak boleh melebihi volume dan periode yang disetujui.'],
            ['bi-person-check', 'Data yang benar', 'Isi data pengajuan dengan jujur dan lengkap sesuai kondisi sebenarnya.'],
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
                    <span class="text-white">Informasi</span>
                </nav>
                <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Informasi</h1>
                <p class="mt-3 text-[#E9C85A] font-medium text-lg">Aturan, larangan, dan sanksi BBM bersubsidi</p>
                <p class="mt-2 text-white/75 text-sm max-w-lg">Pahami ketentuan agar penggunaan solar dan pupuk bersubsidi tepat sasaran.</p>
            </div>
        </div>
    </section>

    {{-- ================= LARANGAN & PERHATIAN ================= --}}
    <section class="py-16 reveal">
        <div class="grid lg:grid-cols-5 gap-6 items-stretch">

            {{-- Larangan --}}
            <div class="lg:col-span-3 relative bg-[#1B4332] text-white rounded-3xl p-8 md:p-10 overflow-hidden shadow-xl shadow-[#1B4332]/20">
                <div class="absolute -top-16 -right-16 w-56 h-56 rounded-full bg-[#2D6A4F]/50"></div>
                <div class="absolute -bottom-20 -left-12 w-64 h-64 rounded-full bg-[#C9A227]/10"></div>

                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 text-xs font-medium px-3 py-1.5">
                        <i class="bi bi-exclamation-triangle-fill text-[#E9C85A]"></i> Wajib diketahui
                    </span>

                    <h2 class="font-serif text-2xl md:text-3xl mt-5 leading-snug">Larangan penyalahgunaan solar bersubsidi</h2>
                    <p class="text-[#CFE3D6] mt-3 text-sm leading-relaxed">
                        Solar bersubsidi disediakan khusus untuk kegiatan pertanian. Perbuatan berikut tidak diperbolehkan:
                    </p>

                    <ul class="mt-6 space-y-3.5">
                        @foreach ($larangan as $item)
                            <li class="flex gap-3 items-start">
                                <span class="w-6 h-6 shrink-0 rounded-full bg-red-500/20 text-[#FFB4B4] flex items-center justify-center text-xs mt-0.5">
                                    <i class="bi bi-x-lg"></i>
                                </span>
                                <span class="text-sm text-[#E4EAE1] leading-relaxed">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-7 text-xs text-[#CFE3D6] bg-white/10 border border-white/15 rounded-xl px-4 py-3 leading-relaxed">
                        <i class="bi bi-shield-exclamation text-[#E9C85A]"></i>
                        Penyalahgunaan BBM bersubsidi dapat dikenai sanksi sesuai peraturan perundang-undangan yang berlaku,
                        termasuk sanksi pidana dan pencabutan rekomendasi.
                    </p>
                </div>
            </div>

            {{-- Yang perlu diperhatikan --}}
            <div class="lg:col-span-2 bg-white border border-[#E1DCC9] rounded-3xl p-7 shadow-sm">
                <p class="text-[#2D6A4F] font-semibold text-xs tracking-wide">PANDUAN SINGKAT</p>
                <h3 class="font-serif text-xl text-[#1B4332] mt-1">Yang perlu diperhatikan</h3>

                <ul class="mt-5 space-y-4">
                    @foreach ($perhatian as [$icon, $judul, $isi])
                        <li class="flex gap-3.5 items-start">
                            <span class="w-10 h-10 shrink-0 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center">
                                <i class="bi {{ $icon }}"></i>
                            </span>
                            <div>
                                <p class="font-semibold text-sm text-[#14231C]">{{ $judul }}</p>
                                <p class="text-sm text-[#5C6B62] mt-0.5 leading-relaxed">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- ================= DAFTAR INFORMASI ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
        <div class="max-w-4xl mx-auto px-6 reveal">

            <div class="flex items-end justify-between flex-wrap gap-4 mb-7">
                <div>
                    <p class="text-[#2D6A4F] font-semibold text-sm mb-1">PENGUMUMAN & KETENTUAN</p>
                    <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Informasi dari UPTD</h2>
                </div>

                @if ($kategoriList->isNotEmpty())
                    <div class="flex flex-wrap gap-2" role="group" aria-label="Filter kategori">
                        <button type="button" data-filter="semua" aria-pressed="true"
                                class="inline-flex items-center gap-2 text-sm font-medium bg-white text-[#5C6B62] border border-[#E1DCC9] rounded-full px-4 py-2 transition hover:border-[#2D6A4F]">
                            Semua <span class="count text-xs rounded-full bg-[#F5F3EC] text-[#5C6B62] px-2 py-0.5">{{ $semua->count() }}</span>
                        </button>
                        @foreach ($kategoriList as $k)
                            <button type="button" data-filter="{{ strtolower($k) }}" aria-pressed="false"
                                    class="inline-flex items-center gap-2 text-sm font-medium bg-white text-[#5C6B62] border border-[#E1DCC9] rounded-full px-4 py-2 transition hover:border-[#2D6A4F]">
                                {{ ucfirst($k) }} <span class="count text-xs rounded-full bg-[#F5F3EC] text-[#5C6B62] px-2 py-0.5">{{ $hitung($k) }}</span>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($semua->isEmpty())
                <div class="bg-white border border-[#E1DCC9] rounded-3xl px-6 py-16 text-center">
                    <span class="w-16 h-16 mx-auto rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-3xl">
                        <i class="bi bi-journal-text"></i>
                    </span>
                    <h3 class="font-serif text-xl text-[#1B4332] mt-5">Belum ada informasi</h3>
                    <p class="text-sm text-[#5C6B62] mt-2 max-w-sm mx-auto">Pengumuman dan ketentuan akan tampil di sini setelah diisi oleh admin UPTD.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($semua as $item)
                        @php $m = $meta($item->kategori); @endphp
                        <details data-item data-kategori="{{ strtolower(trim((string) $item->kategori)) }}"
                                 class="group bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden transition open:shadow-lg open:border-[#2D6A4F]">
                            <summary class="flex items-center gap-4 p-5 cursor-pointer select-none">
                                <span class="w-11 h-11 shrink-0 rounded-xl {{ $m['ico'] }} flex items-center justify-center text-lg">
                                    <i class="bi {{ $m['icon'] }}"></i>
                                </span>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if ($item->kategori)
                                            <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $m['badge'] }}">{{ $item->kategori }}</span>
                                        @endif
                                        @if ($item->created_at)
                                            <span class="text-xs text-[#8C9086]">{{ $item->created_at->translatedFormat('d F Y') }}</span>
                                        @endif
                                    </div>
                                    <h3 class="font-semibold text-[#14231C] mt-1 leading-snug">{{ $item->judul }}</h3>
                                </div>
                                <i class="bi bi-chevron-down text-[#5C6B62] transition-transform duration-300 group-open:rotate-180"></i>
                            </summary>

                            <div class="px-5 pb-5 pl-[4.75rem]">
                                <div class="text-sm text-[#5C6B62] leading-relaxed border-t border-[#E1DCC9] pt-4">
                                    {!! nl2br(e($item->isi)) !!}
                                </div>
                            </div>
                        </details>
                    @endforeach
                </div>

                <div id="kosong" class="hidden bg-white border border-[#E1DCC9] rounded-3xl px-6 py-12 text-center mt-3">
                    <p class="text-sm text-[#5C6B62]">Belum ada informasi untuk kategori ini.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= LAPOR ================= --}}
    <section class="py-16 reveal">
        <div class="grid md:grid-cols-2 gap-5">
            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-7">
                <span class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
                    <i class="bi bi-megaphone"></i>
                </span>
                <h3 class="font-serif text-xl text-amber-900 mt-4">Menemukan penyalahgunaan?</h3>
                <p class="text-sm text-amber-900/80 mt-2 leading-relaxed">
                    Bantu kami menjaga agar subsidi tepat sasaran. Sampaikan laporan atau informasi kepada petugas UPTD.
                </p>
                <a href="{{ route('hubungi') }}" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-amber-900 underline underline-offset-2">
                    Hubungi petugas <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="bg-white border border-[#E1DCC9] rounded-3xl p-7">
                <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-xl">
                    <i class="bi bi-question-circle"></i>
                </span>
                <h3 class="font-serif text-xl text-[#1B4332] mt-4">Masih bingung soal aturan?</h3>
                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">
                    Lihat jawaban pertanyaan yang paling sering diajukan seputar pupuk, solar, dan surat rekomendasi.
                </p>
                <a href="{{ route('faq') }}" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-[#1B4332] underline underline-offset-2">
                    Buka FAQ <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- ================= PENUTUP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <h2 class="font-serif text-3xl md:text-4xl leading-tight">Sudah paham ketentuannya?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Ajukan surat rekomendasi solar secara online dan pantau statusnya kapan saja.</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ route('infopublik.pelayanan') }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3 rounded-full transition">
                    Lihat Pelayanan <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('infopublik.stokkuota') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-7 py-3 rounded-full transition">
                    Stok & Kuota
                </a>
            </div>
        </div>
    </section>

    <script>
        (function () {
            // Animasi muncul saat scroll
            const els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); }
            else {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
                }, { threshold: 0.12 });
                els.forEach(e => io.observe(e));
            }

            // Filter kategori
            const tabs = document.querySelectorAll('[data-filter]');
            const items = document.querySelectorAll('[data-item]');
            const kosong = document.getElementById('kosong');

            tabs.forEach(tab => tab.addEventListener('click', () => {
                const f = tab.dataset.filter;
                tabs.forEach(t => t.setAttribute('aria-pressed', t === tab ? 'true' : 'false'));

                let tampil = 0;
                items.forEach(it => {
                    const cocok = f === 'semua' || it.dataset.kategori === f;
                    it.classList.toggle('hidden', !cocok);
                    if (cocok) tampil++;
                });
                if (kosong) kosong.classList.toggle('hidden', tampil > 0);
            }));
        })();
    </script>

@endsection