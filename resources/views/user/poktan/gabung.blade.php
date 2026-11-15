@extends('layouts.user')

@section('title', 'Gabung Poktan')

@section('content')

    <a href="{{ route('user.poktan') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Gabung dengan Kode Undangan</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Minta kode 6 karakter dari ketua poktan Anda.</p>

    @if ($poktan)
        <div class="bg-white border border-[#E1DCC9] rounded-3xl p-6 md:p-7 max-w-xl">
            <p class="text-xs font-semibold tracking-wide text-[#2D6A4F]">KONFIRMASI</p>
            <h2 class="font-serif text-2xl text-[#1B4332] mt-1">{{ $poktan->nama_kelompok }}</h2>
            <dl class="mt-5 space-y-2.5 text-sm">
                <div class="flex gap-3"><dt class="text-[#8C9086] w-24 shrink-0">Desa</dt><dd>{{ $poktan->desa->nama ?? '-' }}</dd></div>
                <div class="flex gap-3"><dt class="text-[#8C9086] w-24 shrink-0">Ketua</dt><dd>{{ $ketuaNama ?? '-' }}</dd></div>
                <div class="flex gap-3"><dt class="text-[#8C9086] w-24 shrink-0">Anggota</dt><dd>{{ $jumlah }} / {{ $maksAnggota }} orang</dd></div>
            </dl>
            <p class="text-sm bg-[#EAF2EC] rounded-xl px-4 py-3 text-[#3F5148] mt-5 leading-relaxed">
                <i class="bi bi-info-circle text-[#2D6A4F]"></i> Pastikan ini poktan yang benar. Setelah bergabung, Anda mengisi data diri dan mengunggah KTP, KK, dan SPPT sendiri.
            </p>

            <div class="flex justify-end gap-3 mt-6">
                <a href="{{ route('user.poktan.gabung') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Bukan ini</a>
                <form method="POST" action="{{ route('user.poktan.gabung.konfirmasi') }}"
                      data-judul="Gabung ke poktan ini?"
                      data-konfirmasi="Anda akan menjadi anggota {{ $poktan->nama_kelompok }}."
                      data-tombol="Ya, Gabung" data-tipe="normal">
                    @csrf
                    <input type="hidden" name="kode" value="{{ $kode }}">
                    <button type="submit" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-2.5 rounded-full">
                        Ya, Gabung <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('user.poktan.gabung.cek') }}" class="bg-white border border-[#E1DCC9] rounded-3xl p-6 md:p-7 max-w-xl">
            @csrf
            <label class="block text-sm mb-1.5">Kode Undangan *</label>
            <input type="text" name="kode" value="{{ old('kode', $kode) }}" maxlength="10" autocomplete="off" autofocus
                   style="text-transform: uppercase; letter-spacing: .3em;"
                   class="w-full rounded-lg border border-[#E1DCC9] px-3.5 py-3 text-center text-xl font-semibold focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20"
                   placeholder="ABC123">
            @error('kode') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror

            <button type="submit" class="mt-5 w-full inline-flex items-center justify-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-3 rounded-full">
                Cek Kode <i class="bi bi-arrow-right"></i>
            </button>
        </form>
    @endif

@endsection