@extends('layouts.public')

@section('title', 'Beranda - UPTD Pertanian Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }
        .hero-fade { animation: heroUp .9s ease both; }
        @keyframes heroUp { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: none; } }
        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
            .hero-fade { animation: none; }
        }
    </style>

    @php
        // ------------------------------------------------------------------
        // KONTEN SEMENTARA — dipakai selama admin belum mengisi data.
        // Setelah ada data di database, bagian ini otomatis tidak tampil.
        // ------------------------------------------------------------------
        $infoContoh = [
            ['tag' => 'Solar', 'icon' => 'bi-fuel-pump',
             'judul' => 'Syarat Pengajuan Surat Rekomendasi Solar',
             'isi' => 'Siapkan KTP, Surat Keterangan Usaha kelompok tani atau pelaku usaha, dan foto alat/mesin pertanian yang digunakan.'],
            ['tag' => 'Larangan', 'icon' => 'bi-exclamation-triangle',
             'judul' => 'Penyalahgunaan Solar Bersubsidi',
             'isi' => 'Solar bersubsidi hanya untuk kegiatan pertanian sesuai surat rekomendasi. Dilarang diperjualbelikan atau dipakai di luar peruntukan.'],
            ['tag' => 'Pupuk', 'icon' => 'bi-flower1',
             'judul' => 'Pengajuan RDKK Pupuk Bersubsidi',
             'isi' => 'Pengajuan RDKK dilakukan melalui PPL atau kelompok tani, lalu diinput ke sistem e-RDKK resmi Kementerian Pertanian.'],
        ];

        $edukasiContoh = [
            ['tag' => 'Pupuk', 'icon' => 'bi-flower2', 'grad' => 'from-[#1B4332] to-[#2D6A4F]',
             'judul' => 'Cara Menggunakan Pupuk Bersubsidi Secara Tepat',
             'ringkas' => 'Dosis, waktu, dan cara aplikasi yang benar agar pupuk efektif dan tidak terbuang.'],
            ['tag' => 'Alat & Mesin', 'icon' => 'bi-gear-wide-connected', 'grad' => 'from-[#2D6A4F] to-[#7FA650]',
             'judul' => 'Merawat Alat dan Mesin Pertanian agar Hemat BBM',
             'ringkas' => 'Perawatan rutin traktor dan mesin penggiling yang menjaga performa dan menekan konsumsi solar.'],
            ['tag' => 'Musim Tanam', 'icon' => 'bi-calendar2-check', 'grad' => 'from-[#7A5C12] to-[#C9A227]',
             'judul' => 'Mengenal Siklus Tanam dan Waktu Pengajuan Rekomendasi',
             'ringkas' => 'Atur jadwal pengajuan surat rekomendasi solar mengikuti siklus tanam setiap musim.'],
        ];

        // Info: pakai data database kalau ada, kalau kosong pakai contoh
        $infoList = collect($infoTerbaru ?? [])->take(3)->map(fn ($i) => [
            'tag' => $i->jenis ?? $i->kategori ?? 'Informasi',
            'icon' => 'bi-megaphone',
            'judul' => $i->judul,
            'isi' => $i->isi,
        ])->values();
        if ($infoList->isEmpty()) { $infoList = collect($infoContoh); }

        // Edukasi: sama
        $grads = ['from-[#1B4332] to-[#2D6A4F]', 'from-[#2D6A4F] to-[#7FA650]', 'from-[#7A5C12] to-[#C9A227]'];
        $edukasiList = collect($edukasiTerbaru ?? [])->take(3)->map(fn ($a, $n) => [
            'tag' => 'Artikel',
            'icon' => 'bi-book',
            'grad' => $grads[$n % 3],
            'judul' => $a->judul,
            'ringkas' => \Illuminate\Support\Str::limit(strip_tags($a->konten ?? ''), 95),
            'gambar' => $a->gambar ? asset('storage/'.$a->gambar) : null,
            'url' => route('infopublik.edukasi.show', $a->slug),
        ])->values();
        if ($edukasiList->isEmpty()) {
            $edukasiList = collect($edukasiContoh)->map(fn ($e) => $e + ['gambar' => null, 'url' => route('infopublik.edukasi')]);
        }

        $tagColor = fn ($t) => match (strtolower($t)) {
            'solar' => 'bg-amber-50 text-amber-700',
            'larangan' => 'bg-red-50 text-red-700',
            'pupuk' => 'bg-emerald-50 text-emerald-700',
            default => 'bg-[#EAF2EC] text-[#2D6A4F]',
        };

        // Tombol CTA menyesuaikan status login
        if (! auth()->check()) {
            $ctaUrl = route('register'); $ctaLabel = 'Daftar Sekarang';
        } elseif (auth()->user()->role === 'admin') {
            $ctaUrl = route('admin.dashboard'); $ctaLabel = 'Buka Dashboard';
        } else {
            $ctaUrl = route('user.surat-solar.create'); $ctaLabel = 'Ajukan Sekarang';
        }
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10 z-0">

        <div class="relative h-[78vh] md:h-[88vh] min-h-[540px]">
            <img src="{{ asset('storage/home/uptd.png') }}" alt="Kantor UPTD Pertanian Warungkondang"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0F1F17]/90 via-[#0F1F17]/55 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F1F17]/85 via-transparent to-transparent"></div>

            <div class="relative z-10 h-full flex items-center px-6 md:px-16 pb-6 md:pb-24">
                <div class="max-w-xl hero-fade">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-medium px-3.5 py-1.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C9A227] opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#C9A227]"></span>
                        </span>
                        Layanan Digital Resmi · BPP Kec. Warungkondang
                    </span>

                    <h1 class="font-serif text-4xl md:text-6xl text-white leading-[1.1] mt-5">
                        Melayani petani, <span class="text-[#E9C85A]">lebih dekat</span> dan lebih cepat
                    </h1>
                    <p class="mt-5 text-[#E4EAE1] max-w-md leading-relaxed">
                        Ajukan surat rekomendasi solar, pantau informasi pupuk & kuota, dan akses edukasi pertanian dalam satu layanan digital.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="{{ route('infopublik.pelayanan') }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-6 py-3 rounded-full transition">
                            Lihat Pelayanan <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('infopublik.stokkuota') }}" class="inline-flex items-center gap-2 border border-white/70 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-full transition">
                            Info Pupuk & Solar
                        </a>
                    </div>

                    <ul class="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-xs text-white/80">
                        <li class="flex items-center gap-1.5"><i class="bi bi-check-circle-fill text-[#C9A227]"></i> Tanpa pungutan biaya</li>
                        <li class="flex items-center gap-1.5"><i class="bi bi-check-circle-fill text-[#C9A227]"></i> Status pengajuan real-time</li>
                        <li class="flex items-center gap-1.5"><i class="bi bi-check-circle-fill text-[#C9A227]"></i> Dikelola resmi oleh UPTD</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Kartu layanan: mobile di bawah foto, desktop mengambang --}}
        <div class="relative px-6 py-8 md:py-0
                    md:absolute md:left-0 md:right-0 md:bottom-0 md:translate-y-1/2 md:z-10
                    bg-[#F5F3EC] md:bg-transparent">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach ([
                        ['bi-fuel-pump', 'Solar & Pupuk Bersubsidi', 'Pantau informasi kuota pupuk dan solar terbaru.', route('infopublik.stokkuota')],
                        ['bi-lightning-charge', 'Layanan Cepat', 'Ajukan surat rekomendasi online, tanpa antre.', route('infopublik.pelayanan')],
                        ['bi-people', 'Pendampingan Petani', 'Bimbingan teknis dari penyuluh pertanian lapangan.', route('hubungi')],
                        ['bi-book', 'Edukasi Pertanian', 'Artikel dan materi edukasi untuk kelompok tani.', route('infopublik.edukasi')],
                    ] as [$icon, $title, $desc, $url])
                        <a href="{{ $url }}" class="group relative block bg-white rounded-2xl p-6 pt-7 shadow-xl shadow-black/10 border border-[#E1DCC9] overflow-hidden transition hover:-translate-y-1 hover:shadow-2xl">
                            <span class="absolute top-0 left-0 h-1 w-0 bg-[#C9A227] transition-all duration-300 group-hover:w-full"></span>
                            <span class="w-11 h-11 rounded-full bg-[#EAF2EC] text-[#1B4332] group-hover:bg-[#1B4332] group-hover:text-white flex items-center justify-center text-lg transition-colors">
                                <i class="bi {{ $icon }}"></i>
                            </span>
                            <p class="font-semibold text-[#14231C] text-sm mt-4">{{ $title }}</p>
                            <p class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed">{{ $desc }}</p>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-3">Selengkapnya <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="hidden md:block md:h-28"></div>

    {{-- ================= STATISTIK ================= --}}
    {{-- CATATAN: angka di bawah masih contoh. Ganti dengan data asli sebelum launching. --}}
    <section class="py-8 reveal">
        <div class="bg-white border border-[#E1DCC9] rounded-3xl shadow-sm grid grid-cols-2 md:grid-cols-4 md:divide-x divide-[#E1DCC9] text-center">
            @foreach ([
                ['120+', 'Kelompok Tani Terlayani'],
                ['350+', 'Pengajuan Diproses'],
                ['4', 'Desa Cakupan Wilayah'],
                ['100%', 'Digital & Transparan'],
            ] as [$angka, $label])
                <div class="py-7 px-4">
                    <p class="font-serif text-3xl md:text-4xl text-[#1B4332]">{{ $angka }}</p>
                    <p class="text-sm text-[#5C6B62] mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= CARA MENGAJUKAN ================= --}}
    <section class="py-14 reveal">
        <div class="text-center max-w-xl mx-auto">
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">ALUR PENGAJUAN</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Empat langkah mudah mendapatkan surat rekomendasi solar</h2>
        </div>

        <div class="relative mt-12">
            <div class="hidden md:block absolute top-7 left-[12.5%] right-[12.5%] border-t-2 border-dashed border-[#D8D2B8]"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                @foreach ([
                    ['bi-person-plus', 'Daftar Akun', 'Buat akun dengan NIK, No. HP, dan pilih kelompok tani Anda.'],
                    ['bi-pencil-square', 'Isi Form & Unggah Berkas', 'Lengkapi data alat/mesin, lalu unggah KTP, SKU, dan foto mesin.'],
                    ['bi-patch-check', 'Verifikasi', 'Sistem menyaring kelayakan awal, lalu petugas UPTD meninjau pengajuan.'],
                    ['bi-file-earmark-check', 'Surat Resmi Terbit', 'Lihat status dan unduh surat rekomendasi untuk pembelian solar bersubsidi.'],
                ] as $n => [$icon, $title, $desc])
                    <div class="relative text-center">
                        <div class="relative mx-auto w-14 h-14 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-xl ring-8 ring-[#F5F3EC] z-10">
                            <i class="bi {{ $icon }}"></i>
                            <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-[#C9A227] text-[#1B4332] text-xs font-bold flex items-center justify-center">{{ $n + 1 }}</span>
                        </div>
                        <p class="font-semibold text-[#14231C] mt-5">{{ $title }}</p>
                        <p class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed max-w-[15rem] mx-auto">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="text-center mt-10">
            <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#1B4332] hover:bg-[#153629] text-white font-semibold px-7 py-3 rounded-full transition">
                {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>

    {{-- ================= KENAPA ================= --}}
    <section class="py-10 grid md:grid-cols-2 gap-10 items-center reveal">
        <div>
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">MENGAPA UPTD WARUNGKONDANG</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Layanan yang dirancang agar petani tidak perlu bolak-balik kantor</h2>
            <p class="mt-4 text-[#5C6B62] leading-relaxed">Proses administrasi yang tadinya manual kini digabung dalam satu sistem digital, sehingga pengajuan bisa dipantau kapan saja tanpa harus datang berulang kali.</p>
        </div>
        <div class="space-y-4">
            @foreach ([
                ['bi-shield-check', 'Transparan', 'Status pengajuan bisa dipantau langsung oleh pemohon, real-time.'],
                ['bi-clock-history', 'Hemat Waktu', 'Formulir dan dokumen dikirim online, tidak perlu antre di kantor.'],
                ['bi-geo-alt', 'Menjangkau Semua Desa', 'Melayani seluruh kelompok tani di wilayah Kecamatan Warungkondang.'],
            ] as [$icon, $title, $desc])
                <div class="flex gap-4 items-start bg-white border border-[#E1DCC9] rounded-2xl p-5 hover:border-[#2D6A4F] transition">
                    <span class="w-11 h-11 shrink-0 rounded-xl bg-[#1B4332] text-white flex items-center justify-center">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-[#14231C]">{{ $title }}</p>
                        <p class="text-sm text-[#5C6B62] mt-1">{{ $desc }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= INFORMASI TERBARU ================= --}}
    <section class="py-14 reveal">
        <div class="flex items-end justify-between mb-7 flex-wrap gap-3">
            <div>
                <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Informasi Terbaru</h2>
                <p class="text-sm text-[#5C6B62] mt-1">Aturan, ketentuan, dan pengumuman resmi seputar pupuk dan solar.</p>
            </div>
            <a href="{{ route('infopublik.informasi') }}" class="text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227] pb-0.5">Lihat semua →</a>
        </div>

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
            @foreach ($infoList as $item)
                <a href="{{ route('infopublik.informasi') }}" class="group block bg-white border border-[#E1DCC9] rounded-2xl p-6 hover:-translate-y-1 hover:shadow-lg hover:border-[#2D6A4F] transition">
                    <div class="flex items-center justify-between">
                        <span class="w-11 h-11 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg">
                            <i class="bi {{ $item['icon'] }}"></i>
                        </span>
                        <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-1 {{ $tagColor($item['tag']) }}">{{ $item['tag'] }}</span>
                    </div>
                    <h3 class="font-semibold text-[#14231C] mt-4 leading-snug">{{ $item['judul'] }}</h3>
                    <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed line-clamp-3">{{ $item['isi'] }}</p>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-4">Baca selengkapnya <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i></span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= EDUKASI ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
        <div class="max-w-6xl mx-auto px-6 reveal">
            <div class="flex items-end justify-between mb-7 flex-wrap gap-3">
                <div>
                    <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Edukasi Pertanian</h2>
                    <p class="text-sm text-[#5C6B62] mt-1">Tips dan artikel seputar budi daya, pupuk, dan alat mesin pertanian.</p>
                </div>
                <a href="{{ route('infopublik.edukasi') }}" class="text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227] pb-0.5">Baca semua →</a>
            </div>

            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
                @foreach ($edukasiList as $e)
                    <a href="{{ $e['url'] }}" class="group block bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden hover:-translate-y-1 hover:shadow-lg transition">
                        <div class="relative h-40 overflow-hidden">
                            @if ($e['gambar'])
                                <img src="{{ $e['gambar'] }}" alt="{{ $e['judul'] }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            @else
                                <div class="w-full h-full bg-gradient-to-br {{ $e['grad'] }} flex items-center justify-center">
                                    <i class="bi {{ $e['icon'] }} text-6xl text-white/25 transition-transform duration-500 group-hover:scale-110"></i>
                                </div>
                            @endif
                            <span class="absolute top-3 left-3 text-[11px] font-semibold rounded-full bg-white/95 text-[#1B4332] px-2.5 py-1">{{ $e['tag'] }}</span>
                        </div>
                        <div class="p-5">
                            <h3 class="font-semibold text-[#14231C] leading-snug">{{ $e['judul'] }}</h3>
                            <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed line-clamp-2">{{ $e['ringkas'] }}</p>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-4">Baca artikel <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= CUPLIKAN GALERI ================= --}}
    <section class="py-16 reveal">
        <div class="flex items-end justify-between mb-7 flex-wrap gap-3">
            <div>
                <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Dokumentasi Kegiatan</h2>
                <p class="text-sm text-[#5C6B62] mt-1">Potret pelayanan dan kegiatan UPTD bersama petani.</p>
            </div>
            <a href="{{ route('galeri.foto') }}" class="text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227] pb-0.5">Lihat galeri →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 auto-rows-[140px] md:auto-rows-[200px] gap-3 md:gap-4">
            @foreach ([1, 2, 3, 4, 5] as $n)
                <a href="{{ route('galeri.foto') }}"
                   class="group relative overflow-hidden rounded-2xl bg-[#E1DCC9] {{ $n === 1 ? 'col-span-2 row-span-2' : '' }}">
                    <img src="{{ asset('storage/galeri/'.$n.'.jpg') }}" alt="Dokumentasi kegiatan UPTD {{ $n }}" loading="lazy"
                         onerror="this.parentElement.style.display='none'"
                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#14231C]/70 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                        <span class="text-white text-xs font-medium inline-flex items-center gap-1.5"><i class="bi bi-images"></i> Lihat galeri</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= CTA + SOSMED ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <p class="text-[#C9E4B8] font-semibold text-sm tracking-wide">SIAP MENGAJUKAN?</p>
            <h2 class="font-serif text-3xl md:text-4xl mt-2 leading-tight">Urus surat rekomendasi solar dan pupuk tanpa antre</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Daftar, lengkapi data, dan pantau statusnya dari mana saja.</p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="{{ $ctaUrl }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-7 py-3 rounded-full transition">
                    {{ $ctaLabel }} <i class="bi bi-arrow-right"></i>
                </a>
                <a href="{{ route('hubungi') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-7 py-3 rounded-full transition">
                    Hubungi Kami
                </a>
            </div>

            <div class="border-t border-white/10 mt-12 pt-8">
                <p class="text-sm text-[#CFE3D6]">Ikuti kabar terbaru UPTD Warungkondang</p>
                <div class="flex justify-center gap-3 mt-4">
                    @foreach ([
                        ['bi-facebook', 'Facebook', '#'],
                        ['bi-instagram', 'Instagram', '#'],
                        ['bi-youtube', 'YouTube', '#'],
                        ['bi-whatsapp', 'WhatsApp', 'https://wa.me/62xxxxxxxxxx'],
                    ] as [$ico, $nama, $link])
                        <a href="{{ $link }}" aria-label="{{ $nama }}" class="w-11 h-11 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center text-lg transition">
                            <i class="bi {{ $ico }}"></i>
                        </a>
                    @endforeach
                </div>
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