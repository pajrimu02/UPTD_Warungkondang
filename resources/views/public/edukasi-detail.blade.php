@extends('layouts.public')
@section('title', $artikel->judul)
@section('content')
    <h1 class="font-serif text-3xl text-[#1B4332] mb-4">{{ $artikel->judul }}</h1>
    <div class="prose max-w-none">{!! $artikel->konten !!}</div>
@endsection