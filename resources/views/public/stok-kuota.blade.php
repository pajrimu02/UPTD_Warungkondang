@extends('layouts.public')
@section('title', 'Stok & Kuota')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-6">Stok & Kuota Pupuk / Solar</h1>
    <div class="space-y-4">
        @forelse ($data as $item)
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-5">
                <span class="text-xs uppercase text-[#2D6A4F] font-semibold">{{ $item->jenis }} · {{ $item->periode }}</span>
                <h3 class="font-semibold mt-1">{{ $item->judul }}</h3>
                <p class="text-sm text-[#5C6B62] mt-1">{{ $item->isi }}</p>
            </div>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada data.</p>
        @endforelse
    </div>
@endsection