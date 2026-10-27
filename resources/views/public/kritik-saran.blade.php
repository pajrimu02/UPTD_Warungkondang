@extends('layouts.public')

@section('title', 'Kritik & Saran - UPTD Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        .hero-fade { animation: heroUp .9s ease both; }
        @keyframes heroUp { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: none; } }
        .isi-clamp { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
            .hero-fade { animation: none; }
        }
    </style>

    @php
        $items = $data instanceof \Illuminate\Pagination\AbstractPaginator ? $data->getCollection() : collect($data);

        $kategoriMeta = [
            'surat'       => ['label' => 'Surat',       'icon' => 'bi-envelope-paper', 'warna' => 'bg-sky-50 text-sky-700'],
            'pupuk_solar' => ['label' => 'Pupuk & Solar', 'icon' => 'bi-fuel-pump',    'warna' => 'bg-amber-50 text-amber-700'],
            'lainnya'     => ['label' => 'Lainnya',     'icon' => 'bi-three-dots',     'warna' => 'bg-[#EAF2EC] text-[#2D6A4F]'],
        ];

        $total = $items->count();

        if (! auth()->check()) {
            $ctaUrl = route('login'); $ctaLabel = 'Masuk untuk Mengirim';
        } elseif (auth()->user()->role === 'admin') {
            $ctaUrl = route('admin.kritiksaran.index'); $ctaLabel = 'Kelola Kritik & Saran';
        } else {
            $ctaUrl = route('user.kritiksaran.create'); $ctaLabel = 'Kirim Kritik & Saran';
        }
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10 bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 md:py-20">
            <div class="max-w-2xl hero-fade">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-medium px-3.5 py-1.5">
                    <i class="bi bi-chat-square-heart text-[#E9C85A]"></i>
                    Suara Petani · UPTD Warungkondang
                </span>

                <h1 class="font-serif text-4xl md:text-5xl leading-[1.1] mt-5">
                    Kritik & saran Anda, <span class="text-[#E9C85A]">kami dengar</span> dan tanggapi
                </h1>
                <p class="mt-5 text-[#CFE3D6] leading-relaxed max-w-lg">
                    Setiap masukan dibaca langsung oleh petugas UPTD. Di halaman ini ditampilkan pesan yang sudah ditanggapi, supaya semua petani bisa ikut belajar dari jawabannya.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-6 py-3 rounded-full transition">
                        {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#daftar" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-full transition">
                        Lihat Tanggapan
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= RINGKASAN ================= --}}
    <section class="relative -mt-8 z-10 reveal">
        <div class="bg-white border border-[#E1DCC9] rounded-3xl shadow-xl shadow-black/5 grid grid-cols-2 md:grid-cols-4 md:divide-x divide-[#E1DCC9] text-center">
            <div class="py-6 px-4">
                <p class="font-serif text-3xl text-[#1B4332]">{{ $total }}</p>
                <p class="text-sm text-[#5C6B62] mt-1">Telah Ditanggapi</p>
            </div>
            @foreach ($kategoriMeta as $key => $meta)
                <div class="py-6 px-4">
                    <p class="font-serif text-3xl text-[#1B4332]">{{ $items->where('kategori', $key)->count() }}</p>
                    <p class="text-sm text-[#5C6B62] mt-1">{{ $meta['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= DAFTAR ================= --}}
    <section id="daftar" class="pt-14 pb-10 scroll-mt-24">

        <div class="flex items-end justify-between flex-wrap gap-3 mb-6 reveal">
            <div>
                <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Tanggapan UPTD</h2>
                <p class="text-sm text-[#5C6B62] mt-1">Identitas pengirim disamarkan demi kenyamanan bersama.</p>
            </div>
        </div>

        {{-- Filter & pencarian --}}
        <div class="flex flex-col md:flex-row md:items-center gap-3 mb-8 reveal">
            <div class="flex flex-wrap gap-2" id="filter-chips">
                <button type="button" data-filter="semua" class="chip chip-active inline-flex items-center gap-1.5 text-sm px-4 py-2 rounded-full border border-[#1B4332] bg-[#1B4332] text-white transition">
                    <i class="bi bi-grid"></i> Semua
                </button>
                @foreach ($kategoriMeta as $key => $meta)
                    <button type="button" data-filter="{{ $key }}" class="chip inline-flex items-center gap-1.5 text-sm px-4 py-2 rounded-full border border-[#E1DCC9] bg-white text-[#5C6B62] hover:border-[#2D6A4F] transition">
                        <i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                    </button>
                @endforeach
            </div>

            <div class="relative md:ml-auto md:w-72">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-[#5C6B62]"></i>
                <input type="text" id="cari" placeholder="Cari kata kunci..." class="w-full pl-10 pr-4 py-2.5 rounded-full border border-[#E1DCC9] bg-white text-sm focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            </div>
        </div>

        @if ($items->isEmpty())
            <div class="bg-white border border-[#E1DCC9] rounded-3xl px-6 py-16 text-center reveal">
                <span class="w-16 h-16 rounded-full bg-[#EAF2EC] text-[#2D6A4F] flex items-center justify-center text-2xl mx-auto">
                    <i class="bi bi-chat-square-text"></i>
                </span>
                <p class="font-semibold text-[#14231C] mt-4">Belum ada tanggapan yang ditampilkan</p>
                <p class="text-sm text-[#5C6B62] mt-1 max-w-sm mx-auto">Jadilah yang pertama menyampaikan masukan. Pesan yang sudah ditanggapi akan muncul di sini.</p>
                <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-2.5 rounded-full mt-5">
                    {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @else
            <div class="grid md:grid-cols-2 gap-5" id="grid-kritik">
                @foreach ($items as $item)
                    @php
                        $meta = $kategoriMeta[$item->kategori] ?? $kategoriMeta['lainnya'];
                    @endphp
                    <article
                        class="kritik-card group bg-white border border-[#E1DCC9] rounded-2xl p-6 hover:border-[#2D6A4F] hover:shadow-lg transition reveal flex flex-col"
                        data-kategori="{{ $item->kategori }}"
                        data-teks="{{ strtolower($item->isi.' '.$item->tanggapan_admin) }}"
                    >
                        <div class="flex items-center justify-between gap-3">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-1 {{ $meta['warna'] }}">
                                <i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                            </span>
                            <span class="text-xs text-[#5C6B62]">{{ $item->created_at->translatedFormat('d M Y') }}</span>
                        </div>

                        {{-- Pesan warga --}}
                        <div class="mt-4 flex gap-3">
                            <span class="w-9 h-9 shrink-0 rounded-full bg-[#F5F3EC] text-[#5C6B62] flex items-center justify-center">
                                <i class="bi bi-person"></i>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-[#5C6B62]">Petani Warungkondang</p>
                                <p class="isi-text isi-clamp text-[#14231C] leading-relaxed mt-1">{{ $item->isi }}</p>
                                <button type="button" class="toggle-isi hidden text-xs font-semibold text-[#2D6A4F] mt-1">Baca selengkapnya</button>
                            </div>
                        </div>

                        {{-- Tanggapan UPTD --}}
                        @if ($item->tanggapan_admin)
                            <div class="mt-4 rounded-xl bg-[#EEF3E7] border-l-4 border-[#2D6A4F] p-4 flex gap-3">
                                <span class="w-9 h-9 shrink-0 rounded-full bg-[#1B4332] text-white flex items-center justify-center">
                                    <i class="bi bi-patch-check"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-[#1B4332]">
                                        Tanggapan UPTD
                                        <span class="font-normal text-[#5C6B62]"> · {{ $item->updated_at->translatedFormat('d M Y') }}</span>
                                    </p>
                                    <p class="isi-text isi-clamp text-sm text-[#14231C] leading-relaxed mt-1">{{ $item->tanggapan_admin }}</p>
                                    <button type="button" class="toggle-isi hidden text-xs font-semibold text-[#2D6A4F] mt-1">Baca selengkapnya</button>
                                </div>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            {{-- Tampil saat filter/pencarian tidak menemukan apa pun --}}
            <div id="filter-kosong" class="hidden bg-white border border-[#E1DCC9] rounded-3xl px-6 py-14 text-center">
                <i class="bi bi-search text-3xl text-[#5C6B62]"></i>
                <p class="font-semibold text-[#14231C] mt-3">Tidak ada hasil yang cocok</p>
                <p class="text-sm text-[#5C6B62] mt-1">Coba kategori lain atau ubah kata kunci pencarian.</p>
            </div>

            @if ($data instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="mt-8">{{ $data->links() }}</div>
            @endif
        @endif
    </section>

    {{-- ================= ALUR ================= --}}
    <section class="py-12 reveal">
        <div class="text-center max-w-xl mx-auto">
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">CARA MENYAMPAIKAN</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Tiga langkah menyampaikan masukan</h2>
        </div>

        <div class="relative mt-10">
            <div class="hidden md:block absolute top-7 left-[16.6%] right-[16.6%] border-t-2 border-dashed border-[#D8D2B8]"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ([
                    ['bi-box-arrow-in-right', 'Masuk ke Akun', 'Masuk atau daftar akun agar pesan Anda tercatat dan bisa dilacak.'],
                    ['bi-pencil-square', 'Tulis Pesan', 'Pilih kategori (surat, pupuk/solar, atau lainnya) lalu tuliskan masukan Anda.'],
                    ['bi-reply', 'Terima Tanggapan', 'Petugas UPTD menanggapi, dan jawabannya bisa dilihat di dashboard Anda.'],
                ] as $n => [$icon, $title, $desc])
                    <div class="relative text-center">
                        <div class="relative mx-auto w-14 h-14 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-xl ring-8 ring-[#F5F3EC] z-10">
                            <i class="bi {{ $icon }}"></i>
                            <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-[#C9A227] text-[#1B4332] text-xs font-bold flex items-center justify-center">{{ $n + 1 }}</span>
                        </div>
                        <p class="font-semibold text-[#14231C] mt-5">{{ $title }}</p>
                        <p class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed max-w-[16rem] mx-auto">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden mt-6">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-14 text-center reveal">
            <h2 class="font-serif text-3xl leading-tight">Ada yang ingin disampaikan?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Setiap keluhan dan saran membantu UPTD memperbaiki pelayanan untuk seluruh petani.</p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3 rounded-full transition">
                    {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('hubungi') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-7 py-3 rounded-full transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <script>
        (function () {
            // Reveal saat scroll
            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) { els.forEach(function (e) { e.classList.add('in'); }); }
            else {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (en) {
                        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
                    });
                }, { threshold: 0.12 });
                els.forEach(function (e) { io.observe(e); });
            }

            // Filter kategori + pencarian
            var chips = document.querySelectorAll('.chip');
            var cards = document.querySelectorAll('.kritik-card');
            var kosong = document.getElementById('filter-kosong');
            var cari = document.getElementById('cari');
            var aktif = 'semua';

            function setChipStyle() {
                chips.forEach(function (c) {
                    var on = c.dataset.filter === aktif;
                    c.classList.toggle('bg-[#1B4332]', on);
                    c.classList.toggle('text-white', on);
                    c.classList.toggle('border-[#1B4332]', on);
                    c.classList.toggle('bg-white', !on);
                    c.classList.toggle('text-[#5C6B62]', !on);
                    c.classList.toggle('border-[#E1DCC9]', !on);
                });
            }

            function terapkan() {
                var kata = cari ? cari.value.trim().toLowerCase() : '';
                var tampil = 0;
                cards.forEach(function (card) {
                    var cocokKat = aktif === 'semua' || card.dataset.kategori === aktif;
                    var cocokKata = !kata || card.dataset.teks.indexOf(kata) !== -1;
                    var show = cocokKat && cocokKata;
                    card.classList.toggle('hidden', !show);
                    if (show) { tampil++; card.classList.add('in'); }
                });
                if (kosong) { kosong.classList.toggle('hidden', tampil !== 0); }
            }

            chips.forEach(function (chip) {
                chip.addEventListener('click', function () {
                    // klik kategori yang sedang aktif = batalkan, kembali ke Semua
                    aktif = (chip.dataset.filter === aktif && aktif !== 'semua') ? 'semua' : chip.dataset.filter;
                    setChipStyle();
                    terapkan();
                });
            });
            if (cari) { cari.addEventListener('input', terapkan); }

            // Tombol "Baca selengkapnya" hanya muncul kalau teks terpotong
            document.querySelectorAll('.isi-text').forEach(function (p) {
                var btn = p.parentElement.querySelector('.toggle-isi');
                if (!btn) { return; }
                if (p.scrollHeight > p.clientHeight + 1) { btn.classList.remove('hidden'); }
                btn.addEventListener('click', function () {
                    var terbuka = p.classList.toggle('isi-clamp');
                    btn.textContent = terbuka ? 'Baca selengkapnya' : 'Tutup';
                });
            });
        })();
    </script>

@endsection