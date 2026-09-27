@extends('layouts.admin')

@section('title', 'Tambah Stok & Kuota')

@section('content')
    <a href="{{ route('admin.stok-kuota.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Tambah Informasi Stok & Kuota</h1>

    <form method="POST" action="{{ route('admin.stok-kuota.store') }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        @csrf
        @include('admin.stok-kuota._form')

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.stok-kuota.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">Simpan</button>
        </div>
    </form>
@endsection