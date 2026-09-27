@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

    @php
        $jam = now()->hour;
        $sapaan = match (true) {
            $jam < 11 => 'Selamat pagi',
            $jam < 15 => 'Selamat siang',
            $jam < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    @endphp

    {{-- Greeting --}}
    <div class="mb-6">
        <p class="text-sm text-[#5C6B62]">{{ now()->translatedFormat('l, d F Y') }}</p>
        <h1 class="text-2xl font-semibold text-[#1B4332] mt-1">{{ $sapaan }}, {{ explode(' ', auth()->user()->name)[0] }}</h1>
    </div>

    {{-- Strip ringkasan --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl mb-8 grid grid-cols-3 divide-x divide-[#E1DCC9]">
        <div class="px-5 py-4">
            <div class="flex items-center gap-2 text-[#5C6B62] text-xs mb-1.5">
                <i class="bi bi-file-earmark-text"></i>
                <span>Total Pengajuan</span>
            </div>
            <p class="text-2xl font-semibold text-[#1B4332]">{{ $totalPengajuan ?? 0 }}</p>
        </div>

        <div class="px-5 py-4">
            <div class="flex items-center gap-2 text-[#5C6B62] text-xs mb-1.5">
                <i class="bi bi-hourglass-split"></i>
                <span>Status Terakhir</span>
            </div>
            <p class="text-2xl font-semibold text-[#1B4332] capitalize">{{ $statusTerakhir ?? '—' }}</p>
        </div>

        <div class="px-5 py-4">
            <div class="flex items-center gap-2 text-[#5C6B62] text-xs mb-1.5">
                <i class="bi bi-person-check"></i>
                <span>Terdaftar Sejak</span>
            </div>
            <p class="text-2xl font-semibold text-[#1B4332]">{{ auth()->user()->created_at->translatedFormat('M Y') }}</p>
        </div>
    </div>

    {{-- Daftar layanan --}}
    <h2 class="text-sm font-medium text-[#5C6B62] mb-3">Layanan</h2>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">

        <a href="{{ route('user.surat-solar.index') }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
            <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-fuel-pump"></i>
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-[#14231C]">Surat Rekomendasi Solar</p>
                <p class="text-sm text-[#5C6B62]">Ajukan permohonan baru atau lihat riwayat pengajuan</p>
            </div>
            @if (($statusTerakhir ?? null) === 'pending')
                <span class="text-xs px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 shrink-0">Menunggu</span>
            @endif
            <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
        </a>

        <a href="{{ route('user.profil') }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
            <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-person"></i>
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-[#14231C]">Profil Saya</p>
                <p class="text-sm text-[#5C6B62]">Lihat dan ubah data akun kamu</p>
            </div>
            <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
        </a>

        <a href="{{ route('user.kritiksaran.create') }}" class="flex items-center gap-4 px-5 py-4 hover:bg-[#F5F3EC] transition-colors">
            <span class="w-10 h-10 rounded-full bg-[#EAF2EC] text-[#1B4332] flex items-center justify-center text-lg shrink-0">
                <i class="bi bi-chat-square-text"></i>
            </span>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-[#14231C]">Kritik & Saran</p>
                <p class="text-sm text-[#5C6B62]">Sampaikan masukan untuk pelayanan UPTD</p>
            </div>
            <i class="bi bi-chevron-right text-[#5C6B62] shrink-0"></i>
        </a>

    </div>

@endsection