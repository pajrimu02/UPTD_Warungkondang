@extends('layouts.public')

@section('title', 'Pelayanan - UPTD Pelayanan Pertanian Warungkondang')

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
        // CTA solar menyesuaikan status login
        if (! auth()->check()) {
            $ajukanUrl = route('login'); $ajukanLabel = 'Masuk untuk Mengajukan';
        } elseif (auth()->user()->role === 'admin') {
            $ajukanUrl = route('admin.dashboard'); $ajukanLabel = 'Buka Dashboard Admin';
        } else {
            $ajukanUrl = route('user.surat-solar.create'); $ajukanLabel = 'Ajukan Sekarang';
        }

        // Persyaratan solar (sesuai form pengajuan di sistem)
        $persyaratanSolar = [
            ['bi-person-vcard', 'KTP pemohon', 'Foto atau scan KTP yang masih berlaku.'],
            ['bi-file-earmark-text', 'Surat Keterangan Usaha (SKU)', 'Dari kelompok tani atau pelaku usaha pertanian.'],
            ['bi-camera', 'Foto alat/mesin pertanian', 'Traktor, pompa air, RMU, mesin pemotong rumput, power thresher, dan sejenisnya.'],
        ];

        // Alur pupuk subsidi (informasi, bukan form di website ini)
        // TODO: konfirmasi detail alur ini dengan UPTD sebelum launching
        $alurPupuk = [
            ['bi-people', 'Melalui kelompok tani', 'Kebutuhan pupuk disampaikan lewat kelompok tani dan penyuluh pertanian lapangan (PPL).'],
            ['bi-clipboard-data', 'Tercatat di sistem e-RDKK', 'Data kebutuhan direkap dan diinput ke sistem e-RDKK resmi Kementerian Pertanian.'],
            ['bi-box-seam', 'Pantau kuota & jadwal', 'Cek informasi kuota dan jadwal distribusi pupuk di halaman Stok & Kuota.'],
        ];

        // Layanan lain: pakai data database kalau ada (solar & pupuk disembunyikan karena sudah jadi layanan utama)
        $layananDb = collect($daftarLayanan ?? [])
            ->reject(fn ($l) => str_contains(strtolower($l->nama_layanan), 'solar')
                             || str_contains(strtolower($l->nama_layanan), 'pupuk'))
            ->map(fn ($l) => [
                'icon' => str_starts_with((string) $l->icon, 'bi-') ? $l->icon : 'bi-file-earmark-text',
                'judul' => $l->nama_layanan,
                'isi' => $l->deskripsi,
                'url' => null,
                'label' => null,
            ])->values();

        $layananBawaan = collect([
            ['icon' => 'bi-box-seam', 'judul' => 'Informasi Stok & Kuota',
             'isi' => 'Pantau informasi kuota dan jadwal distribusi pupuk dan solar yang diperbarui oleh admin UPTD.',
             'url' => route('infopublik.stokkuota'), 'label' => 'Lihat stok & kuota'],
            ['icon' => 'bi-shield-exclamation', 'judul' => 'Aturan & Larangan',
             'isi' => 'Ketentuan penggunaan BBM dan pupuk bersubsidi, termasuk larangan penyalahgunaan dan sanksinya.',
             'url' => route('infopublik.informasi'), 'label' => 'Baca ketentuan'],
            ['icon' => 'bi-people', 'judul' => 'Pendampingan Petani',
             'isi' => 'Pendampingan, informasi, dan penyuluhan dari penyuluh pertanian lapangan untuk petani dan kelompok tani.',
             'url' => route('hubungi'), 'label' => 'Hubungi kami'],
            ['icon' => 'bi-book', 'judul' => 'Edukasi Pertanian',
             'isi' => 'Artikel dan materi edukasi seputar budi daya, penggunaan pupuk, dan perawatan alat mesin pertanian.',
             'url' => route('infopublik.edukasi'), 'label' => 'Baca edukasi'],
            ['icon' => 'bi-megaphone', 'judul' => 'Kritik & Saran',
             'isi' => 'Sampaikan masukan untuk perbaikan pelayanan UPTD. Pengiriman dilakukan melalui akun yang sudah login.',
             'url' => auth()->check() && auth()->user()->role !== 'admin' ? route('user.kritiksaran.create') : route('kritiksaran.publik'),
             'label' => 'Sampaikan masukan'],
            ['icon' => 'bi-question-circle', 'judul' => 'Pertanyaan Umum (FAQ)',
             'isi' => 'Jawaban atas pertanyaan yang sering diajukan seputar pupuk, solar, dan surat rekomendasi.',
             'url' => route('faq'), 'label' => 'Buka FAQ'],
        ]);

        $layananList = $layananDb->isNotEmpty() ? $layananDb : $layananBawaan;
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
                    <span class="text-white">Pelayanan</span>
                </nav>
                <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Pelayanan</h1>
                <p class="mt-3 text-[#E9C85A] font-medium text-lg">Cepat, tepat, transparan, dan tanpa pungutan</p>
                <p class="mt-2 text-white/75 text-sm max-w-lg">Rekomendasi solar dan pupuk bersubsidi untuk petani dan kelompok tani Warungkondang.</p>
            </div>
        </div>
    </section>

    {{-- ================= LAYANAN UTAMA ================= --}}
    <section class="py-16 reveal">
        <div class="text-center max-w-xl mx-auto mb-10">
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">LAYANAN UTAMA</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Rekomendasi solar dan pupuk bersubsidi</h2>
        </div>

        <div class="grid lg:grid-cols-2 gap-6 items-stretch">

            {{-- ===== SOLAR ===== --}}
            <div class="flex flex-col bg-white border border-[#E1DCC9] rounded-3xl overflow-hidden shadow-sm">
                <div class="relative bg-[#1B4332] text-white p-8 overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-52 h-52 rounded-full bg-[#2D6A4F]/50"></div>
                    <div class="absolute -bottom-20 -left-12 w-56 h-56 rounded-full bg-[#C9A227]/10"></div>

                    <div class="relative">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 text-xs font-medium px-3 py-1.5">
                            <i class="bi bi-laptop text-[#E9C85A]"></i> Diajukan online
                        </span>

                        <div class="flex items-center gap-4 mt-5">
                            <span class="w-14 h-14 shrink-0 rounded-2xl bg-[#C9A227] text-[#1B4332] flex items-center justify-center text-2xl">
                                <i class="bi bi-fuel-pump"></i>
                            </span>
                            <h3 class="font-serif text-2xl leading-snug">Rekomendasi BBM Solar Bersubsidi</h3>
                        </div>

                        <p class="text-[#CFE3D6] mt-5 leading-relaxed text-sm">
                            Surat rekomendasi pembelian solar bersubsidi untuk kegiatan pertanian, seperti traktor, penggilingan padi (RMU),
                            pompa air, dan alat/mesin pertanian lainnya.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ $ajukanUrl }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-6 py-3 rounded-full transition">
                                {{ $ajukanLabel }} <i class="bi bi-arrow-right"></i>
                            </a>
                            @guest
                                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-full transition">
                                    Daftar Akun
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>

                <div class="p-7 flex-1">
                    <p class="text-[#2D6A4F] font-semibold text-xs tracking-wide">PERSYARATAN BERKAS</p>
                    <ul class="mt-4 space-y-4">
                        @foreach ($persyaratanSolar as [$icon, $judul, $isi])
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

                    <p class="mt-5 text-xs text-[#5C6B62] bg-[#F5F3EC] rounded-xl px-4 py-3 leading-relaxed">
                        <i class="bi bi-info-circle text-[#2D6A4F]"></i>
                        Format file JPG, PNG, atau PDF, maksimal 2 MB per berkas.
                    </p>
                </div>
            </div>

            {{-- ===== PUPUK ===== --}}
            <div class="flex flex-col bg-white border border-[#E1DCC9] rounded-3xl overflow-hidden shadow-sm">
                <div class="relative bg-[#2D6A4F] text-white p-8 overflow-hidden">
                    <div class="absolute -top-16 -right-16 w-52 h-52 rounded-full bg-[#7FA650]/40"></div>
                    <div class="absolute -bottom-20 -left-12 w-56 h-56 rounded-full bg-[#C9A227]/15"></div>

                    <div class="relative">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 border border-white/20 text-xs font-medium px-3 py-1.5">
                            <i class="bi bi-diagram-3 text-[#E9C85A]"></i> Melalui kelompok tani & PPL
                        </span>

                        <div class="flex items-center gap-4 mt-5">
                            <span class="w-14 h-14 shrink-0 rounded-2xl bg-[#C9A227] text-[#1B4332] flex items-center justify-center text-2xl">
                                <i class="bi bi-flower1"></i>
                            </span>
                            <h3 class="font-serif text-2xl leading-snug">Rekomendasi Pupuk Bersubsidi</h3>
                        </div>

                        <p class="text-[#DCEBDD] mt-5 leading-relaxed text-sm">
                            Layanan rekomendasi kebutuhan pupuk bersubsidi bagi petani dan kelompok tani, mengikuti ketentuan alokasi
                            dan sistem e-RDKK yang berlaku.
                        </p>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('infopublik.stokkuota') }}" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-6 py-3 rounded-full transition">
                                Lihat Kuota Pupuk <i class="bi bi-arrow-right"></i>
                            </a>
                            <a href="{{ route('hubungi') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-full transition">
                                Tanya Petugas
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-7 flex-1">
                    <p class="text-[#2D6A4F] font-semibold text-xs tracking-wide">ALUR PENGAJUAN</p>
                    <ul class="mt-4 space-y-4">
                        @foreach ($alurPupuk as [$icon, $judul, $isi])
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

                    <p class="mt-5 text-xs text-[#5C6B62] bg-[#F5F3EC] rounded-xl px-4 py-3 leading-relaxed">
                        <i class="bi bi-info-circle text-[#2D6A4F]"></i>
                        Pengajuan pupuk tidak dilakukan lewat website ini. Hubungi kelompok tani atau PPL di wilayah Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= LAYANAN LAINNYA ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
        <div class="max-w-6xl mx-auto px-6 reveal">
            <div class="text-center max-w-xl mx-auto">
                <p class="text-[#2D6A4F] font-semibold text-sm mb-2">LAYANAN & INFORMASI</p>
                <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Layanan lain yang tersedia</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 mt-10">
                @foreach ($layananList as $l)
                    @php $tag = $l['url'] ? 'a' : 'div'; @endphp
                    <{{ $tag }} @if ($l['url']) href="{{ $l['url'] }}" @endif
                        class="group block bg-white border border-[#E1DCC9] rounded-2xl p-6 transition hover:-translate-y-1 hover:shadow-lg hover:border-[#2D6A4F]">
                        <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#1B4332] group-hover:bg-[#1B4332] group-hover:text-white flex items-center justify-center text-xl transition-colors">
                            <i class="bi {{ $l['icon'] }}"></i>
                        </span>
                        <h3 class="font-semibold text-[#14231C] mt-5">{{ $l['judul'] }}</h3>
                        <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">{{ $l['isi'] }}</p>
                        @if ($l['url'])
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-4">
                                {{ $l['label'] }} <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                            </span>
                        @endif
                    </{{ $tag }}>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= ALUR SOLAR ================= --}}
    <section class="py-16 reveal">
        <div class="text-center max-w-xl mx-auto">
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">ALUR PENGAJUAN SOLAR</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Empat langkah mengurus surat rekomendasi solar</h2>
        </div>

        <div class="relative mt-12">
            <div class="hidden md:block absolute top-7 left-[12.5%] right-[12.5%] border-t-2 border-dashed border-[#D8D2B8]"></div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                @foreach ([
                    ['bi-person-plus', 'Daftar Akun', 'Buat akun dengan NIK, No. HP, dan pilih kelompok tani Anda.'],
                    ['bi-pencil-square', 'Isi Form & Unggah Berkas', 'Lengkapi data alat/mesin dan kebutuhan BBM, lalu unggah berkas.'],
                    ['bi-patch-check', 'Verifikasi', 'Sistem menyaring kelayakan awal, lalu petugas UPTD meninjau pengajuan.'],
                    ['bi-file-earmark-check', 'Surat Resmi Terbit', 'Pantau status dan unduh surat rekomendasi setelah disetujui.'],
                ] as $n => [$icon, $judul, $isi])
                    <div class="relative text-center">
                        <div class="relative mx-auto w-14 h-14 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-xl ring-8 ring-[#F5F3EC] z-10">
                            <i class="bi {{ $icon }}"></i>
                            <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-[#C9A227] text-[#1B4332] text-xs font-bold flex items-center justify-center">{{ $n + 1 }}</span>
                        </div>
                        <p class="font-semibold text-[#14231C] mt-5">{{ $judul }}</p>
                        <p class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed max-w-[15rem] mx-auto">{{ $isi }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= KETENTUAN PENTING ================= --}}
    <section class="pb-16 reveal">
        <div class="grid md:grid-cols-2 gap-5">
            <div class="bg-white border border-[#E1DCC9] rounded-3xl p-7">
                <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-xl">
                    <i class="bi bi-arrow-repeat"></i>
                </span>
                <h3 class="font-serif text-xl text-[#1B4332] mt-4">Pengajuan solar per musim tanam</h3>
                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed">
                    Pengajuan surat rekomendasi solar dilakukan setiap musim tanam, sekitar 3 bulan sekali. Setelah masa itu terlewati, Anda dapat memakai
                    fitur <span class="font-semibold text-[#1B4332]">Ajukan Cepat</span> untuk mengirim ulang dengan data sebelumnya, tanpa mengisi form dari awal.
                </p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-3xl p-7">
                <span class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
                    <i class="bi bi-exclamation-triangle"></i>
                </span>
                <h3 class="font-serif text-xl text-amber-900 mt-4">Gunakan sesuai peruntukan</h3>
                <p class="text-sm text-amber-900/80 mt-2 leading-relaxed">
                    Solar dan pupuk bersubsidi hanya untuk kegiatan pertanian sesuai rekomendasi. Penyalahgunaan dapat dikenai sanksi
                    sesuai ketentuan yang berlaku.
                    <a href="{{ route('infopublik.informasi') }}" class="font-semibold underline underline-offset-2">Baca ketentuan selengkapnya</a>.
                </p>
            </div>
        </div>
    </section>

    {{-- ================= PENUTUP ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-16 text-center reveal">
            <h2 class="font-serif text-3xl md:text-4xl leading-tight">Masih ada yang ingin ditanyakan?</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Lihat jawaban pertanyaan umum atau hubungi UPTD Warungkondang langsung.</p>
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
            if (!('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); return; }
            const io = new IntersectionObserver((entries) => {
                entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
            }, { threshold: 0.12 });
            els.forEach(e => io.observe(e));
        })();
    </script>

@endsection