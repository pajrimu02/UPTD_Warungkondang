@extends('layouts.public')

@section('title', 'Hubungi Kami - UPTD Pertanian Warungkondang')

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
        // KONTEN SEMENTARA: ganti dengan data asli UPTD sebelum launching.
        $kontak = [
            'alamat'  => 'Jl. (isi nama jalan), Kec. Warungkondang, Kab. Cianjur, Jawa Barat',
            'telepon' => '(0263) xxx xxx',
            'wa'      => '62xxxxxxxxxx',
            'email'   => 'uptd.warungkondang@contoh.go.id',
        ];

        $lat = -6.857114;
        $lng = 107.109592;
        $mapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . $lat . ',' . $lng;
        $rutePath = 'https://www.google.com/maps/dir/?api=1&destination=' . $lat . ',' . $lng;

        $jadwal = [
            1 => ['Senin',  '08:00', '16:00'],
            2 => ['Selasa', '08:00', '16:00'],
            3 => ['Rabu',   '08:00', '16:00'],
            4 => ['Kamis',  '08:00', '16:00'],
            5 => ['Jumat',  '08:00', '16:00'],
            6 => ['Sabtu',  null, null],
            7 => ['Minggu', null, null],
        ];

        $sekarang = now('Asia/Jakarta');
        $hariIni = $sekarang->dayOfWeekIso;
        $jamHariIni = $jadwal[$hariIni];
        $sedangBuka = $jamHariIni[1] && $sekarang->format('H:i') >= $jamHariIni[1] && $sekarang->format('H:i') < $jamHariIni[2];

        $sosmed = [
            ['bi-facebook', 'Facebook', '#'],
            ['bi-instagram', 'Instagram', '#'],
            ['bi-youtube', 'YouTube', '#'],
            ['bi-whatsapp', 'WhatsApp', 'https://wa.me/' . $kontak['wa']],
        ];
    @endphp

    {{-- ================= HERO ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10 bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 pt-16 pb-24 md:pt-20 md:pb-28">
            <div class="max-w-2xl hero-fade">
                <span class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur border border-white/20 text-white text-xs font-medium px-3.5 py-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full rounded-full opacity-75 {{ $sedangBuka ? 'animate-ping bg-emerald-400' : 'bg-[#C9A227]' }}"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 {{ $sedangBuka ? 'bg-emerald-400' : 'bg-[#C9A227]' }}"></span>
                    </span>
                    {{ $sedangBuka ? 'Kantor sedang buka' : 'Kantor sedang tutup' }}
                </span>

                <h1 class="font-serif text-4xl md:text-5xl leading-[1.1] mt-5">
                    Kami siap membantu, <span class="text-[#E9C85A]">hubungi</span> UPTD Warungkondang
                </h1>
                <p class="mt-5 text-[#CFE3D6] leading-relaxed max-w-lg">
                    Datang langsung ke kantor, telepon, atau kirim pesan lewat WhatsApp. Petugas kami akan menjawab pertanyaan seputar surat rekomendasi solar, pupuk, dan penyuluhan.
                </p>

                <div class="mt-7 flex flex-wrap gap-3">
                    <a href="{{ $rutePath }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-[#C9A227] hover:bg-[#D8B23A] text-[#1B4332] font-semibold px-6 py-3 rounded-full transition">
                        <i class="bi bi-signpost-split"></i> Petunjuk Arah
                    </a>
                    <a href="https://wa.me/{{ $kontak['wa'] }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 border border-white/60 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-full transition">
                        <i class="bi bi-whatsapp"></i> Chat WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= KARTU KONTAK ================= --}}
    <section class="relative -mt-14 z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach ([
                ['bi-geo-alt', 'Alamat Kantor', $kontak['alamat'], 'Lihat di Google Maps', $mapsUrl],
                ['bi-telephone', 'Telepon & WhatsApp', $kontak['telepon'] . ' · WA ' . $kontak['wa'], 'Kirim pesan WhatsApp', 'https://wa.me/' . $kontak['wa']],
                ['bi-envelope', 'Email Resmi', $kontak['email'], 'Kirim email', 'mailto:' . $kontak['email']],
            ] as [$icon, $title, $isi, $aksiLabel, $aksiUrl])
                <a href="{{ $aksiUrl }}" @if(!str_starts_with($aksiUrl, 'mailto')) target="_blank" rel="noopener" @endif
                   class="group relative block bg-white rounded-2xl p-6 pt-7 shadow-xl shadow-black/10 border border-[#E1DCC9] overflow-hidden transition hover:-translate-y-1 hover:shadow-2xl">
                    <span class="absolute top-0 left-0 h-1 w-0 bg-[#C9A227] transition-all duration-300 group-hover:w-full"></span>
                    <span class="w-11 h-11 rounded-full bg-[#EAF2EC] text-[#1B4332] group-hover:bg-[#1B4332] group-hover:text-white flex items-center justify-center text-lg transition-colors">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <p class="font-semibold text-[#14231C] text-sm mt-4">{{ $title }}</p>
                    <p class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed break-words">{{ $isi }}</p>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-3">
                        {{ $aksiLabel }} <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= PETA + JAM OPERASIONAL ================= --}}
    <section class="py-16 grid lg:grid-cols-5 gap-6 reveal">

        {{-- Peta --}}
        <div class="lg:col-span-3 bg-white border border-[#E1DCC9] rounded-3xl overflow-hidden flex flex-col">
            <div class="relative h-80 md:h-[26rem] lg:h-auto lg:flex-1 lg:min-h-[28rem] bg-[#EEF3E7]">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1807.3099043761465!2d107.10959200963708!3d-6.857114340755042!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68539e6c363b7f%3A0x6e46f524a508b712!2sBALAI%20PENYULUHAN%20PERTANIAN%20WARUNGKONDANG!5e1!3m2!1sid!2sid!4v1793092017263!5m2!1sid!2sid"
                    title="Lokasi Balai Penyuluhan Pertanian Warungkondang"
                    class="absolute inset-0 w-full h-full border-0"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
            <div class="flex items-center justify-between gap-3 flex-wrap px-5 py-4 border-t border-[#E1DCC9]">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-10 h-10 shrink-0 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center">
                        <i class="bi bi-pin-map"></i>
                    </span>
                    <div class="min-w-0">
                        <p class="font-semibold text-[#14231C] text-sm">Balai Penyuluhan Pertanian Warungkondang</p>
                        <p class="text-xs text-[#5C6B62] truncate">Kec. Warungkondang, Kab. Cianjur</p>
                    </div>
                </div>
                <a href="{{ $rutePath }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-[#1B4332] hover:bg-[#153629] text-white text-sm font-semibold px-5 py-2.5 rounded-full transition">
                    <i class="bi bi-signpost-split"></i> Rute ke sini
                </a>
            </div>
        </div>

        {{-- Jam operasional + kirim pesan --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="font-serif text-xl text-[#1B4332]">Jam Operasional</h2>
                    <span class="text-[11px] font-semibold rounded-full px-2.5 py-1 {{ $sedangBuka ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                        {{ $sedangBuka ? 'Buka sekarang' : 'Tutup' }}
                    </span>
                </div>

                <ul class="divide-y divide-[#E1DCC9] text-sm">
                    @foreach ($jadwal as $no => [$hari, $buka, $tutup])
                        <li class="flex items-center justify-between py-2.5 {{ $no === $hariIni ? 'font-semibold text-[#1B4332]' : 'text-[#5C6B62]' }}">
                            <span class="flex items-center gap-2">
                                @if ($no === $hariIni)<span class="w-1.5 h-1.5 rounded-full bg-[#C9A227]"></span>@endif
                                {{ $hari }}
                            </span>
                            <span>{{ $buka ? $buka . ' - ' . $tutup . ' WIB' : 'Tutup' }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6">
                <h2 class="font-serif text-xl text-[#1B4332]">Kirim Pesan Cepat</h2>
                <p class="text-sm text-[#5C6B62] mt-1 mb-4">Pesan akan dibuka di WhatsApp, tinggal tekan kirim.</p>

                <form id="form-wa" data-nomor="{{ $kontak['wa'] }}" class="space-y-3">
                    <div>
                        <label class="block text-sm mb-1" for="wa-nama">Nama</label>
                        <input id="wa-nama" type="text" required class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F] text-sm">
                    </div>
                    <div>
                        <label class="block text-sm mb-1" for="wa-topik">Topik</label>
                        <select id="wa-topik" class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F] text-sm">
                            <option>Surat rekomendasi solar</option>
                            <option>Pupuk bersubsidi</option>
                            <option>Penyuluhan & pendampingan</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-1" for="wa-pesan">Pesan</label>
                        <textarea id="wa-pesan" rows="3" required class="w-full rounded-lg border-[#E1DCC9] focus:border-[#2D6A4F] focus:ring-[#2D6A4F] text-sm"></textarea>
                    </div>
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-[#1B4332] hover:bg-[#153629] text-white text-sm font-semibold px-6 py-2.5 rounded-full transition">
                        <i class="bi bi-whatsapp"></i> Buka WhatsApp
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ================= PERTANYAAN LAIN ================= --}}
    <section class="pb-16 reveal">
        <div class="grid md:grid-cols-3 gap-5">
            @foreach ([
                ['bi-question-circle', 'Pertanyaan Umum', 'Jawaban singkat seputar layanan dan persyaratan.', route('faq')],
                ['bi-chat-square-text', 'Kritik & Saran', 'Sampaikan masukan, lihat tanggapan UPTD.', route('kritiksaran.publik')],
                ['bi-clipboard-check', 'Daftar Pelayanan', 'Lihat semua layanan yang tersedia di UPTD.', route('infopublik.pelayanan')],
            ] as [$icon, $title, $desc, $url])
                <a href="{{ $url }}" class="group flex gap-4 items-start bg-white border border-[#E1DCC9] rounded-2xl p-5 hover:border-[#2D6A4F] hover:shadow-lg transition">
                    <span class="w-11 h-11 shrink-0 rounded-xl bg-[#1B4332] text-white flex items-center justify-center">
                        <i class="bi {{ $icon }}"></i>
                    </span>
                    <div>
                        <p class="font-semibold text-[#14231C]">{{ $title }}</p>
                        <p class="text-sm text-[#5C6B62] mt-1">{{ $desc }}</p>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#2D6A4F] mt-2">
                            Buka <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ================= SOSMED ================= --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 rounded-full bg-[#2D6A4F]/40"></div>
        <div class="absolute -bottom-32 -left-20 w-96 h-96 rounded-full bg-[#C9A227]/10"></div>

        <div class="relative max-w-6xl mx-auto px-6 py-14 text-center reveal">
            <h2 class="font-serif text-3xl leading-tight">Ikuti kabar terbaru UPTD</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Pengumuman kuota, jadwal penyuluhan, dan kegiatan lapangan kami bagikan lewat kanal resmi.</p>

            <div class="flex justify-center gap-3 mt-6">
                @foreach ($sosmed as [$ico, $nama, $link])
                    <a href="{{ $link }}" target="_blank" rel="noopener" aria-label="{{ $nama }}" class="w-11 h-11 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center text-lg transition">
                        <i class="bi {{ $ico }}"></i>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <script>
        (function () {
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

            var form = document.getElementById('form-wa');
            if (form) {
                form.addEventListener('submit', function (ev) {
                    ev.preventDefault();
                    var nama = document.getElementById('wa-nama').value.trim();
                    var topik = document.getElementById('wa-topik').value;
                    var pesan = document.getElementById('wa-pesan').value.trim();
                    var teks = 'Halo UPTD Pertanian Warungkondang, saya ' + nama + '.\n'
                        + 'Topik: ' + topik + '\n\n' + pesan;
                    window.open('https://wa.me/' + form.dataset.nomor + '?text=' + encodeURIComponent(teks), '_blank');
                });
            }
        })();
    </script>

@endsection