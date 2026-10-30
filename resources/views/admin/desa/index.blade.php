@extends('layouts.admin')

@section('title', 'Data Desa')

@section('content')

    @php $input = 'w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm'; @endphp

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Data Desa</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Luas baku sawah membatasi total lahan poktan, dan kode desa dipakai di surat pupuk.</p>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5 mb-6">
        <p class="text-sm font-medium text-[#1B4332] mb-3">Tambah Desa</p>
        <form method="POST" action="{{ route('admin.desa.store') }}" class="grid sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="sm:col-span-1"><label class="block text-xs mb-1 text-[#5C6B62]">Nama Desa</label><input type="text" name="nama" value="{{ old('nama') }}" class="{{ $input }}"></div>
            <div><label class="block text-xs mb-1 text-[#5C6B62]">Kode Desa</label><input type="text" name="kode_desa" value="{{ old('kode_desa') }}" class="{{ $input }}"></div>
            <div><label class="block text-xs mb-1 text-[#5C6B62]">Luas Baku Sawah (ha)</label><input type="number" step="0.01" min="0" name="luas_baku_sawah_ha" value="{{ old('luas_baku_sawah_ha') }}" class="{{ $input }}"></div>
            <button type="submit" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Tambah</button>
        </form>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
        @forelse ($desas as $d)
            <div class="px-5 py-4">
                <div class="flex flex-wrap items-end gap-3">
                    <form method="POST" action="{{ route('admin.desa.update', $d) }}" class="flex flex-wrap items-end gap-3 flex-1">
                        @csrf @method('PUT')
                        <div class="flex-1 min-w-[140px]"><label class="block text-xs mb-1 text-[#5C6B62]">Nama</label><input type="text" name="nama" value="{{ $d->nama }}" class="{{ $input }}"></div>
                        <div class="w-32"><label class="block text-xs mb-1 text-[#5C6B62]">Kode Desa</label><input type="text" name="kode_desa" value="{{ $d->kode_desa }}" class="{{ $input }}"></div>
                        <div class="w-40"><label class="block text-xs mb-1 text-[#5C6B62]">Luas Baku (ha)</label><input type="number" step="0.01" min="0" name="luas_baku_sawah_ha" value="{{ $d->luas_baku_sawah_ha }}" class="{{ $input }}"></div>
                        <button type="submit" class="border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full">Simpan</button>
                    </form>
                    <form method="POST" action="{{ route('admin.desa.destroy', $d) }}" onsubmit="return confirm('Hapus desa {{ $d->nama }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 px-3 py-2">Hapus</button>
                    </form>
                </div>
                <p class="text-xs text-[#5C6B62] mt-2">{{ $d->poktans_count }} poktan · terpakai {{ number_format($d->terpakaiHa(), 2, ',', '.') }} ha · sisa {{ number_format($d->sisaHa(), 2, ',', '.') }} ha</p>
            </div>
        @empty
            <div class="px-6 py-14 text-center"><p class="text-sm text-[#5C6B62]">Belum ada desa.</p></div>
        @endforelse
    </div>

@endsection