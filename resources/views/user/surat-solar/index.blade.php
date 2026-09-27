@extends('layouts.user')

@section('title', 'Surat Rekomendasi Solar')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-[#5C6B62]">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="text-2xl font-semibold text-[#1B4332] mt-1">Surat Rekomendasi Solar</h1>
        </div>
        <a href="{{ route('user.surat-solar.create') }}" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full shrink-0">
            <i class="bi bi-plus-lg"></i>
            <span>Ajukan Baru</span>
        </a>
    </div>

    @if ($suratSolars->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-file-earmark-text text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada pengajuan. Klik "Ajukan Baru" untuk mengirim permohonan pertama kamu.</p>
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
            @foreach ($suratSolars as $surat)
                @php
                    $statusIcon = match ($surat->status) {
                        'pending' => 'bi-hourglass-split',
                        'diproses' => 'bi-arrow-repeat',
                        'diterima' => 'bi-check-circle',
                        'ditolak' => 'bi-x-circle',
                    };
                    $statusColor = match ($surat->status) {
                        'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                        'diproses' => 'bg-sky-50 text-sky-700 border-sky-200',
                        'diterima' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                    };
                @endphp
                <a href="{{ route('user.surat-solar.show', $surat) }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
                    <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                        <i class="bi bi-fuel-pump"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-[#14231C]">{{ $surat->jumlah_liter_diajukan }} Liter</p>
                        <p class="text-sm text-[#5C6B62]">Diajukan {{ $surat->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <span class="flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full border {{ $statusColor }} shrink-0">
                        <i class="{{ $statusIcon }} bi"></i>
                        <span>{{ ucfirst($surat->status) }}</span>
                    </span>
                    <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $suratSolars->links() }}
        </div>
    @endif

@endsection