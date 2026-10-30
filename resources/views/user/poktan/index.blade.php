@extends('layouts.user')

@section('title', 'Poktan')

@section('content')

    @php
        $badge = [
            'draft' => ['Draft', 'bg-slate-100 text-slate-700'],
            'diajukan' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-800'],
            'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
            'ditolak' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        $boleh = ! $poktan || $poktan->status === 'ditolak';
    @endphp

    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Poktan</h1>
            <p class="text-sm text-[#5C6B62]">Kelompok tani Anda dan status pengajuannya.</p>
        </div>
        @if ($boleh)
            <a href="{{ route('user.poktan.create') }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white px-4 py-2.5 rounded-full text-sm font-medium whitespace-nowrap">
                <i class="bi bi-plus-lg"></i> {{ $poktan ? 'Ajukan Ulang' : 'Ajukan Poktan' }}
            </a>
        @endif
    </div>

    @if ($poktan)
        @php [$labelStatus, $kelasStatus] = $badge[$poktan->status] ?? ['-', 'bg-slate-100 text-slate-700']; @endphp

        <a href="{{ $poktan->status === 'draft' ? route('user.poktan.anggota', $poktan) : route('user.poktan.show', $poktan) }}"
           class="group block bg-white border border-[#E1DCC9] rounded-3xl p-6 md:p-7 hover:border-[#2D6A4F] hover:shadow-lg transition">
            <div class="flex items-start gap-4">
                <span class="w-14 h-14 shrink-0 rounded-2xl bg-[#1B4332] text-[#E9C85A] flex items-center justify-center text-2xl">
                    <i class="bi bi-people-fill"></i>
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <h2 class="font-serif text-xl text-[#1B4332] truncate">{{ $poktan->nama_kelompok }}</h2>
                        <span class="text-xs font-semibold px-3 py-1.5 rounded-full {{ $kelasStatus }}">{{ $labelStatus }}</span>
                    </div>
                    <p class="text-sm text-[#5C6B62] mt-1"><i class="bi bi-geo-alt"></i> {{ $poktan->desa->nama ?? '-' }}</p>
                </div>
            </div>

            <dl class="grid grid-cols-3 gap-4 mt-6 pt-5 border-t border-[#E1DCC9] text-sm">
                <div><dt class="text-xs text-[#8C9086]">Ketua</dt><dd class="font-medium mt-0.5 truncate">{{ $poktan->nama_ketua ?? '-' }}</dd></div>
                <div><dt class="text-xs text-[#8C9086]">Anggota</dt><dd class="font-medium mt-0.5">{{ $poktan->anggota_count }} orang</dd></div>
                <div><dt class="text-xs text-[#8C9086]">Luas Lahan</dt><dd class="font-medium mt-0.5">{{ number_format($poktan->luas_lahan_ha ?? 0, 2, ',', '.') }} ha</dd></div>
            </dl>

            <div class="mt-5">
                @if ($poktan->status === 'draft')
                    <p class="text-sm text-[#2D6A4F] font-semibold">Pengajuan belum selesai. Lanjutkan pengisian <i class="bi bi-arrow-right"></i></p>
                @elseif ($poktan->status === 'diajukan')
                    <p class="text-sm bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3"><i class="bi bi-hourglass-split"></i> Pengajuan sedang diverifikasi admin UPTD. Profil lengkap tersedia setelah disetujui.</p>
                @elseif ($poktan->status === 'disetujui')
                    <p class="text-sm text-[#2D6A4F] font-semibold">Lihat profil lengkap & daftar anggota <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i></p>
                @else
                    <div class="text-sm bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">
                        <p class="font-semibold">Pengajuan ditolak</p>
                        <p class="mt-1">{{ $poktan->catatan_admin ?? 'Tidak ada catatan dari admin.' }}</p>
                    </div>
                @endif
            </div>
        </a>
    @else
        <div class="bg-white border border-dashed border-[#C9C2A3] rounded-3xl px-6 py-14 text-center">
            <span class="w-16 h-16 mx-auto rounded-2xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-3xl">
                <i class="bi bi-people"></i>
            </span>
            <h2 class="font-serif text-xl text-[#1B4332] mt-5">Anda belum memiliki poktan</h2>
            <p class="text-sm text-[#5C6B62] mt-2 max-w-md mx-auto">Ajukan pembentukan kelompok tani dengan menyiapkan berkas anggota sesuai persyaratan di bawah.</p>
            <a href="{{ route('user.poktan.create') }}" class="inline-flex items-center gap-2 mt-6 bg-[#1B4332] text-white px-6 py-3 rounded-full text-sm font-semibold">
                Ajukan Poktan <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    @endif

    @if ($boleh)
        <div class="grid lg:grid-cols-5 gap-5 mt-8">
            <div class="lg:col-span-3 bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <p class="text-xs font-semibold tracking-wide text-[#2D6A4F]">PERSYARATAN PEMBENTUKAN POKTAN</p>
                <ul class="mt-4 space-y-4">
                    @foreach ([
                        ['bi-person-vcard', 'KTP', 'Setiap anggota menyiapkan KTP.'],
                        ['bi-people', 'Kartu Keluarga (KK)', 'Setiap anggota menyiapkan KK.'],
                        ['bi-map', 'SPPT Sawah', 'Bukti kepemilikan sawah beserta luas lahannya. Poktan hanya dapat terbentuk jika SPPT sawah dapat dibuktikan.'],
                    ] as [$icon, $judul, $isi])
                        <li class="flex gap-3.5 items-start">
                            <span class="w-10 h-10 shrink-0 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center"><i class="bi {{ $icon }}"></i></span>
                            <div>
                                <p class="font-semibold text-sm text-[#14231C]">{{ $judul }}</p>
                                <p class="text-sm text-[#5C6B62] mt-0.5 leading-relaxed">{{ $isi }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-5 text-sm bg-amber-50 border border-amber-200 text-amber-900 rounded-xl px-4 py-3 leading-relaxed">
                    <i class="bi bi-exclamation-triangle"></i>
                    Total luas lahan seluruh anggota di satu desa tidak boleh melebihi <b>Luas Baku Sawah (LBS)</b> desa tersebut.
                </p>
                <p class="mt-3 text-xs text-[#8C9086]">Format berkas JPG, PNG, atau PDF, maksimal 2 MB per berkas.</p>
            </div>

            <div class="lg:col-span-2 bg-[#1B4332] text-white rounded-2xl p-6">
                <p class="text-xs font-semibold tracking-wide text-[#E9C85A]">ALUR PENGAJUAN</p>
                <ol class="mt-4 space-y-4">
                    @foreach (['Isi data poktan dan pilih desa', 'Tambahkan anggota beserta berkas KTP, KK, dan SPPT', 'Klik "Selesai & Ajukan"', 'Admin UPTD memverifikasi, lalu profil poktan aktif'] as $n => $teks)
                        <li class="flex gap-3 items-start">
                            <span class="w-7 h-7 shrink-0 rounded-full bg-[#C9A227] text-[#1B4332] text-xs font-bold flex items-center justify-center">{{ $n + 1 }}</span>
                            <span class="text-sm text-[#E4EAE1] leading-relaxed">{{ $teks }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @endif

@endsection