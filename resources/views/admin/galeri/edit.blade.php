@extends('layouts.admin')

@section('title', 'Edit Galeri')

@section('content')
    <a href="{{ route('admin.galeri.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Edit Item Galeri</h1>

    <form method="POST" action="{{ route('admin.galeri.update', $galeri) }}" enctype="multipart/form-data" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        @csrf
        @method('PUT')
        @include('admin.galeri._form', ['galeri' => $galeri])

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('admin.galeri.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">Simpan</button>
        </div>
    </form>
@endsection