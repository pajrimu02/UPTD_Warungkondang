@extends('layouts.user')

@section('title', 'Surat Rekomendasi Solar')

@section('content')

    @php
        $labelMesin = [
            'traktor_roda_dua' => 'Traktor Roda Dua (Hand Tractor)',
            'traktor_roda_empat' => 'Traktor Roda Empat',
            'pompa_air' => 'Pompa Air',
            'rmu' => 'RMU (Rice Milling Unit)',
            'mesin_pemotong_rumput' => 'Mesin Pemotong Rumput',
            'power_thresher' => 'Power Thresher',
            'lainnya' => 'Lainnya',
        ];

        $terakhir = $suratSolars->first();
        $bolehAjukanCepat = $terakhir && $terakhir->created_at->diffInMonths(now()) >= 3;
    @endphp

    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Surat Rekomendasi Solar</h1>
            <p class="text-sm text-[#5C6B62]">Riwayat pengajuan kamu.</p>
        </div>
        <a href="{{ route('user.surat-solar.create') }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white px-4 py-2.5 rounded-full text-sm font-medium whitespace-nowrap">
            <i class="bi bi-plus-lg"></i> Ajukan Baru
        </a>
    </div>

    @if ($bolehAjukanCepat)
        <div class="bg-[#EFF6F0] border border-[#2D6A4F]/30 rounded-2xl p-5 mb-6 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-start gap-3">
                <i class="bi bi-arrow-repeat text-xl text-[#2D6A4F] mt-0.5"></i>
                <div>
                    <p class="font-medium text-[#1B4332]">Sudah waktunya ajukan musim ini</p>
                    <p class="text-sm text-[#5C6B62] mt-0.5">Sudah lebih dari 3 bulan sejak pengajuan terakhir kamu. Kirim ulang pakai data yang sama, tanpa isi form dari awal.</p>
                </div>
            </div>
            <form method="POST" action="{{ route('user.surat-solar.quick-store') }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-[#2D6A4F] text-white text-sm px-5 py-2.5 rounded-full whitespace-nowrap">
                    <i class="bi bi-send"></i>
                    <span>Ajukan Sekarang</span>
                </button>
            </form>
        </div>
    @endif

    <div class="space-y-3">
        <div class="space-y-3">
    @forelse ($suratSolars as $item)
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
            <div class="flex items-center justify-between gap-3">
                <p class="font-medium text-[#14231C]">{{ $labelMesin[$item->jenis_alat_mesin] ?? ucwords(str_replace('_', ' ', $item->jenis_alat_mesin)) }}</p>
                <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap
                    @class([
                        'bg-amber-100 text-amber-800' => $item->status === 'diajukan',
                        'bg-sky-100 text-sky-800' => $item->status === 'diproses',
                        'bg-emerald-100 text-emerald-800' => $item->status === 'diterima',
                        'bg-red-100 text-red-800' => $item->status === 'ditolak',
                    ])">
                    {{ ucfirst($item->status) }}
                </span>
            </div>
            <p class="text-sm text-[#5C6B62] mt-1">Diajukan {{ $item->created_at->format('d M Y') }}</p>

            <div class="flex items-center gap-2 mt-3">
                <a href="{{ route('user.surat-solar.show', $item) }}" class="flex-1 text-center text-sm border border-[#E1DCC9] text-[#1B4332] rounded-lg py-2 hover:border-[#2D6A4F] transition">
                    <i class="bi bi-file-earmark-text"></i> Surat Permohonan
                </a>
                <a href="{{ route('user.surat-solar.resmi', $item) }}" class="flex-1 text-center text-sm border border-[#E1DCC9] text-[#1B4332] rounded-lg py-2 hover:border-[#2D6A4F] transition">
                    <i class="bi bi-patch-check"></i> Surat Resmi
                </a>
            </div>
        </div>
    @empty
        <p class="text-sm text-[#5C6B62]">Belum ada pengajuan.</p>
    @endforelse
</div>
    </div>

    <div class="mt-6">
        {{ $suratSolars->links() }}
    </div>

@endsection