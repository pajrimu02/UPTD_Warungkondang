@extends('layouts.public')
@section('title', 'Galeri Video')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-6">Galeri Video</h1>
    <div class="grid sm:grid-cols-2 gap-4">
        @forelse ($data as $item)
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
                <p class="font-medium">{{ $item->judul }}</p>
                @if ($item->video_url)
                    <a href="{{ $item->video_url }}" class="text-sm text-[#2D6A4F] underline" target="_blank">Tonton video</a>
                @endif
            </div>
        @empty
            <p class="text-sm text-[#5C6B62]">Belum ada video.</p>
        @endforelse
    </div>
@endsection