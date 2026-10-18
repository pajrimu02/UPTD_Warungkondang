@extends('layouts.user')

@section('title', 'Surat Rekomendasi')

@section('content')

    <a href="{{ route('user.surat-solar.show', $data) }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali ke Detail Pengajuan</span>
    </a>

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Surat Rekomendasi Resmi</h1>
            <p class="text-sm text-[#5C6B62]">Nomor: {{ $data->nomor_surat ?? '-' }}</p>
        </div>
        <a href="{{ asset('storage/'.$data->file_surat_resmi) }}" download class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
            <i class="bi bi-download"></i>
            <span>Unduh</span>
        </a>
    </div>

    @php
        $ext = strtolower(pathinfo($data->file_surat_resmi, PATHINFO_EXTENSION));
    @endphp

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        @if ($ext === 'pdf')
            <iframe src="{{ asset('storage/'.$data->file_surat_resmi) }}" class="w-full" style="height: 80vh;"></iframe>
        @else
            <img src="{{ asset('storage/'.$data->file_surat_resmi) }}" class="w-full h-auto">
        @endif
    </div>

@endsection