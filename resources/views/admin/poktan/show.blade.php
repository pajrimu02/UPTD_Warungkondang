@extends('layouts.admin')

@section('title', 'Detail Kelompok Tani')

@section('content')

    @php
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    <div class="flex items-center justify-between gap-3 flex-wrap mb-4">
        <a href="{{ route('admin.poktan.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] hover:text-[#1B4332]">
            <i class="bi bi-arrow-left"></i><span>Kembali ke Daftar</span>
        </a>

        <a href="{{ route('admin.poktan.kelola', $poktan) }}"
           class="inline-flex items-center gap-1.5 bg-[#1B4332] text-white text-xs font-medium px-4 py-2 rounded-full">
            <i class="bi bi-sliders"></i> Kelola Pengajuan
        </a>
    </div>

    {{-- Header Profil Poktan --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 mb-6">
        <div class="flex items-start justify-between gap-4 flex-wrap pb-5 mb-5 border-b border-[#E1DCC9]">
            <div>
                <p class="text-xs font-semibold tracking-wider text-[#2D6A4F] uppercase">Profil Kelompok Tani</p>
                <h1 class="font-serif text-2xl text-[#1B4332] mt-1">{{ $poktan->nama_kelompok }}</h1>
                <p class="text-sm text-[#5C6B62] mt-1">
                    Desa {{ $poktan->desa?->nama ?? '—' }}, Kec. {{ config('uptd.kecamatan', 'Warungkondang') }}
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @include('admin._badge', ['status' => $poktan->status])
                <a href="{{ route('admin.poktan.sk-preview', $poktan) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] text-[#1B4332] rounded-full px-3.5 py-1.5 hover:border-[#2D6A4F]">
                    <i class="bi bi-file-earmark-pdf"></i> {{ $poktan->status === 'disetujui' ? 'Pratinjau SK' : 'Draf SK' }}
                </a>
                @if ($poktan->file_sk_resmi)
                    <a href="{{ asset('storage/' . $poktan->file_sk_resmi) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full px-3.5 py-1.5">
                        <i class="bi bi-patch-check-fill"></i> Scan SK Resmi
                    </a>
                @endif
            </div>
        </div>

        <dl class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div>
                <dt class="text-xs text-[#8C9086]">Ketua Kelompok</dt>
                <dd class="font-semibold text-[#14231C] mt-0.5">{{ $poktan->nama_ketua ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Kapasitas Anggota</dt>
                <dd class="font-semibold text-[#14231C] mt-0.5">{{ $jumlah }} / {{ $maksAnggota }} orang</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Total Luas Lahan</dt>
                <dd class="font-semibold text-[#14231C] mt-0.5">{{ $fmt($totalLuas) }} ha <span class="text-xs font-normal text-[#8C9086]">(min. {{ $fmt($minLuas) }} ha)</span></dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Nomor SK Pengukuhan</dt>
                <dd class="font-semibold text-[#1B4332] mt-0.5">{{ $poktan->nomor_sk ?? 'Belum diterbitkan' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs text-[#8C9086]">Alamat Sekretariat</dt>
                <dd class="font-medium text-[#14231C] mt-0.5">{{ $poktan->alamat ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Diajukan Oleh</dt>
                <dd class="font-medium text-[#14231C] mt-0.5">{{ $poktan->pengaju?->name ?? '—' }} ({{ $poktan->created_at->translatedFormat('d M Y') }})</dd>
            </div>
            <div>
                <dt class="text-xs text-[#8C9086]">Status Alur TTD</dt>
                <dd class="font-medium text-[#14231C] mt-0.5">
                    @if ($poktan->diserahkan_at)
                        SK Resmi Terbit ({{ $poktan->diserahkan_at->translatedFormat('d M Y') }})
                    @elseif ($poktan->ttd_kembali_at)
                        Sudah di-TTD Kepala UPTD
                    @elseif ($poktan->ttd_dikirim_at)
                        Menunggu TTD Kepala UPTD
                    @elseif ($poktan->status === 'disetujui')
                        Siap Dikirim ke Kepala UPTD
                    @else
                        —
                    @endif
                </dd>
            </div>
        </dl>

        @if ($poktan->catatan_admin)
            <div class="mt-5 pt-4 border-t border-[#E1DCC9] text-sm">
                <span class="font-medium text-[#1B4332]">Catatan Admin:</span>
                <span class="text-[#5C6B62]">{{ $poktan->catatan_admin }}</span>
            </div>
        @endif
    </div>

    {{-- Tabel Daftar Anggota & Berkas --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E1DCC9] flex items-center justify-between">
            <h2 class="font-semibold text-[#1B4332]">Daftar Pengurus & Anggota ({{ $jumlah }})</h2>
        </div>

        <div class="divide-y divide-[#E1DCC9]">
            @forelse ($poktan->anggota as $i => $a)
                <div class="px-6 py-4 flex items-center justify-between gap-4 flex-wrap {{ $a->status === 'nonaktif' ? 'opacity-60' : '' }}">
                    <div class="flex items-start gap-3 min-w-0">
                        <span class="w-8 h-8 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-xs font-semibold shrink-0 mt-0.5">
                            {{ $i + 1 }}
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-medium text-[#14231C]">{{ $a->nama ?: '(belum diisi)' }}</p>
                                <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">
                                    {{ $a->jabatan }}
                                </span>
                                @if ($a->status === 'menunggu')
                                    <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-amber-50 text-amber-700">menunggu diisi</span>
                                @endif
                                @if ($a->dibantu_petugas)
                                    <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-violet-50 text-violet-700">dibantu petugas</span>
                                @endif
                            </div>
                            <p class="text-xs text-[#5C6B62] mt-1">
                                NIK: {{ $a->nik ?: '—' }} · KK: {{ $a->nomor_kk ?: '—' }} · HP: {{ $a->no_hp ?: '—' }}
                            </p>
                            @if ($a->tempat_lahir)
                                <p class="text-xs text-[#8C9086] mt-0.5">
                                    {{ $a->tempat_lahir }}, {{ $a->tanggal_lahir?->format('d-m-Y') }} · Ibu: {{ $a->nama_ibu_kandung }} · {{ $a->alamat ?: '—' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="text-xs font-semibold text-[#1B4332] bg-[#F5F3EC] px-3 py-1 rounded-full">
                            {{ $fmt($a->luas_lahan_ha ?? 0) }} ha
                        </span>

                        <div class="flex items-center gap-1.5">
                            @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $j => $l)
                                @if ($a->{'file_' . $j})
                                    <a href="{{ route('admin.poktan.berkas', [$poktan, $a, $j]) }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs border border-[#E1DCC9] rounded-full px-2.5 py-1 text-[#1B4332] hover:border-[#2D6A4F]">
                                        <i class="bi bi-paperclip"></i> {{ $l }}
                                    </a>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs border border-red-200 bg-red-50 rounded-full px-2.5 py-1 text-red-700">
                                        {{ $l }} kosong
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <p class="px-6 py-10 text-sm text-center text-[#5C6B62]">Belum ada anggota terdaftar.</p>
            @endforelse
        </div>
    </div>

@endsection