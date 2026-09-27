@extends('layouts.public')

@section('title', 'Beranda - UPTD Pertanian Warungkondang')

@section('content')

    {{-- HERO — full bleed --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen -mt-10 z-0">

        <div class="relative h-[70vh] md:h-[85vh]">

            <img
                src="{{ asset('storage/home/uptd.png') }}"
                alt="Kantor UPTD Pertanian Warungkondang"
                class="absolute inset-0 w-full h-full object-cover"
            >

            <div class="absolute inset-0 bg-gradient-to-r from-[#0F1F17]/90 via-[#0F1F17]/55 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0F1F17]/85 via-transparent to-transparent"></div>

            <div class="relative z-10 h-full flex items-center px-6 md:px-16">
                <div class="max-w-lg">
                    <h1 class="font-serif text-4xl md:text-5xl text-white leading-tight">Melayani petani, lebih dekat dan lebih cepat</h1>
                    <p class="mt-4 text-[#E4EAE1] max-w-md">Ajukan surat rekomendasi solar, pantau informasi pupuk & kuota, dan akses edukasi pertanian dalam satu layanan digital.</p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('infopublik.pelayanan') }}" class="bg-[#C9A227] text-[#1B4332] font-semibold px-6 py-3 rounded-full">Lihat Pelayanan</a>
                        <a href="{{ route('infopublik.stokkuota') }}" class="border border-white text-white font-semibold px-6 py-3 rounded-full">Info Pupuk & Solar</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kartu Pelayanan
             - Mobile: elemen biasa, di bawah foto, TIDAK overlap (aman, gak numpuk)
             - Desktop (md+): overlap/ngambang di bawah foto, seperti sebelumnya --}}
        <div class="relative px-6 py-8 md:py-0
                    md:absolute md:left-0 md:right-0 md:bottom-0 md:translate-y-1/2 md:z-10
                    bg-[#F5F3EC] md:bg-transparent">
            <div class="max-w-6xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach ([
                        ['bi-fuel-pump', 'Solar & Pupuk Bersubsidi', 'Informasi kuota diperbarui rutin setiap bulan.'],
                        ['bi-lightning-charge', 'Layanan Cepat', 'Ajukan surat rekomendasi online, tanpa antre.'],
                        ['bi-people', 'Pendampingan Petani', 'Bimbingan teknis langsung dari penyuluh lapangan.'],
                        ['bi-book', 'Edukasi Pertanian', 'Materi dan pelatihan rutin untuk kelompok tani.'],
                    ] as [$icon, $title, $desc])
                        <div class="group relative bg-white rounded-2xl p-6 pt-7 shadow-xl shadow-black/10 border border-[#E1DCC9] overflow-hidden transition hover:-translate-y-1 hover:shadow-2xl">
                            <span class="absolute top-0 left-0 h-1 w-0 bg-[#C9A227] transition-all duration-300 group-hover:w-full"></span>
                            <span class="w-11 h-11 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg">
                                <i class="bi {{ $icon }}"></i>
                            </span>
                            <p class="font-semibold text-[#14231C] text-sm mt-4">{{ $title }}</p>
                            <p class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Spacer — cuma dibutuhkan di desktop (buat ngasih ruang kartu yang overlap).
         Mobile gak butuh spacer karena kartu udah di alur normal (bukan overlap). --}}
    <div class="hidden md:block md:h-24"></div>

    {{-- STATISTIK --}}
    <section class="py-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach ([
                ['120+', 'Kelompok Tani Terlayani'],
                ['350+', 'Pengajuan Diproses'],
                ['4', 'Desa Cakupan Wilayah'],
                ['100%', 'Digital & Transparan'],
            ] as [$angka, $label])
                <div>
                    <p class="font-serif text-3xl md:text-4xl text-[#1B4332]">{{ $angka }}</p>
                    <p class="text-sm text-[#5C6B62] mt-1">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- KENAPA PILIH KAMI --}}
    <section class="py-14 grid md:grid-cols-2 gap-10 items-center">
        <div>
            <p class="text-[#2D6A4F] font-semibold text-sm mb-2">MENGAPA UPTD WARUNGKONDANG</p>
            <h2 class="font-serif text-3xl text-[#1B4332] leading-snug">Layanan yang dirancang agar petani tidak perlu bolak-balik kantor</h2>
            <p class="mt-4 text-[#5C6B62]">Kami menggabungkan proses administrasi lama menjadi satu sistem digital, sehingga pengajuan bisa dipantau kapan saja tanpa harus datang berulang kali.</p>
        </div>
        <div class="space-y-5">
            @foreach ([
                ['bi-shield-check', 'Transparan', 'Status pengajuan bisa dipantau langsung oleh pemohon, real-time.'],
                ['bi-clock-history', 'Hemat Waktu', 'Formulir dan dokumen dikirim online, tidak perlu antre di kantor.'],
                ['bi-geo-alt', 'Menjangkau Semua Desa', 'Melayani seluruh kelompok tani di wilayah Kecamatan Warungkondang.'],
            ] as [$icon, $title, $desc])
                <div class="flex gap-4 items-start">
                    <span class="w-10 h-10 shrink-0 rounded-full bg-[#1B4332] text-white flex items-center justify-center">
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

    {{-- INFORMASI TERBARU --}}
    <section class="py-10">
        <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
            <div>
                <h2 class="font-serif text-2xl text-[#1B4332]">Informasi Terbaru</h2>
                <p class="text-sm text-[#5C6B62] mt-1">Kuota, jadwal distribusi, dan pengumuman resmi.</p>
            </div>
            <a href="{{ route('infopublik.stokkuota') }}" class="text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227]">Lihat semua →</a>
        </div>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
            @forelse (($infoTerbaru ?? collect())->take(3) as $item)
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <span class="text-xs uppercase text-[#2D6A4F] font-semibold">{{ $item->jenis ?? $item->kategori }}</span>
                    <h3 class="font-semibold mt-2">{{ $item->judul }}</h3>
                    <p class="text-sm text-[#5C6B62] mt-1.5 line-clamp-2">{{ $item->isi }}</p>
                </div>
            @empty
                @foreach (['Solar', 'Pupuk', 'Umum'] as $tag)
                    <div class="bg-white border border-dashed border-[#C9C2A3] rounded-2xl p-5">
                        <span class="text-xs uppercase text-[#2D6A4F] font-semibold">{{ $tag }}</span>
                        <h3 class="font-semibold mt-2 text-[#5C6B62]">Belum ada data</h3>
                        <p class="text-sm text-[#8C9086] mt-1.5">Konten akan tampil di sini setelah diisi lewat panel Admin.</p>
                    </div>
                @endforeach
            @endforelse
        </div>
    </section>

    {{-- EDUKASI TERBARU --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#EEF3E7] py-14">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-end justify-between mb-6 flex-wrap gap-3">
                <div>
                    <h2 class="font-serif text-2xl text-[#1B4332]">Edukasi Pertanian</h2>
                    <p class="text-sm text-[#5C6B62] mt-1">Tips dan artikel seputar budi daya & penggunaan pupuk yang baik.</p>
                </div>
                <a href="{{ route('infopublik.edukasi') }}" class="text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227]">Baca semua →</a>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-5">
                @forelse (($edukasiTerbaru ?? collect())->take(3) as $artikel)
                    <a href="{{ route('infopublik.edukasi.show', $artikel->slug) }}" class="block bg-white border border-[#E1DCC9] rounded-2xl p-5 hover:border-[#2D6A4F] transition">
                        <h3 class="font-semibold text-[#14231C]">{{ $artikel->judul }}</h3>
                        <p class="text-sm text-[#2D6A4F] mt-2 font-medium">Baca artikel →</p>
                    </a>
                @empty
                    @for ($i = 0; $i < 3; $i++)
                        <div class="bg-white border border-dashed border-[#C9C2A3] rounded-2xl p-5">
                            <h3 class="font-semibold text-[#5C6B62]">Belum ada artikel</h3>
                            <p class="text-sm text-[#8C9086] mt-1.5">Menyusul dari panel Admin.</p>
                        </div>
                    @endfor
                @endforelse
            </div>
        </div>
    </section>

    {{-- IKUTI KAMI --}}
    <section class="relative left-1/2 -translate-x-1/2 w-screen bg-[#1B4332] text-white text-center py-16">
        <div class="max-w-6xl mx-auto px-6">
            <p class="text-[#C9E4B8] font-semibold text-sm tracking-wide">TETAP TERHUBUNG</p>
            <h2 class="font-serif text-3xl mt-2">Ikuti Kabar Terbaru UPTD Warungkondang</h2>
            <p class="text-[#CFE3D6] mt-3 max-w-md mx-auto">Pantau kegiatan, pengumuman, dan dokumentasi kami di media sosial resmi.</p>
            <div class="flex justify-center gap-4 mt-7">
                <a href="#" aria-label="Facebook" class="w-12 h-12 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center text-lg transition">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" aria-label="Instagram" class="w-12 h-12 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center text-lg transition">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" aria-label="YouTube" class="w-12 h-12 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center text-lg transition">
                    <i class="bi bi-youtube"></i>
                </a>
                <a href="https://wa.me/62xxxxxxxxxx" aria-label="WhatsApp" class="w-12 h-12 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center text-lg transition">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
    </section>

@endsection