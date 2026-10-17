@extends('layouts.public')

@section('title', 'Stok & Kuota - UPTD Pelayanan Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        [data-filter][aria-pressed="true"] { background: #1B4332; color: #fff; border-color: #1B4332; }
        [data-filter][aria-pressed="true"] .count { background: rgba(255,255,255,.2); color: #fff; }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
        }
    </style>

    @php
        $semua = collect($data ?? []);
        $solar = $semua->where('jenis', 'solar');
        $pupuk = $semua->where('jenis', 'pupuk');

        $terbaruSolar = $solar->first();   // controller sudah latest()
        $terbaruPupuk = $pupuk->first();

        $tgl = fn ($item) => $item->updated_at
            ? $item->updated_at->translatedFormat('d F Y')
            : null;

        $meta = [
            'solar' => ['icon' => 'bi-fuel-pump', 'label' => 'Solar', 'badge' => 'bg-amber-50 text-amber-700', 'icobg' => 'bg-amber-50 text-amber-700'],
            'pupuk' => ['icon' => 'bi-flower1',   'label' => 'Pupuk', 'badge' => 'bg-emerald-50 text-emerald-700', 'icobg' => 'bg-emerald-50 text-emerald-700'],
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
                    <span class="text-white">Stok & Kuota</span>
                </nav>
                <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Stok & Kuota</h1>
                <p class="mt-3 text-[#E9C85A] font-medium text-lg">Pupuk dan solar bersubsidi</p>
                <p class="mt-2 text-white/75 text-sm max-w-lg">Informasi ketersediaan, kuota, dan jadwal distribusi yang diperbarui langsung oleh UPTD.</p>
            </div>
        </div>
    </section>

    {{-- ================= SOROTAN TERBARU ================= --}}
    <section class="py-14 reveal">
        <div class="flex items-end justify-between flex-wrap gap-3 mb-7">
            <div>
                <p class="text-[#2D6A4F] font-semibold text-sm mb-1">PEMBARUAN TERBARU</p>
                <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Informasi paling baru</h2>
            </div>
            <span class="inline-flex items-center gap-2 text-xs text-[#5C6B62] bg-white border border-[#E1DCC9] rounded-full px-3 py-1.5">
                <i class="bi bi-arrow-repeat text-[#2D6A4F]"></i> Diperbarui oleh admin UPTD
            </span>
        </div>

        <div class="grid md:grid-cols-2 gap-5">
            @foreach ([
                ['solar', $terbaruSolar, 'bg-[#1B4332]', 'bg-[#2D6A4F]/50', 'text-[#CFE3D6]'],
                ['pupuk', $terbaruPupuk, 'bg-[#2D6A4F]', 'bg-[#7FA650]/40', 'text-[#DCEBDD]'],
            ] as [$jenis, $item, $bg, $blob, $soft])
                <div class="relative {{ $bg }} text-white rounded-3xl p-7 md:p-8 overflow-hidden shadow-xl shadow-[#1B4332]/15 min-h-[230px]">
                    <div class="absolute -top-14 -right-14 w-48 h-48 rounded-full {{ $blob }}"></div>
                    <div class="absolute -bottom-20 -left-10 w-52 h-52 rounded-full bg-[#C9A227]/10"></div>

                    <div class="relative flex flex-col h-full">
                        <div class="flex items-center gap-3">
                            <span class="w-12 h-12 rounded-2xl bg-[#C9A227] text-[#1B4332] flex items-center justify-center text-xl">
                                <i class="bi {{ $meta[$jenis]['icon'] }}"></i>
                            </span>
                            <p class="text-xs font-semibold uppercase tracking-widest text-[#E9C85A]">{{ $meta[$jenis]['label'] }}</p>
                        </div>

                        @if ($item)
                            <h3 class="font-serif text-xl md:text-2xl mt-5 leading-snug">{{ $item->judul }}</h3>
                            <p class="{{ $soft }} text-sm mt-2 leading-relaxed line-clamp-3">{{ $item->isi }}</p>

                            <div class="mt-auto pt-5 flex flex-wrap gap-2 text-xs">
                                @if ($item->periode)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/20 px-3 py-1.5">
                                        <i class="bi bi-calendar3"></i> {{ $item->periode }}
                                    </span>
                                @endif
                                @if ($tgl($item))
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/20 px-3 py-1.5">
                                        <i class="bi bi-clock-history"></i> Diperbarui {{ $tgl($item) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <h3 class="font-serif text-xl mt-5 leading-snug">Belum ada informasi {{ strtolower($meta[$jenis]['label']) }}</h3>
                            <p class="{{ $soft }} text-sm mt-2 leading-relaxed">Informasi akan tampil di sini setelah diperbarui oleh admin UPTD.</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= DAFTAR LENGKAP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
        <div class="max-w-6xl mx-auto px-6 reveal">

            <div class="flex items-end justify-between flex-wrap gap-4 mb-7">
                <div>
                    <p class="text-[#2D6A4F] font-semibold text-sm mb-1">SEMUA INFORMASI</p>
                    <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Riwayat stok dan kuota</h2>
                </div>

                {{-- Tab filter --}}
                <div class="flex flex-wrap gap-2" role="group" aria-label="Filter jenis">
                    @foreach ([
                        ['semua', 'Semua', $semua->count()],
                        ['solar', 'Solar', $solar->count()],
                        ['pupuk', 'Pupuk', $pupuk->count()],
                    ] as [$kode, $nama, $jumlah])
                        <button type="button" data-filter="{{ $kode }}" aria-pressed="{{ $kode === 'semua' ? 'true' : 'false' }}"
                                class="inline-flex items-center gap-2 text-sm font-medium bg-white text-[#5C6B62] border border-[#E1DCC9] rounded-full px-4 py-2 transition hover:border-[#2D6A4F]">
                            {{ $nama }}
                            <span class="count text-xs rounded-full bg-[#F5F3EC] text-[#5C6B62] px-2 py-0.5">{{ $jumlah }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($semua->isEmpty())
                <div class="bg-white border border-[#E1DCC9] rounded-3xl px-6 py-16 text-center">
                    <span class="w-16 h-16 mx-auto rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-3xl">
                        <i class="bi bi-box-seam"></i>
                    </span>
                    <h3 class="font-serif text-xl text-[#1B4332] mt-5">Belum ada informasi</h3>
                    <p class="text-sm text-[#5C6B62] mt-2 max-w-sm mx-auto">Informasi stok dan kuota akan tampil di sini setelah diperbarui oleh admin UPTD.</p>
                </div>
            @else
                <div id="daftar" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($semua as $item)
                        @php $m = $meta[$item->jenis] ?? $meta['pupuk']; @endphp
                        <article data-item data-jenis="{{ $item->jenis }}"
                                 class="group bg-white border border-[#E1DCC9] rounded-2xl p-6 flex flex-col hover:-translate-y-1 hover:shadow-lg hover:border-[#2D6A4F] transition">
                            <div class="flex items-center justify-between">
                                <span class="w-11 h-11 rounded-xl {{ $m['icobg'] }} flex items-center justify-center text-lg">
                                    <i class="bi {{ $m['icon'] }}"></i>
                                </span>
                                <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-1 {{ $m['badge'] }}">{{ $m['label'] }}</span>
                            </div>

                            <h3 class="font-semibold text-[#14231C] mt-4 leading-snug">{{ $item->judul }}</h3>
                            <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed flex-1">{{ $item->isi }}</p>

                            <div class="mt-5 pt-4 border-t border-[#E1DCC9] flex flex-wrap items-center justify-between gap-2 text-xs text-[#5C6B62]">
                                @if ($item->periode)
                                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-calendar3 text-[#2D6A4F]"></i> {{ $item->periode }}</span>
                                @else
                                    <span></span>
                                @endif
                                @if ($tgl($item))
                                    <span class="inline-flex items-center gap-1.5"><i class="bi bi-clock-history text-[#2D6A4F]"></i> {{ $tgl($item) }}</span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>

                <div id="kosong" class="hidden bg-white border border-[#E1DCC9] rounded-3xl px-6 py-14 text-center mt-5">
                    <p class="text-sm text-[#5C6B62]">Belum ada informasi untuk kategori ini.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= CATATAN ================= --}}
    <section class="py-16 reveal">
        <div class="grid md:grid-cols-2 gap-5">
            <div class="bg-white border border-[#E1DCC9] rounded-3xl p-7">
                <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-xl">
                    <i class="bi bi-info-circle"></i>
                </span>
                <h3 class="font-serif text-xl text-[#1B4332] mt-4">Cara membaca informasi ini</h3>
                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">
                    Informasi di halaman ini bersifat umum dan diperbarui secara berkala oleh UPTD. Untuk memastikan ketersediaan
                    sebelum Anda datang, silakan hubungi petugas atau penyuluh di wilayah Anda.
                </p>
            </div>

            <div class="bg-[#EAF2EC] border border-[#2D6A4F]/20 rounded-3xl p-7">
                <span class="w-12 h-12 rounded-xl bg-white text-[#1B4332] flex items-center justify-center text-xl">
                    <i class="bi bi-flower1"></i>
                </span>
                <h3 class="font-serif text-xl text-[#1B4332] mt-4">Soal pupuk bersubsidi</h3>
                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">
                    Pengajuan kebutuhan pupuk dilakukan melalui kelompok tani dan penyuluh lapangan (PPL), lalu tercatat di sistem
                    e-RDKK resmi Kementerian Pertanian. Halaman ini hanya menampilkan informasinya.
                    <a href="{{ route('infopublik.pelayanan') }}" class="font-semibold text-[#1B4332] underline underline-offset-2">Lihat alur lengkapnya</a>.
                </p>
            </div>
        </div>
    </section>

    {{-- ================= PENUTUP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <h2 class="font-serif text-3xl md:text-4xl leading-tight">Butuh surat rekomendasi solar?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Ajukan secara online dan pantau statusnya kapan saja, tanpa antre di kantor.</p>
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
            // Animasi muncul saat scroll
            const els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); }
            else {
                const io = new IntersectionObserver((entries) => {
                    entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
                }, { threshold: 0.12 });
                els.forEach(e => io.observe(e));
            }

            // Filter jenis
            const tabs = document.querySelectorAll('[data-filter]');
            const items = document.querySelectorAll('[data-item]');
            const kosong = document.getElementById('kosong');

            tabs.forEach(tab => tab.addEventListener('click', () => {
                const f = tab.dataset.filter;
                tabs.forEach(t => t.setAttribute('aria-pressed', t === tab ? 'true' : 'false'));

                let tampil = 0;
                items.forEach(it => {
                    const cocok = f === 'semua' || it.dataset.jenis === f;
                    it.classList.toggle('hidden', !cocok);
                    if (cocok) tampil++;
                });
                if (kosong) kosong.classList.toggle('hidden', tampil > 0);
            }));
        })();
    </script>

@endsection