@extends('layouts.public')
@section('title', 'Galeri Foto')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-6">Galeri Foto</h1>
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
        @forelse ($data as $item)
            <div class="bg-white border border-[#E1DCC9] rounded-xl overflow-hidden">
                @if ($item->file_path)
                    <img src="{{ asset('storage/' . $item->file_path) }}" class="w-full h-32 object-cover">
                @endif
                <p class="text-sm p-3">{{ $item->judul }}</p>
            </div>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada foto.</p>
        @endforelse
    </div>
@endsection