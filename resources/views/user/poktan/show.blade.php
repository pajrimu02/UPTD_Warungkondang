@extends('layouts.user')

@section('title', $poktan->nama_kelompok)

@section('content')

    @php
        $badge = [
            'diajukan' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-800'],
            'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
            'ditolak' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        [$labelStatus, $kelasStatus] = $badge[$poktan->status] ?? ['-', 'bg-slate-100 text-slate-700'];
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    <a href="{{ route('user.poktan') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex items-start justify-between gap-3 flex-wrap mb-6">
        <div>
            <p class="text-xs font-semibold tracking-wide text-[#2D6A4F]">PROFIL POKTAN</p>
            <h1 class="font-serif text-3xl text-[#1B4332] mt-1">{{ $poktan->nama_kelompok }}</h1>
            <p class="text-sm text-[#5C6B62] mt-1"><i class="bi bi-geo-alt"></i> Desa {{ $poktan->desa->nama ?? '-' }}</p>
        </div>
        <span class="text-xs font-semibold px-3.5 py-2 rounded-full {{ $kelasStatus }}">{{ $labelStatus }}</span>
    </div>

    @if ($poktan->status === 'diajukan')
        <div class="text-sm bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl px-5 py-4 mb-6">
            <i class="bi bi-hourglass-split"></i> Pengajuan sedang diverifikasi admin UPTD.
        </div>
    @elseif ($poktan->status === 'ditolak')
        <div class="text-sm bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6">
            <p class="font-semibold">Pengajuan ditolak</p>
            <p class="mt-1">{{ $poktan->catatan_admin ?? 'Tidak ada catatan dari admin.' }}</p>
        </div>
    @elseif ($poktan->disetujui_at)
        <div class="text-sm bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 mb-6">
            <i class="bi bi-patch-check-fill"></i> Poktan disetujui pada {{ $poktan->disetujui_at->translatedFormat('d F Y') }}.
        </div>
    @endif

    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach ([
            ['bi-person-badge', 'Ketua', $poktan->nama_ketua ?? '-'],
            ['bi-people', 'Jumlah Anggota', $poktan->anggota->count() . ' orang'],
            ['bi-map', 'Total Luas Lahan', $fmt($poktan->luas_lahan_ha ?? 0) . ' ha'],
            ['bi-geo-alt', 'Alamat / Sekretariat', $poktan->alamat ?? '-'],
        ] as [$icon, $label, $nilai])
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <span class="w-10 h-10 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center"><i class="bi {{ $icon }}"></i></span>
                <p class="text-xs text-[#8C9086] mt-3">{{ $label }}</p>
                <p class="font-semibold text-[#14231C] mt-0.5 text-sm leading-snug">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <h2 class="font-semibold text-[#14231C] mb-3">Daftar Anggota</h2>
    <div class="grid md:grid-cols-2 gap-4">
        @foreach ($poktan->anggota as $i => $a)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <span class="w-10 h-10 shrink-0 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-sm font-semibold">{{ $i + 1 }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-semibold text-[#14231C]">{{ $a->nama }}</p>
                            <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">{{ $a->jabatan }}</span>
                        </div>
                        <dl class="mt-3 space-y-1.5 text-sm">
                            <div class="flex gap-2"><dt class="text-[#8C9086] w-20 shrink-0">NIK</dt><dd>{{ $pemilik ? $a->nik : substr($a->nik, 0, 6) . '••••••••••' }}</dd></div>
                            @if ($pemilik)
                                <div class="flex gap-2"><dt class="text-[#8C9086] w-20 shrink-0">No. HP</dt><dd>{{ $a->no_hp }}</dd></div>
                            @endif
                            <div class="flex gap-2"><dt class="text-[#8C9086] w-20 shrink-0">Alamat</dt><dd>{{ $a->alamat }}</dd></div>
                            <div class="flex gap-2"><dt class="text-[#8C9086] w-20 shrink-0">Luas lahan</dt><dd>{{ $fmt($a->luas_lahan_ha) }} ha</dd></div>
                        </dl>
                        @if ($pemilik)
                            <div class="flex flex-wrap gap-2 mt-3">
                                @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $jenis => $label)
                                    <a href="{{ route('user.poktan.berkas', [$poktan, $a, $jenis]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] rounded-full px-3 py-1 text-[#1B4332] hover:border-[#2D6A4F]">
                                        <i class="bi bi-paperclip"></i> {{ $label }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection