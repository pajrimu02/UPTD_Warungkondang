@extends('layouts.public')
@section('title', 'Edukasi')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-6">Edukasi</h1>
    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse ($data as $item)
            <a href="{{ route('infopublik.edukasi.show', $item->slug) }}" class="bg-white border border-[#E1DCC9] rounded-xl p-5 block">
                <h3 class="font-semibold">{{ $item->judul }}</h3>
            </a>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada artikel.</p>
        @endforelse
    </div>
@endsection