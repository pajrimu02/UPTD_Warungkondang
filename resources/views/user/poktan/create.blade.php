@extends('layouts.user')

@section('title', 'Ajukan Poktan')

@section('content')

    @php
        $inputClass = 'w-full rounded-lg border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20';
    @endphp

    <a href="{{ route('user.poktan') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-6">Ajukan Poktan</h1>

    @include('user.poktan._stepper', ['aktif' => 1])

    <form method="POST" action="{{ route('user.poktan.store') }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm mb-1.5">Nama Kelompok Tani *</label>
            <input type="text" name="nama_kelompok" value="{{ old('nama_kelompok') }}" placeholder="mis. Poktan Tani Makmur" class="{{ $inputClass }}">
            @error('nama_kelompok') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm mb-1.5">Desa *</label>
            <select name="desa_id" class="{{ $inputClass }} bg-white">
                <option value="">-- Pilih desa --</option>
                @foreach ($desas as $d)
                    <option value="{{ $d->id }}" @selected(old('desa_id') == $d->id)>
                        {{ $d->nama }} (sisa LBS {{ number_format($d->sisaHa(), 2, ',', '.') }} ha)
                    </option>
                @endforeach
            </select>
            @error('desa_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            @if ($desas->isEmpty())
                <p class="text-xs text-amber-700 mt-1">Data desa belum diisi admin. Hubungi petugas UPTD.</p>
            @else
                <p class="text-xs text-[#8C9086] mt-1">LBS = Luas Baku Sawah. Total lahan anggota tidak boleh melebihi sisa ini.</p>
            @endif
        </div>

        <div>
            <label class="block text-sm mb-1.5">Alamat / Sekretariat *</label>
            <textarea name="alamat" rows="3" class="{{ $inputClass }}">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="text-sm bg-[#EAF2EC] rounded-xl px-4 py-3 text-[#3F5148] leading-relaxed">
            <i class="bi bi-info-circle text-[#2D6A4F]"></i>
            Langkah berikutnya: tambahkan anggota. Siapkan KTP, KK, dan SPPT sawah tiap anggota (JPG/PNG/PDF, maks 2 MB).
        </div>

        <div class="flex justify-end gap-3 pt-1">
            <a href="{{ route('user.poktan') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" @disabled($desas->isEmpty()) class="inline-flex items-center gap-2 bg-[#1B4332] disabled:opacity-40 text-white text-sm px-6 py-2.5 rounded-full">
                Lanjut: Tambah Anggota <i class="bi bi-arrow-right"></i>
            </button>
        </div>
    </form>

@endsection