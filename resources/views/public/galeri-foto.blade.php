@extends('layouts.public')

@section('title', 'Galeri Foto')

@section('content')

    @php
        /*
        |--------------------------------------------------------------------------
        | 6 FOTO UNGGULAN (statis)
        |--------------------------------------------------------------------------
        | Taruh file di: storage/app/public/galeri/1.jpg ... 6.jpg
        | (diakses lewat asset('storage/galeri/1.jpg'), pastikan sudah php artisan storage:link)
        | Edit 'judul' dan 'deskripsi' sesuai isi foto asli.
        */
        $unggulan = [
            ['file' => 'galeri/1.jpg', 'judul' => 'Penyuluhan Kelompok Tani',
             'deskripsi' => 'Penyuluh pertanian memberikan pendampingan dan informasi budi daya kepada anggota kelompok tani.'],
            ['file' => 'galeri/2.jpg', 'judul' => 'Pelayanan Surat Rekomendasi Solar',
             'deskripsi' => 'Petugas UPTD memverifikasi berkas pengajuan surat rekomendasi BBM solar bersubsidi dari petani.'],
            ['file' => 'galeri/3.jpg', 'judul' => 'Monitoring Lahan Pertanian',
             'deskripsi' => 'Kunjungan lapangan untuk memantau kondisi lahan dan pertumbuhan tanaman di wilayah Warungkondang.'],
            ['file' => 'galeri/4.jpg', 'judul' => 'Koordinasi UPTD dan BPP',
             'deskripsi' => 'Rapat koordinasi antara UPTD dan Balai Penyuluhan Pertanian untuk menyelaraskan program pelayanan.'],
            ['file' => 'galeri/5.jpg', 'judul' => 'Sosialisasi Pupuk dan Solar Bersubsidi',
             'deskripsi' => 'Sosialisasi ketentuan penyaluran dan penggunaan pupuk serta BBM bersubsidi kepada petani.'],
            ['file' => 'galeri/6.jpg', 'judul' => 'Alat dan Mesin Pertanian',
             'deskripsi' => 'Dokumentasi alat dan mesin pertanian (traktor, RMU, dll) yang menjadi dasar pengajuan rekomendasi.'],
             ['file' => 'galeri/7.jpg', 'judul' => 'Pelatihan Budi Daya Tanaman',
             'deskripsi' => 'Pelatihan bagi petani mengenai teknik budi daya tanaman yang efektif dan ramah lingkungan.'],
             ['file' => 'galeri/8.jpg', 'judul' => 'Pameran Hasil Pertanian',
            'deskripsi' => 'Pameran yang menampilkan hasil pertanian lokal dan inovasi pertanian dari petani setempat.'],
            ['file' => 'galeri/9.jpg', 'judul' => 'Kegiatan Penyuluhan Lapangan',
            'deskripsi' => 'Penyuluh pertanian memberikan bimbingan langsung di lapangan kepada petani mengenai teknik bercocok tanam.'],
            ['file' => 'galeri/10.jpg', 'judul' => 'Distribusi Bantuan Pertanian',
            'deskripsi' => 'Distribusi bantuan pertanian kepada petani di wilayah Warungkondang.'],
            ['file' => 'galeri/11.jpg', 'judul' => 'Kegiatan Panen Bersama',
            'deskripsi' => 'Petani dan penyuluh pertanian melakukan panen bersama untuk meningkatkan semangat gotong royong dan berbagi pengalaman.'],
            ['file' => 'galeri/12.jpg', 'judul' => 'Pelatihan Pengolahan Hasil Pertanian',
            'deskripsi' => 'Pelatihan bagi petani mengenai teknik pengolahan hasil pertanian yang efektif dan ramah lingkungan.'],
        ];

        // Gabungkan: 6 foto unggulan + foto yang diunggah admin dari database
        $semua = collect($unggulan)->map(fn ($f) => [
            'src'       => asset('storage/' . $f['file']),
            'judul'     => $f['judul'],
            'deskripsi' => $f['deskripsi'],
            'tanggal'   => null,
        ]);

        $semua = $semua->concat(
            $data->filter(fn ($i) => $i->file_path)->map(fn ($i) => [
                'src'       => asset('storage/' . $i->file_path),
                'judul'     => $i->judul,
                'deskripsi' => $i->deskripsi ?? null,
                'tanggal'   => $i->tanggal ? \Carbon\Carbon::parse($i->tanggal)->translatedFormat('d F Y') : null,
            ])
        )->values();
    @endphp

    {{-- Header --}}
    <div class="mb-8">
        <p class="text-[#2D6A4F] font-semibold text-sm mb-2 flex items-center gap-2">
         
        </p>
        <div class="flex items-end justify-between gap-4 flex-wrap">
            <div>
                <h1 class="font-serif text-3xl text-[#1B4332] mb-1">Galeri Foto</h1>
                <p class="text-sm text-[#5C6B62] max-w-xl">Dokumentasi kegiatan dan pelayanan UPTD Pertanian Warungkondang bersama petani dan kelompok tani.</p>
            </div>
             
        </div>
    </div>

    {{-- Masonry grid --}}
    <div class="columns-2 md:columns-3 lg:columns-4 gap-4">
        @foreach ($semua as $i => $item)
            <button type="button"
                    data-item="{{ $i }}"
                    onclick="bukaLightbox({{ $i }})"
                    class="group relative block w-full mb-4 break-inside-avoid overflow-hidden rounded-2xl bg-[#E1DCC9] border border-[#E1DCC9] text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#2D6A4F]">

                <img src="{{ $item['src'] }}"
                     alt="{{ $item['judul'] }}"
                     loading="lazy"
                     onerror="this.closest('[data-item]').remove()"
                     class="w-full h-auto block transition-transform duration-500 group-hover:scale-105">

                {{-- Overlay saat hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-[#14231C]/85 via-[#14231C]/20 to-transparent opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                    <p class="text-white text-sm font-semibold leading-snug">{{ $item['judul'] }}</p>
                    @if ($item['deskripsi'])
                        <p class="text-white/80 text-xs mt-1 line-clamp-2">{{ $item['deskripsi'] }}</p>
                    @endif
                    <span class="absolute top-3 right-3 w-8 h-8 rounded-full bg-white/90 text-[#1B4332] flex items-center justify-center text-sm">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </span>
                </div>
            </button>
        @endforeach
    </div>

    {{-- Lightbox --}}
    <div id="lightbox" class="hidden fixed inset-0 z-[60] bg-[#0B1511]/95 flex items-center justify-center p-4" onclick="if(event.target===this) tutupLightbox()">

        <button type="button" onclick="tutupLightbox()" aria-label="Tutup"
                class="absolute top-4 right-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg transition">
            <i class="bi bi-x-lg"></i>
        </button>

        <button type="button" onclick="geser(-1)" aria-label="Sebelumnya"
                class="absolute left-3 md:left-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg transition">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button type="button" onclick="geser(1)" aria-label="Berikutnya"
                class="absolute right-3 md:right-6 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg transition">
            <i class="bi bi-chevron-right"></i>
        </button>

        <figure class="max-w-4xl w-full">
            <img id="lb-img" src="" alt="" class="mx-auto max-h-[70vh] w-auto max-w-full rounded-xl object-contain">
            <figcaption class="mt-4 text-center px-12">
                <p id="lb-judul" class="text-white font-semibold text-lg"></p>
                <p id="lb-deskripsi" class="text-white/70 text-sm mt-1"></p>
                <p id="lb-meta" class="text-white/50 text-xs mt-2"></p>
            </figcaption>
        </figure>
    </div>

    <script>
        const fotoGaleri = @json($semua);
        let idxAktif = 0;

        const lb = document.getElementById('lightbox');

        function tampilkan() {
            const f = fotoGaleri[idxAktif];
            document.getElementById('lb-img').src = f.src;
            document.getElementById('lb-img').alt = f.judul;
            document.getElementById('lb-judul').textContent = f.judul;
            document.getElementById('lb-deskripsi').textContent = f.deskripsi ?? '';
            document.getElementById('lb-meta').textContent =
                (f.tanggal ? f.tanggal + ' · ' : '') + (idxAktif + 1) + ' / ' + fotoGaleri.length;
        }

        function bukaLightbox(i) {
            idxAktif = i;
            tampilkan();
            lb.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function tutupLightbox() {
            lb.classList.add('hidden');
            document.body.style.overflow = '';
        }

        function geser(arah) {
            idxAktif = (idxAktif + arah + fotoGaleri.length) % fotoGaleri.length;
            tampilkan();
        }

        document.addEventListener('keydown', (e) => {
            if (lb.classList.contains('hidden')) return;
            if (e.key === 'Escape') tutupLightbox();
            if (e.key === 'ArrowLeft') geser(-1);
            if (e.key === 'ArrowRight') geser(1);
        });
    </script>

@endsection