@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

    <h2 class="font-semibold text-xl text-[#1B4332] mb-1">Halo, {{ auth()->user()->name }} 👋</h2>
    <p class="text-sm text-[#5C6B62] mb-6">Selamat datang kembali di dashboard UPTD Pertanian Warungkondang.</p>

    {{-- Ringkasan singkat --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
            <p class="text-xs text-[#5C6B62]">Total Pengajuan Solar</p>
            <p class="text-2xl font-semibold text-[#1B4332] mt-1">{{ $totalPengajuan ?? 0 }}</p>
        </div>
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
            <p class="text-xs text-[#5C6B62]">Status Terakhir</p>
            <p class="text-2xl font-semibold text-[#1B4332] mt-1 capitalize">{{ $statusTerakhir ?? '-' }}</p>
        </div>
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
            <p class="text-xs text-[#5C6B62]">Bergabung Sejak</p>
            <p class="text-2xl font-semibold text-[#1B4332] mt-1">{{ auth()->user()->created_at->translatedFormat('M Y') }}</p>
        </div>
    </div>

    {{-- Menu utama --}}
    <div class="space-y-4">
        <a href="{{ route('user.surat-solar.index') }}" class="block bg-white border border-[#E1DCC9] rounded-xl p-5 hover:border-[#2D6A4F] transition">
            <div class="flex items-center gap-3">
                <span class="text-xl">⛽</span>
                <div>
                    <h3 class="font-semibold text-[#14231C]">Ajukan Surat Rekomendasi Solar</h3>
                    <p class="text-sm text-[#5C6B62] mt-1">Lihat riwayat pengajuan atau ajukan baru</p>
                </div>
            </div>
        </a>

        <a href="{{ route('user.profil') }}" class="block bg-white border border-[#E1DCC9] rounded-xl p-5 hover:border-[#2D6A4F] transition">
            <div class="flex items-center gap-3">
                <span class="text-xl">👤</span>
                <div>
                    <h3 class="font-semibold text-[#14231C]">Profil Saya</h3>
                    <p class="text-sm text-[#5C6B62] mt-1">Data akun kamu</p>
                </div>
            </div>
        </a>

        <a href="{{ route('user.kritiksaran.create') }}" class="block bg-white border border-[#E1DCC9] rounded-xl p-5 hover:border-[#2D6A4F] transition">
            <div class="flex items-center gap-3">
                <span class="text-xl">📢</span>
                <div>
                    <h3 class="font-semibold text-[#14231C]">Kritik & Saran</h3>
                    <p class="text-sm text-[#5C6B62] mt-1">Sampaikan masukan untuk UPTD</p>
                </div>
            </div>
        </a>
    </div>

@endsection