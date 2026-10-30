@extends('layouts.ketua')

@section('title', 'Dashboard Ketua')

@section('content')

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Halo, {{ auth()->user()->name }}</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Ringkasan surat rekomendasi.</p>

    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        @foreach ([
            ['Menunggu Tanda Tangan', $menungguTtd, 'text-yellow-700'],
            ['Ditandatangani Bulan Ini', $sudahTtdBulanIni, 'text-[#1B4332]'],
            ['Disetujui Bulan Ini', $diterimaBulanIni, 'text-[#1B4332]'],
        ] as [$label, $nilai, $warna])
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-5">
                <p class="text-xs text-[#5C6B62]">{{ $label }}</p>
                <p class="text-3xl font-semibold {{ $warna }} mt-1">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <a href="{{ route('ketua.surat.index') }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-5 py-2.5 rounded-full">
        <i class="bi bi-pen"></i> Lihat surat menunggu tanda tangan
    </a>

@endsection