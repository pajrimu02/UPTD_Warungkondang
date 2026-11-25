@extends('layouts.public')

@section('title', 'Kritik & Saran - UPTD Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(16px); transition: opacity .65s ease, transform .65s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        .hero-fade { animation: heroUp .85s ease both; }
        @keyframes heroUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: none; } }
        .isi-clamp { display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
            .hero-fade { animation: none; }
        }
    </style>

    @php
        $items = $data instanceof \Illuminate\Pagination\AbstractPaginator ? $data->getCollection() : collect($data);

        $kategoriMeta = [
            'surat'       => ['label' => 'Layanan Surat', 'icon' => 'bi-envelope-paper', 'warna' => 'bg-sky-50 text-sky-700 border-sky-200'],
            'pupuk_solar' => ['label' => 'Pupuk & Solar', 'icon' => 'bi-fuel-pump',      'warna' => 'bg-amber-50 text-amber-700 border-amber-200'],
            'lainnya'     => ['label' => 'Umum & Lainnya','icon' => 'bi-chat-dots',      'warna' => 'bg-[#EAF2EC] text-[#2D6A4F] border-[#C9DEC9]'],
        ];

        $total = $items->count();

        if (! auth()->check()) {
            $ctaUrl = route('login');
            $ctaLabel = 'Masuk untuk Tulis Ulasan & Masukan';
        } elseif (auth()->user()->role === 'admin') {
            $ctaUrl = route('admin.kritiksaran.index');
            $ctaLabel = 'Kelola Kritik & Saran';
        } else {
            $ctaUrl = route('user.kritiksaran.create');
            $ctaLabel = 'Tulis Kritik & Saran Anda';
        }

        $sensorNama = function (?string $nama): string {
            $nama = trim((string) $nama);
            if ($nama === '') {
                return 'Petani Warungkondang';
            }
            $kata = preg_split('/\s+/', $nama);
            return collect($kata)->map(function ($k) {
                $len = mb_strlen($k);
                return $len <= 1 ? $k . '***' : mb_substr($k, 0, 1) . str_repeat('*', min(4, $len - 1));
            })->implode(' ');
        };
    @endphp

    {{-- ================= HERO PUBLIK ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10 bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-4xl mx-auto px-6 py-16 md:py-20 text-center">
            <div class="hero-fade">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-medium px-4 py-1.5">
                    <i class="bi bi-chat-quote-fill text-[#E9C85A]"></i>
                    Forum Aspirasi & Ulasan Layanan Petani
                </span>

                <h1 class="font-serif text-4xl md:text-5xl leading-[1.12] mt-5">
                    Suara Petani untuk <span class="text-[#E9C85A]">Layanan Lebih Baik</span>
                </h1>
                <p class="mt-4 text-[#CFE3D6] leading-relaxed max-w-2xl mx-auto">
                    Baca ulasan, pertanyaan, dan masukan dari sesama petani Warungkondang beserta jawaban resmi langsung dari petugas UPTD.
                </p>

                <div class="mt-8 flex justify-center">
                    <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2.5 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3.5 rounded-full shadow-lg shadow-black/15 transition">
                        <i class="bi bi-pencil-square"></i> {{ $ctaLabel }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= RINGKASAN TOPIK ================= --}}
    <section class="relative -mt-8 z-10 max-w-4xl mx-auto reveal">
        <div class="bg-white border border-[#E1DCC9] rounded-3xl shadow-xl shadow-black/5 grid grid-cols-2 md:grid-cols-4 md:divide-x divide-[#E1DCC9] text-center">
            <div class="py-5 px-4">
                <p class="font-serif text-3xl text-[#1B4332]">{{ $total }}</p>
                <p class="text-xs font-medium text-[#5C6B62] mt-1">Utas Terjawab</p>
            </div>
            @foreach ($kategoriMeta as $key => $meta)
                <div class="py-5 px-4">
                    <p class="font-serif text-3xl text-[#1B4332]">{{ $items->where('kategori', $key)->count() }}</p>
                    <p class="text-xs font-medium text-[#5C6B62] mt-1">{{ $meta['label'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= UTAS KOMENTAR & BALASAN ================= --}}
    <section class="max-w-4xl mx-auto pt-12 pb-12">

        {{-- Bar Filter & Pencarian --}}
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-4 mb-6 flex flex-col md:flex-row md:items-center gap-3 reveal">
            <div class="flex flex-wrap gap-2" id="filter-chips">
                <button type="button" data-filter="semua"
                        class="chip inline-flex items-center gap-1.5 text-xs font-semibold px-4 py-2 rounded-full border border-[#1B4332] bg-[#1B4332] text-white transition">
                    Semua ({{ $total }})
                </button>
                @foreach ($kategoriMeta as $key => $meta)
                    <button type="button" data-filter="{{ $key }}"
                            class="chip inline-flex items-center gap-1.5 text-xs font-semibold px-4 py-2 rounded-full border border-[#E1DCC9] bg-[#FAF9F4] text-[#5C6B62] hover:border-[#2D6A4F] transition">
                        <i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                    </button>
                @endforeach
            </div>

            <div class="relative md:ml-auto md:w-64">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#5C6B62]"></i>
                <input type="text" id="cari" placeholder="Cari topik atau jawaban..."
                       class="w-full pl-9 pr-4 py-2 rounded-full border border-[#E1DCC9] bg-[#FAF9F4] focus:bg-white text-xs focus:border-[#2D6A4F] focus:ring-[#2D6A4F]">
            </div>
        </div>

        @if ($items->isEmpty())
            <div class="bg-white border border-[#E1DCC9] rounded-3xl px-6 py-16 text-center reveal">
                <span class="w-16 h-16 rounded-full bg-[#EAF2EC] text-[#2D6A4F] flex items-center justify-center text-2xl mx-auto">
                    <i class="bi bi-chat-square-heart"></i>
                </span>
                <p class="font-serif text-xl text-[#1B4332] mt-4">Belum ada ulasan yang ditampilkan</p>
                <p class="text-sm text-[#5C6B62] mt-1 max-w-md mx-auto">
                    Jadilah petani pertama yang membagikan pengalaman atau pertanyaan layanan. Setelah ditanggapi petugas, utas diskusi akan tampil di halaman ini.
                </p>
                <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-2.5 rounded-full mt-6">
                    {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        @else
            <div class="space-y-5" id="grid-kritik">
                @foreach ($items as $item)
                    @php
                        $meta = $kategoriMeta[$item->kategori] ?? $kategoriMeta['lainnya'];
                        $namaTampil = $sensorNama($item->user?->name);
                        $inisial = mb_strtoupper(mb_substr($namaTampil, 0, 1));
                    @endphp
                    <article
                        class="kritik-card bg-white border border-[#E1DCC9] rounded-3xl p-5 sm:p-7 hover:border-[#2D6A4F]/60 shadow-sm transition reveal"
                        data-kategori="{{ $item->kategori }}"
                        data-teks="{{ strtolower($item->isi.' '.$item->tanggapan_admin) }}"
                    >
                        {{-- Komentar Utama dari Petani --}}
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 shrink-0 rounded-full bg-[#F6EDC9] text-[#7A5C12] font-bold text-sm flex items-center justify-center">
                                {{ $inisial }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-semibold text-sm text-[#14231C]">{{ $namaTampil }}</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium px-2.5 py-0.5 rounded-full bg-[#F5F3EC] text-[#5C6B62]">
                                            <i class="bi bi-person-check text-[#2D6A4F]"></i> Petani Terdaftar
                                        </span>
                                    </div>
                                    <span class="text-xs text-[#8C9086]">{{ $item->created_at->diffForHumans() }} · {{ $item->created_at->translatedFormat('d M Y') }}</span>
                                </div>

                                {{-- Tag Kategori Topik --}}
                                <div class="mt-2">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold rounded-full px-2.5 py-0.5 border {{ $meta['warna'] }}">
                                        <i class="bi {{ $meta['icon'] }}"></i> {{ $meta['label'] }}
                                    </span>
                                </div>

                                {{-- Isi Kritik / Saran --}}
                                <p class="isi-text isi-clamp text-[#14231C] text-[15px] leading-relaxed mt-3 whitespace-pre-line">{{ $item->isi }}</p>
                                <button type="button" class="toggle-isi hidden text-xs font-semibold text-[#2D6A4F] hover:underline mt-1.5">Baca selengkapnya</button>
                            </div>
                        </div>

                        {{-- Balasan Resmi UPTD (Model Nested Reply seperti Ulasan Penjual) --}}
                        @if ($item->tanggapan_admin)
                            <div class="mt-4 ml-4 sm:ml-12 pl-4 sm:pl-5 border-l-2 border-[#2D6A4F]/40">
                                <div class="rounded-2xl bg-[#F4F8F5] border border-[#DCE8DF] p-4 sm:p-5">
                                    <div class="flex items-center justify-between gap-2 flex-wrap">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-[#1B4332] text-white text-xs flex items-center justify-center">
                                                <i class="bi bi-patch-check-fill"></i>
                                            </span>
                                            <span class="text-xs font-bold text-[#1B4332]">Respon Resmi UPTD Warungkondang</span>
                                        </div>
                                        <span class="text-[11px] text-[#5C6B62]">Dibalas {{ $item->updated_at->translatedFormat('d M Y') }}</span>
                                    </div>

                                    <p class="isi-text isi-clamp text-sm text-[#14231C] leading-relaxed mt-2.5 whitespace-pre-line">{{ $item->tanggapan_admin }}</p>
                                    <button type="button" class="toggle-isi hidden text-xs font-semibold text-[#2D6A4F] hover:underline mt-1.5">Baca selengkapnya</button>
                                </div>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            {{-- State Kosong Hasil Filter --}}
            <div id="filter-kosong" class="hidden bg-white border border-[#E1DCC9] rounded-3xl px-6 py-14 text-center">
                <i class="bi bi-search text-3xl text-[#5C6B62]"></i>
                <p class="font-semibold text-[#14231C] mt-3">Tidak ada ulasan yang cocok</p>
                <p class="text-sm text-[#5C6B62] mt-1">Coba pilih kategori lain atau hapus kata kunci pencarian.</p>
            </div>

            @if ($data instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="mt-8">{{ $data->links() }}</div>
            @endif
        @endif
    </section>

    {{-- ================= CTA BAWAH ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden mt-4">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-4xl mx-auto px-6 py-14 text-center reveal">
            <h2 class="font-serif text-3xl leading-tight">Punya pengalaman atau kendala layanan?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-lg mx-auto">
                Sampaikan langsung kepada kami. Masukan Anda membantu meningkatkan kualitas pelayanan pertanian di Kecamatan Warungkondang.
            </p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3 rounded-full transition">
                    {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('hubungi') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-7 py-3 rounded-full transition">
                    Kontak & Lokasi UPTD
                </a>
            </div>
        </div>
    </section>

    <script>
        (function () {
            var els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                els.forEach(function (e) { e.classList.add('in'); });
            } else {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (en) {
                        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
                    });
                }, { threshold: 0.1 });
                els.forEach(function (e) { io.observe(e); });
            }

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
                    c.classList.toggle('bg-[#FAF9F4]', !on);
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
                    aktif = (chip.dataset.filter === aktif && aktif !== 'semua') ? 'semua' : chip.dataset.filter;
                    setChipStyle();
                    terapkan();
                });
            });
            if (cari) { cari.addEventListener('input', terapkan); }

            document.querySelectorAll('.isi-text').forEach(function (p) {
                var btn = p.parentElement.querySelector('.toggle-isi');
                if (!btn) { return; }
                if (p.scrollHeight > p.clientHeight + 1) { btn.classList.remove('hidden'); }
                btn.addEventListener('click', function () {
                    var terbuka = p.classList.toggle('isi-clamp');
                    btn.textContent = terbuka ? 'Baca selengkapnya' : 'Sembunyikan';
                });
            });
        })();
    </script>

@endsection