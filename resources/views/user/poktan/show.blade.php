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
    <div class="mb-6">
        <x-tombol-kembali :href="route('user.poktan')" />
    </div>
    
    <div>
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

    {{-- AREA 1: GRID 2 KOLOM (DETAIL POKTAN & DOKUMEN SK) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- CARD 1: Detail & Ringkasan Poktan (2 Kolom) --}}
        <div class="lg:col-span-2 bg-white border border-[#E1DCC9] rounded-3xl p-6 flex flex-col justify-between">
            <div>
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
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-[#8C9086]">Alamat / Sekretariat</dt>
                        <dd class="font-medium text-[#14231C] mt-0.5">{{ $poktan->alamat ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- 3 Metrik Ringkas di Bawah Card Detail --}}
            <div class="grid grid-cols-3 gap-3 pt-4 border-t border-[#E1DCC9]">
                <div class="bg-[#F9F8F4] rounded-2xl p-3.5">
                    <p class="text-xs text-[#8C9086]">Jumlah Anggota</p>
                    <p class="font-serif text-xl font-semibold text-[#1B4332] mt-0.5">{{ $aktif->count() }} <span class="text-xs font-sans font-normal text-[#5C6B62]">orang</span></p>
                </div>
                <div class="bg-[#F9F8F4] rounded-2xl p-3.5">
                    <p class="text-xs text-[#8C9086]">Total Luas Lahan</p>
                    <p class="font-serif text-xl font-semibold text-[#1B4332] mt-0.5">{{ $fmt($aktif->sum('luas_lahan_ha')) }} <span class="text-xs font-sans font-normal text-[#5C6B62]">ha</span></p>
                </div>
                <div class="bg-[#F9F8F4] rounded-2xl p-3.5">
                    <p class="text-xs text-[#8C9086]">Tanggal Disetujui</p>
                    <p class="font-semibold text-sm text-[#14231C] mt-1.5">
                        {{ $poktan->disetujui_at ? $poktan->disetujui_at->translatedFormat('d M Y') : '—' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- CARD 2: Tempat Surat / SK Pengukuhan (1 Kolom) --}}
        <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-[#E1DCC9]">
                    <span class="w-9 h-9 rounded-xl bg-[#F6EDC9] text-[#7A5C12] flex items-center justify-center">
                        <i class="bi bi-file-earmark-text"></i>
                    </span>
                    <div>
                        <h2 class="font-semibold text-[#1B4332]">SK Pengukuhan</h2>
                        <p class="text-xs text-[#8C9086]">Dokumen legalitas resmi UPTD</p>
                    </div>
                </div>

                @if ($poktan->status === 'diajukan')
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-800">
                        <p class="font-semibold flex items-center gap-1.5"><i class="bi bi-hourglass-split"></i> Dalam Verifikasi</p>
                        <p class="text-xs mt-1 leading-relaxed">Berkas pengajuan sedang diperiksa oleh Admin UPTD sebelum penerbitan SK.</p>
                    </div>
                @elseif ($poktan->status === 'ditolak')
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-sm text-red-800">
                        <p class="font-semibold flex items-center gap-1.5"><i class="bi bi-x-circle"></i> Pengajuan Ditolak</p>
                        <p class="text-xs mt-1 leading-relaxed">{{ $poktan->catatan_admin ?? 'Tidak ada catatan dari admin.' }}</p>
                    </div>
                @elseif ($poktan->status === 'disetujui')
                    <div class="space-y-3">
                        <div class="bg-[#F9F8F4] border border-[#E1DCC9] rounded-2xl p-3.5">
                            <p class="text-[11px] uppercase tracking-wider text-[#8C9086]">Nomor SK Resmi</p>
                            <p class="font-semibold text-sm text-[#1B4332] mt-0.5 break-all">{{ $poktan->nomor_sk ?? 'Sedang diproses' }}</p>
                        </div>

                        @if ($poktan->diserahkan_at && $poktan->file_sk_resmi)
                            <p class="text-xs text-emerald-700 flex items-center gap-1.5">
                                <i class="bi bi-patch-check-fill"></i> Telah ditandatangani Kepala UPTD
                            </p>
                        @elseif ($poktan->ttd_dikirim_at)
                            <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-xl p-3 leading-relaxed">
                                <i class="bi bi-pen"></i> Menunggu tanda tangan basah Kepala UPTD.
                            </p>
                        @else
                            <p class="text-xs text-[#5C6B62] bg-[#F9F8F4] rounded-xl p-3 leading-relaxed">
                                SK telah bernomor dan sedang disiapkan Admin untuk proses tanda tangan Kepala UPTD.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Tombol Aksi Unduh SK --}}
            <div class="mt-5 pt-4 border-t border-[#E1DCC9]">
                @if ($poktan->diserahkan_at && $poktan->file_sk_resmi)
                    <a href="{{ route('user.poktan.sk-resmi', $poktan) }}" target="_blank"
                       class="w-full inline-flex items-center justify-center gap-2 bg-[#1B4332] hover:bg-[#2D6A4F] text-white text-sm font-semibold px-4 py-2.5 rounded-full transition">
                        <i class="bi bi-download"></i> Unduh SK Resmi (PDF)
                    </a>
                @else
                    <button type="button" disabled
                            class="w-full inline-flex items-center justify-center gap-2 bg-slate-100 text-slate-400 text-sm font-medium px-4 py-2.5 rounded-full cursor-not-allowed">
                        <i class="bi bi-lock"></i> SK Belum Tersedia
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- AREA 2: CARD DATA DIRI & BERKAS SAYA (Jika User adalah Anggota Poktan Ini) --}}
    @if ($dataSaya)
        <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6 mb-6">
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

    {{-- AREA 3: CARD DAFTAR ANGGOTA (Terpisah & Rapi dalam 1 Card) --}}
    <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6">
        <div class="flex items-center justify-between gap-3 flex-wrap pb-4 mb-4 border-b border-[#E1DCC9]">
            <div class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-xl bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center">
                    <i class="bi bi-people"></i>
                </span>
                <div>
                    <h2 class="font-semibold text-[#1B4332]">Daftar Pengurus & Anggota</h2>
                    <p class="text-xs text-[#8C9086]">Total {{ $aktif->count() }} petani terdaftar</p>
                </div>
            </div>

            {{-- Kotak Cari Anggota (Client-side Filter) --}}
            <div class="relative w-full sm:w-64">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-[#8C9086]"></i>
                <input type="text" id="cariAnggota" placeholder="Cari nama anggota..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-full border border-[#E1DCC9] bg-[#F9F8F4] focus:bg-white focus:border-[#2D6A4F] focus:outline-none">
            </div>
        </div>

        <div class="divide-y divide-[#E1DCC9]" id="daftarAnggota">
            @foreach ($aktif as $i => $a)
                @php $punyaSaya = (int) $a->user_id === (int) auth()->id(); @endphp
                <div class="item-anggota py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 flex-wrap" data-nama="{{ strtolower($a->nama) }}">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-8 h-8 shrink-0 rounded-full {{ $punyaSaya ? 'bg-[#1B4332] text-white' : 'bg-[#EAF2EC] text-[#1B4332]' }} flex items-center justify-center text-xs font-semibold">
                            {{ $i + 1 }}
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-medium text-sm text-[#14231C] truncate">
                                    {{ $a->nama }}
                                    @if ($punyaSaya)
                                        <span class="text-xs font-normal text-[#2D6A4F]">(Anda)</span>
                                    @endif
                                </p>
                                <span class="text-[10px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">
                                    {{ $a->jabatan }}
                                </span>
                            </div>
                            <p class="text-xs text-[#8C9086] mt-0.5">
                                NIK: {{ $punyaSaya ? $a->nik : substr((string) $a->nik, 0, 6) . '••••••••••' }}
                            </p>
                        </div>
                    </div>

                    @if ($punyaSaya || $isKetua)
                        <div class="text-right shrink-0">
                            <span class="inline-block text-xs font-semibold text-[#1B4332] bg-[#F9F8F4] border border-[#E1DCC9] px-3 py-1 rounded-full">
                                {{ $fmt($a->luas_lahan_ha) }} ha
                            </span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Script Pencarian Cepat Anggota --}}
    <script>
        document.getElementById('cariAnggota')?.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#daftarAnggota .item-anggota').forEach(el => {
                el.style.display = el.dataset.nama.includes(q) ? '' : 'none';
            });
        });
    </script>

@endsection