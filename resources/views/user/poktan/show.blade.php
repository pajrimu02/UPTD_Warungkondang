@extends('layouts.user')

@section('title', $poktan->nama_kelompok)

@section('content')

    @php
        $badge = [
            'diajukan' => ['Menunggu verifikasi', 'bg-amber-100 text-amber-800 border-amber-200'],
            'disetujui' => ['Aktif / Terdaftar', 'bg-emerald-100 text-emerald-800 border-emerald-200'],
            'ditolak' => ['Ditolak', 'bg-red-100 text-red-800 border-red-200'],
        ];
        [$labelStatus, $kelasStatus] = $badge[$poktan->status] ?? ['-', 'bg-slate-100 text-slate-700 border-slate-200'];
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $aktif = $poktan->anggota->where('status', '!=', 'nonaktif')->values();
        $dataSaya = $aktif->first(fn ($a) => (int) $a->user_id === (int) auth()->id());
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    {{-- Navigasi & Header --}}
    <x-tombol-kembali :href="route('user.poktan')" />

    <div class="mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <p class="text-xs font-semibold tracking-wider text-[#2D6A4F] uppercase">Profil Kelembagaan Tani</p>
                <h1 class="font-serif text-3xl text-[#1B4332] mt-1">{{ $poktan->nama_kelompok }}</h1>
                <p class="text-sm text-[#5C6B62] mt-1 flex items-center gap-1.5">
                    <i class="bi bi-geo-alt text-[#2D6A4F]"></i>
                    Desa {{ $poktan->desa->nama ?? '-' }}, Kec. {{ config('uptd.kecamatan', 'Warungkondang') }}
                </p>
            </div>
            <span class="text-xs font-semibold px-4 py-2 rounded-full border {{ $kelasStatus }}">
                {{ $labelStatus }}
            </span>
        </div>
    </div>

    {{-- CARD 1: Detail & Ringkasan Poktan --}}
    <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6 mb-6">
        <div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-[#E1DCC9]">
            <span class="w-9 h-9 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center">
                <i class="bi bi-info-circle"></i>
            </span>
            <div>
                <h2 class="font-semibold text-[#1B4332]">Detail Kelompok Tani</h2>
                <p class="text-xs text-[#8C9086]">Informasi kepengurusan dan kapasitas lahan</p>
            </div>
        </div>

        <dl class="grid sm:grid-cols-2 gap-y-4 gap-x-6 text-sm mb-6">
            <div>
                <dt class="text-xs text-[#8C9086]">Ketua Kelompok</dt>
                <dd class="font-semibold text-[#14231C] mt-0.5">{{ $poktan->nama_ketua ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Kelas Kemampuan</dt>
                <dd class="font-semibold text-[#14231C] mt-0.5">Kelas Pemula</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Nomor SK Pengukuhan</dt>
                <dd class="font-semibold text-[#1B4332] mt-0.5">{{ $poktan->nomor_sk ?? 'Belum diterbitkan' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Tanggal Disetujui</dt>
                <dd class="font-medium text-[#14231C] mt-0.5">
                    {{ $poktan->disetujui_at ? $poktan->disetujui_at->translatedFormat('d M Y') : '—' }}
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs text-[#8C9086]">Alamat / Sekretariat</dt>
                <dd class="font-medium text-[#14231C] mt-0.5">{{ $poktan->alamat ?? '-' }}</dd>
            </div>
        </dl>

        {{-- Metrik Ringkas --}}
        <div class="grid grid-cols-2 gap-3 pt-4 border-t border-[#E1DCC9]">
            <div class="bg-[#F9F8F4] rounded-2xl p-4">
                <p class="text-xs text-[#8C9086]">Jumlah Anggota Aktif</p>
                <p class="font-serif text-xl font-semibold text-[#1B4332] mt-0.5">{{ $aktif->count() }} <span class="text-xs font-sans font-normal text-[#5C6B62]">orang</span></p>
            </div>
            <div class="bg-[#F9F8F4] rounded-2xl p-4">
                <p class="text-xs text-[#8C9086]">Total Luas Lahan</p>
                <p class="font-serif text-xl font-semibold text-[#1B4332] mt-0.5">{{ $fmt($aktif->sum('luas_lahan_ha')) }} <span class="text-xs font-sans font-normal text-[#5C6B62]">ha</span></p>
            </div>
        </div>
    </div>

    {{-- CARD 2: Data Diri & Berkas Saya --}}
    @if ($dataSaya)
        <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6">
            <div class="flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center">
                        <i class="bi bi-person-vcard"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-semibold text-[#1B4332]">Keanggotaan & Berkas Anda</h2>
                            <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$dataSaya->jabatan] ?? $warnaJabatan['anggota'] }}">
                                {{ $dataSaya->jabatan }}
                            </span>
                        </div>
                        <p class="text-xs text-[#5C6B62] mt-0.5">
                            NIK: {{ $dataSaya->nik }} · Luas Lahan: <b>{{ $fmt($dataSaya->luas_lahan_ha) }} ha</b> · HP: {{ $dataSaya->no_hp }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $jenis => $label)
                        @if ($dataSaya->{'file_' . $jenis})
                            <a href="{{ route('user.poktan.berkas', [$poktan, $dataSaya, $jenis]) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-xs font-medium border border-[#E1DCC9] bg-[#F9F8F4] rounded-full px-3.5 py-1.5 text-[#1B4332] hover:border-[#2D6A4F] transition">
                                <i class="bi bi-file-earmark-check text-[#2D6A4F]"></i> {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

@endsection