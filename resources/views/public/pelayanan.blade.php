@extends('layouts.public')
@section('title', 'Pelayanan')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-6">Pelayanan</h1>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse ($daftarLayanan as $layanan)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <h3 class="font-semibold">{{ $layanan->nama_layanan }}</h3>
                <p class="text-sm text-[#5C6B62] mt-2">{{ $layanan->deskripsi }}</p>
            </div>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada data.</p>
        @endforelse
    </div>
@endsection