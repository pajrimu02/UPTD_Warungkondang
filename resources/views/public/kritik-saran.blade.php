@extends('layouts.public')
@section('title', 'Kritik & Saran')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-2">Kritik & Saran</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Halaman ini hanya menampilkan kritik/saran yang sudah ditanggapi. Untuk mengirim kritik/saran baru, silakan <a href="{{ route('login') }}" class="underline text-[#2D6A4F]">masuk</a> terlebih dahulu.</p>
    <div class="space-y-4">
        @forelse ($data as $item)
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-5">
                <span class="text-xs uppercase text-[#2D6A4F] font-semibold">{{ $item->kategori }}</span>
                <p class="mt-1">{{ $item->isi }}</p>
                <p class="text-sm text-[#5C6B62] mt-2">Tanggapan: {{ $item->tanggapan_admin }}</p>
            </div>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada yang ditampilkan.</p>
        @endforelse
    </div>
@endsection