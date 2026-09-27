@extends('layouts.public')
@section('title', 'FAQ')
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-6">FAQ</h1>
    @foreach ($faq as $kategori => $items)
        <h2 class="font-semibold text-[#2D6A4F] mt-6 mb-2">{{ $kategori }}</h2>
        <div class="space-y-3">
            @foreach ($items as $item)
                <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
                    <p class="font-medium">{{ $item['q'] }}</p>
                    <p class="text-sm text-[#5C6B62] mt-1">{{ $item['a'] }}</p>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection