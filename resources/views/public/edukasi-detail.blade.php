@extends('layouts.public')

@section('title', $artikel->judul . ' - Edukasi UPTD Warungkondang')

@section('content')

    <script>document.documentElement.classList.add('js')</script>
    <style>
        .js .reveal { opacity: 0; transform: translateY(18px); transition: opacity .7s ease, transform .7s ease; }
        .js .reveal.in { opacity: 1; transform: none; }

        /* Tipografi isi artikel (plugin "prose" tidak tersedia di Tailwind CDN) */
        .artikel { font-size: 1.0625rem; line-height: 1.85; color: #2B3A32; }
        .artikel > *:first-child { margin-top: 0; }
        .artikel p { margin: 0 0 1.3em; }
        .artikel h2 { font-family: ui-serif, Georgia, serif; font-size: 1.65rem; line-height: 1.25; color: #1B4332; margin: 2em 0 .6em; font-weight: 600; }
        .artikel h3 { font-family: ui-serif, Georgia, serif; font-size: 1.3rem; line-height: 1.3; color: #1B4332; margin: 1.8em 0 .5em; font-weight: 600; }
        .artikel h4 { font-size: 1.1rem; color: #14231C; margin: 1.5em 0 .4em; font-weight: 600; }
        .artikel ul { list-style: disc; padding-left: 1.5rem; margin: 0 0 1.3em; }
        .artikel ol { list-style: decimal; padding-left: 1.5rem; margin: 0 0 1.3em; }
        .artikel li { margin-bottom: .5em; padding-left: .25rem; }
        .artikel li::marker { color: #2D6A4F; }
        .artikel strong, .artikel b { color: #14231C; font-weight: 600; }
        .artikel blockquote { margin: 1.8em 0; padding: 1.1rem 1.4rem; border-left: 4px solid #C9A227; background: #F6EDC9; background: rgba(246,237,201,.6); border-radius: 0 1rem 1rem 0; color: #1B4332; font-style: italic; }

        @media (prefers-reduced-motion: reduce) {
            .js .reveal { opacity: 1; transform: none; transition: none; }
        }
    </style>

    @php
        // ---------- Isi artikel (aman) ----------
        $mentah = (string) $artikel->konten;

        if (strip_tags($mentah) === $mentah) {
            // Teks biasa: pecah per paragraf
            $isi = collect(preg_split('/\R{2,}/', trim($mentah)))
                ->filter()
                ->map(fn ($p) => '<p>' . nl2br(e(trim($p))) . '</p>')
                ->implode('');
        } else {
            // HTML dari admin: hanya tag aman, semua atribut dibuang
            $isi = strip_tags($mentah, '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote>');
            $isi = preg_replace('/<([a-z][a-z0-9]*)\b[^>]*>/i', '<$1>', $isi);
        }

        $menit = max(1, (int) ceil(str_word_count(strip_tags($mentah)) / 200));
        $tgl = $artikel->created_at ? $artikel->created_at->translatedFormat('d F Y') : null;

        $urlIni = url()->current();
        $pesanWa = urlencode($artikel->judul . ' - UPTD Warungkondang ' . $urlIni);
        $urlFb = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($urlIni);

        // ---------- Artikel terkait ----------
        $terkait = $terkait ?? \App\Models\Edukasi::where('id', '!=', $artikel->id)->latest()->take(3)->get();

        $grads = [
            'from-[#1B4332] to-[#2D6A4F]',
            'from-[#2D6A4F] to-[#7FA650]',
            'from-[#7A5C12] to-[#C9A227]',
        ];
        $icons = ['bi-flower2', 'bi-gear-wide-connected', 'bi-calendar2-check'];

        $ringkas = fn ($a, $n = 90) => \Illuminate\Support\Str::limit(
            trim(preg_replace('/\s+/', ' ', strip_tags($a->konten ?? ''))), $n
        );
    @endphp

    {{-- Bilah progres membaca --}}
    <div id="progres" class="fixed top-0 left-0 h-1 w-0 bg-[#C9A227] z-[70] transition-[width] duration-100"></div>

    {{-- ================= HERO ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10">
        <div class="relative overflow-hidden bg-[#1B4332]">
            <img src="{{ asset('storage/home/uptd.png') }}" alt="" aria-hidden="true"
                 class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-[#0F1F17]/95 via-[#0F1F17]/80 to-[#0F1F17]/50"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F1F17]/70 via-transparent to-transparent"></div>

            <div class="relative z-10 max-w-3xl mx-auto px-6 pt-20 pb-14 md:pt-24 md:pb-16">
                <nav class="flex flex-wrap items-center gap-2 text-xs text-white/70 mb-5" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <a href="{{ route('infopublik.edukasi') }}" class="hover:text-white transition">Edukasi</a>
                    <i class="bi bi-chevron-right text-[10px]"></i>
                    <span class="text-white">Artikel</span>
                </nav>

                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-medium px-3.5 py-1.5">
                    <i class="bi bi-book text-[#E9C85A]"></i> Edukasi Pertanian
                </span>

                <h1 class="font-serif text-3xl md:text-5xl text-white leading-[1.15] mt-5">{{ $artikel->judul }}</h1>

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 mt-6 text-sm text-white/80">
                    @if ($tgl)
                        <span class="inline-flex items-center gap-2"><i class="bi bi-calendar3 text-[#E9C85A]"></i> {{ $tgl }}</span>
                    @endif
                    <span class="inline-flex items-center gap-2"><i class="bi bi-clock text-[#E9C85A]"></i> {{ $menit }} menit baca</span>
                    <span class="inline-flex items-center gap-2"><i class="bi bi-person-badge text-[#E9C85A]"></i> UPTD Warungkondang</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= ARTIKEL ================= --}}
    <article class="max-w-3xl mx-auto py-12 reveal">

        <a href="{{ route('infopublik.edukasi') }}" class="inline-flex items-center gap-1.5 text-sm text-[#5C6B62] hover:text-[#1B4332] transition mb-6">
            <i class="bi bi-arrow-left"></i> Semua artikel
        </a>

        @if ($artikel->gambar)
            <figure class="mb-10">
                <img src="{{ asset('storage/' . $artikel->gambar) }}" alt="{{ $artikel->judul }}"
                     class="w-full max-h-[460px] object-cover rounded-3xl shadow-xl shadow-black/10">
            </figure>
        @endif

        <div id="isi-artikel" class="artikel">
            {!! $isi !!}
        </div>

        {{-- Bagikan --}}
        <div class="mt-12 pt-8 border-t border-[#E1DCC9] flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm font-semibold text-[#14231C]">Bagikan artikel ini</p>
            <div class="flex flex-wrap items-center gap-2">
                <a href="https://wa.me/?text={{ $pesanWa }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] hover:text-[#1B4332] text-[#5C6B62] text-sm font-medium rounded-full px-4 py-2 transition">
                    <i class="bi bi-whatsapp text-[#25D366]"></i> WhatsApp
                </a>
                <a href="{{ $urlFb }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] hover:text-[#1B4332] text-[#5C6B62] text-sm font-medium rounded-full px-4 py-2 transition">
                    <i class="bi bi-facebook text-[#1877F2]"></i> Facebook
                </a>
                <button type="button" id="salin" data-url="{{ $urlIni }}"
                        class="inline-flex items-center gap-2 bg-white border border-[#E1DCC9] hover:border-[#2D6A4F] hover:text-[#1B4332] text-[#5C6B62] text-sm font-medium rounded-full px-4 py-2 transition">
                    <i class="bi bi-link-45deg text-lg leading-none"></i> <span>Salin tautan</span>
                </button>
            </div>
        </div>

        {{-- Kotak penutup artikel --}}
        <div class="mt-10 bg-[#EAF2EC] border border-[#2D6A4F]/20 rounded-3xl p-6 md:p-7 flex gap-4 items-start">
            <span class="w-12 h-12 shrink-0 rounded-xl bg-white text-[#1B4332] flex items-center justify-center text-xl">
                <i class="bi bi-chat-dots"></i>
            </span>
            <div>
                <p class="font-semibold text-[#14231C]">Ada pertanyaan seputar topik ini?</p>
                <p class="text-sm text-[#5C6B62] mt-1 leading-relaxed">
                    Tanyakan langsung kepada penyuluh atau petugas UPTD di wilayah Anda.
                    <a href="{{ route('hubungi') }}" class="font-semibold text-[#1B4332] underline underline-offset-2">Hubungi kami</a>.
                </p>
            </div>
        </div>
    </article>

    {{-- ================= ARTIKEL TERKAIT ================= --}}
    @if ($terkait->isNotEmpty())
        <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-16">
            <div class="max-w-6xl mx-auto px-6 reveal">
                <div class="flex items-end justify-between flex-wrap gap-3 mb-8">
                    <div>
                        <p class="text-[#2D6A4F] font-semibold text-sm mb-1">BACA JUGA</p>
                        <h2 class="font-serif text-2xl md:text-3xl text-[#1B4332]">Artikel lainnya</h2>
                    </div>
                    <a href="{{ route('infopublik.edukasi') }}" class="text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227] pb-0.5">Lihat semua →</a>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($terkait as $i => $a)
                        <a href="{{ route('infopublik.edukasi.show', $a->slug) }}"
                           class="group bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-lg hover:border-[#2D6A4F] transition">
                            <div class="relative h-40 overflow-hidden">
                                @if ($a->gambar)
                                    <img src="{{ asset('storage/' . $a->gambar) }}" alt="{{ $a->judul }}" loading="lazy"
                                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="absolute inset-0 bg-gradient-to-br {{ $grads[$i % 3] }} flex items-center justify-center">
                                        <i class="bi {{ $icons[$i % 3] }} text-6xl text-white/25 transition-transform duration-500 group-hover:scale-110"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 flex flex-col flex-1">
                                @if ($a->created_at)
                                    <span class="text-xs text-[#8C9086]"><i class="bi bi-calendar3"></i> {{ $a->created_at->translatedFormat('d F Y') }}</span>
                                @endif
                                <h3 class="font-semibold text-[#14231C] mt-2 leading-snug">{{ $a->judul }}</h3>
                                <p class="text-sm text-[#5C6B62] mt-2 leading-relaxed line-clamp-2 flex-1">{{ $ringkas($a) }}</p>
                                <span class="inline-flex items-center gap-1 text-sm font-semibold text-[#2D6A4F] mt-4">
                                    Baca artikel <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

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
                <a href="{{ route('infopublik.edukasi') }}" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-7 py-3 rounded-full transition">
                    Artikel Lainnya
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
                }, { threshold: 0.08 });
                els.forEach(e => io.observe(e));
            }

            // Bilah progres membaca
            const bar = document.getElementById('progres');
            const isi = document.getElementById('isi-artikel');
            function hitung() {
                const r = isi.getBoundingClientRect();
                const total = r.height - window.innerHeight * 0.5;
                const lewat = -r.top + window.innerHeight * 0.3;
                const pct = Math.max(0, Math.min(100, (lewat / total) * 100));
                bar.style.width = pct + '%';
            }
            window.addEventListener('scroll', hitung, { passive: true });
            window.addEventListener('resize', hitung);
            hitung();

            // Salin tautan
            const tombol = document.getElementById('salin');
            tombol.addEventListener('click', async () => {
                const url = tombol.dataset.url;
                try {
                    await navigator.clipboard.writeText(url);
                } catch (e) {
                    const t = document.createElement('textarea');
                    t.value = url; document.body.appendChild(t); t.select();
                    document.execCommand('copy'); document.body.removeChild(t);
                }
                const label = tombol.querySelector('span');
                const awal = label.textContent;
                label.textContent = 'Tersalin!';
                setTimeout(() => label.textContent = awal, 1800);
            });
        })();
    </script>

@endsection