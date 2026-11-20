@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

    @php
        $menunggu = $solarPending + $pupukPending + $poktanPending;
        $persen = fn ($bagian, $total) => $total > 0 ? min(100, round($bagian / $total * 100)) : 0;

        $statusSolar = [
            'diajukan' => ['Diajukan', 'bg-amber-100 text-amber-800'],
            'diproses' => ['Diproses', 'bg-sky-100 text-sky-800'],
            'diterima' => ['Diterima', 'bg-emerald-100 text-emerald-800'],
            'ditolak' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        $statusKritik = [
            'baru' => ['Baru', 'bg-amber-100 text-amber-800'],
            'ditanggapi' => ['Ditanggapi', 'bg-emerald-100 text-emerald-800'],
        ];

        $kartu = [
            ['Warga Terdaftar', $totalUser, 'bi-people', false],
            ['Solar Menunggu', $solarPending, 'bi-fuel-pump', true],
            ['Pupuk Menunggu', $pupukPending, 'bi-flower2', true],
            ['Poktan Menunggu', $poktanPending, 'bi-diagram-3', true],
            ['Total Pengajuan Solar', $totalSuratSolar, 'bi-file-earmark-text', false],
            ['Total Pengajuan Pupuk', $totalSuratPupuk, 'bi-file-earmark-check', false],
        ];

        $pintas = [
            ['Pengajuan Solar', 'admin.surat-solar.index', 'bi-fuel-pump'],
            ['Pengajuan Pupuk', 'admin.surat-pupuk.index', 'bi-flower2'],
            ['Verifikasi Poktan', 'admin.poktan.index', 'bi-diagram-3'], 
        ];
    @endphp

        <section class="relative isolate overflow-hidden rounded-3xl bg-[#1B4332] text-white px-6 py-7 sm:px-8 sm:py-8 mb-6">
        <div class="absolute -right-16 -top-20 -z-10 w-72 h-72 rounded-full bg-[#2D6A4F]/30 pointer-events-none"></div>
        <div class="absolute right-24 -bottom-24 -z-10 w-56 h-56 rounded-full bg-[#C9A227]/10 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div>
                <p class="text-xs font-semibold tracking-wide text-[#E9C85A]">
                    {{ now()->translatedFormat('l, d F Y') }}
                </p>
                <h1 class="font-serif text-2xl sm:text-3xl mt-2 leading-tight">
                    Halo, {{ \Illuminate\Support\Str::of(auth()->user()->name)->before(' ') }}
                </h1>
                <p class="text-sm text-white/70 mt-2 max-w-md leading-relaxed">
                    @if ($menunggu > 0)
                        Ada <span class="text-white font-semibold">{{ $menunggu }} pengajuan</span> yang menunggu tindakan Anda hari ini.
                    @else
                        Semua pengajuan sudah ditangani. Tidak ada yang menunggu.
                    @endif
                </p>
            </div>

            <div class="grid grid-cols-3 gap-2.5 lg:w-[26rem]">
                @foreach ($pintas as [$teks, $rute, $ikon])
                    <a href="{{ route($rute) }}"
                       class="group flex flex-col gap-2 rounded-2xl bg-[#2A5A45] hover:bg-[#33694F] border border-white/10 px-4 py-3.5 transition">
                        <i class="bi {{ $ikon }} text-lg text-[#E9C85A]"></i>
                        <span class="text-xs font-medium leading-snug">{{ $teks }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        @foreach ($kartu as [$label, $nilai, $ikon, $perhatian])
            <div @class([
                'relative rounded-2xl border p-4 transition hover:shadow-md',
                'bg-[#FBF5DC] border-[#E9C85A]/70' => $perhatian && $nilai > 0,
                'bg-white border-[#E1DCC9]' => ! ($perhatian && $nilai > 0),
            ])>
                <div class="flex items-start justify-between">
                    <span @class([
                        'w-10 h-10 rounded-xl flex items-center justify-center text-lg',
                        'bg-[#C9A227] text-[#1B4332]' => $perhatian && $nilai > 0,
                        'bg-[#EAF2EC] text-[#1B4332]' => ! ($perhatian && $nilai > 0),
                    ])>
                        <i class="bi {{ $ikon }}"></i>
                    </span>
                    @if ($perhatian && $nilai > 0)
                        <span class="relative flex h-2.5 w-2.5 mt-1">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#C9A227] opacity-60"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#C9A227]"></span>
                        </span>
                    @endif
                </div>
                <p class="text-3xl font-semibold text-[#14231C] mt-4 leading-none">{{ number_format($nilai, 0, ',', '.') }}</p>
                <p class="text-xs text-[#5C6B62] mt-2 leading-snug">{{ $label }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid md:grid-cols-2 gap-4 mb-6">
        @foreach ([
            ['Solar', $solarPending, $totalSuratSolar, 'bi-fuel-pump'],
            ['Pupuk', $pupukPending, $totalSuratPupuk, 'bi-flower2'],
        ] as [$nama, $tunggu, $total, $ikon])
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <div class="flex items-center justify-between">
                    <p class="flex items-center gap-2 text-sm font-semibold text-[#1B4332]">
                        <i class="bi {{ $ikon }}"></i> Antrean {{ $nama }}
                    </p>
                    <span class="text-xs text-[#5C6B62]">{{ $tunggu }} dari {{ $total }} menunggu</span>
                </div>
                <div class="mt-4 h-2.5 rounded-full bg-[#EAF2EC] overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-[#2D6A4F] to-[#C9A227]" style="width: {{ $persen($tunggu, $total) }}%"></div>
                </div>
                <p class="text-xs text-[#8C9086] mt-2">{{ $persen($tunggu, $total) }}% belum ditangani</p>
            </div>
        @endforeach
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <section class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EFEBDD]">
                <h3 class="font-semibold text-[#1B4332]">Pengajuan Solar Terbaru</h3>
                <a href="{{ route('admin.surat-solar.index') }}" class="text-xs font-semibold text-[#2D6A4F] hover:text-[#1B4332]">
                    Lihat semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @forelse ($suratTerbaru as $surat)
                @php
                    $nama = $surat->user->name ?? $surat->nama_pemohon ?? '-';
                    [$labelStatus, $kelasStatus] = $statusSolar[$surat->status] ?? [ucfirst($surat->status), 'bg-slate-100 text-slate-700'];
                @endphp
                <a href="{{ route('admin.surat-solar.show', $surat) }}"
                   class="flex items-center gap-3.5 px-5 py-3.5 border-b border-[#F3F0E4] last:border-0 hover:bg-[#FAF8F1] transition">
                    <span class="w-10 h-10 shrink-0 rounded-full bg-[#EAF2EC] text-[#1B4332] font-semibold flex items-center justify-center">
                        {{ strtoupper(substr($nama, 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#14231C] truncate">{{ $nama }}</p>
                        <p class="text-xs text-[#8C9086] mt-0.5">
                            {{ $surat->usulan_volume_konsumsi ?? 0 }} Liter · {{ $surat->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $kelasStatus }}">{{ $labelStatus }}</span>
                </a>
            @empty
                <div class="px-5 py-12 text-center">
                    <span class="w-12 h-12 mx-auto rounded-2xl bg-[#EAF2EC] text-[#2D6A4F] flex items-center justify-center text-xl"><i class="bi bi-inbox"></i></span>
                    <p class="text-sm text-[#5C6B62] mt-3">Belum ada pengajuan.</p>
                </div>
            @endforelse
        </section>

                <section class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-[#EFEBDD]">
                <h3 class="font-semibold text-[#1B4332]">Pengajuan Pupuk Terbaru</h3>
                <a href="{{ route('admin.surat-pupuk.index') }}" class="text-xs font-semibold text-[#2D6A4F] hover:text-[#1B4332]">
                    Lihat semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            @forelse ($pupukTerbaru as $surat)
                @php
                    $nama = $surat->user->name ?? '-';
                    $poktanNama = optional($surat->poktan)->nama_kelompok;
                    [$labelStatus, $kelasStatus] = $statusSolar[$surat->status] ?? [ucfirst((string) $surat->status), 'bg-slate-100 text-slate-700'];
                @endphp
                <a href="{{ route('admin.surat-pupuk.show', $surat) }}"
                   class="flex items-center gap-3.5 px-5 py-3.5 border-b border-[#F3F0E4] last:border-0 hover:bg-[#FAF8F1] transition">
                    <span class="w-10 h-10 shrink-0 rounded-full bg-[#F6EDC9] text-[#7A5C12] font-semibold flex items-center justify-center">
                        {{ strtoupper(substr($nama, 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-[#14231C] truncate">{{ $nama }}</p>
                        <p class="text-xs text-[#8C9086] mt-0.5 truncate">
                            @if ($poktanNama) {{ $poktanNama }} · @endif{{ $surat->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $kelasStatus }}">{{ $labelStatus }}</span>
                </a>
            @empty
                <div class="px-5 py-12 text-center">
                    <span class="w-12 h-12 mx-auto rounded-2xl bg-[#F6EDC9] text-[#7A5C12] flex items-center justify-center text-xl"><i class="bi bi-flower2"></i></span>
                    <p class="text-sm text-[#5C6B62] mt-3">Belum ada pengajuan pupuk.</p>
                </div>
            @endforelse
        </section>

    </div>

@endsection