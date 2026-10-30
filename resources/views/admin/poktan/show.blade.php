@extends('layouts.admin')

@section('title', 'Detail Kelompok Tani')

@section('content')

    @php $fmt = fn ($n) => number_format($n, 2, ',', '.'); @endphp

    <a href="{{ route('admin.poktan.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-xl text-[#1B4332]">{{ $poktan->nama_kelompok }}</h2>
                    @include('admin._badge', ['status' => $poktan->status])
                </div>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-[#5C6B62]">Desa</dt><dd class="font-medium">{{ $poktan->desa?->nama ?? '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Ketua</dt><dd class="font-medium">{{ $poktan->nama_ketua ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-[#5C6B62]">Alamat</dt><dd class="font-medium">{{ $poktan->alamat ?: '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Diajukan oleh</dt><dd class="font-medium">{{ $poktan->pengaju?->name ?? '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Jumlah Anggota</dt><dd class="font-medium">{{ $poktan->anggota->count() }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Total Luas Lahan</dt><dd class="font-medium">{{ $fmt($totalLuas) }} ha</dd></div>
                    <div>
                        <dt class="text-[#5C6B62]">Sisa Luas Baku Sawah Desa</dt>
                        <dd class="font-medium {{ $sisaDesa !== null && $totalLuas > $sisaDesa && $poktan->status === 'diajukan' ? 'text-red-600' : '' }}">
                            {{ $sisaDesa !== null ? $fmt($sisaDesa).' ha' : '—' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h2 class="font-semibold text-xl text-[#1B4332] mb-4">Anggota</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-[#5C6B62] border-b border-[#E1DCC9]">
                                <th class="py-2 pr-3">Nama</th><th class="pr-3">Jabatan</th><th class="pr-3">NIK</th><th class="pr-3">No. HP</th><th class="pr-3 text-right">Luas (ha)</th><th>Berkas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($poktan->anggota as $a)
                                <tr class="border-b border-[#E1DCC9]/60 align-top">
                                    <td class="py-2 pr-3 font-medium">{{ $a->nama }}</td>
                                    <td class="pr-3">{{ ucfirst($a->jabatan) }}</td>
                                    <td class="pr-3">{{ $a->nik }}</td>
                                    <td class="pr-3">{{ $a->no_hp }}</td>
                                    <td class="pr-3 text-right">{{ $fmt($a->luas_lahan_ha) }}</td>
                                    <td class="whitespace-nowrap">
                                        @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $j => $l)
                                            <a href="{{ route('admin.poktan.berkas', [$poktan, $a, $j]) }}" target="_blank" class="text-[#1B4332] underline mr-2">{{ $l }}</a>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 h-fit">
            <h3 class="font-semibold text-[#1B4332] mb-4">Keputusan</h3>

            @if ($poktan->status === 'diajukan')
                <form method="POST" action="{{ route('admin.poktan.update', $poktan) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm mb-1">Catatan <span class="text-[#5C6B62]">(wajib jika ditolak)</span></label>
                        <textarea name="catatan_admin" rows="4" class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm">{{ old('catatan_admin') }}</textarea>
                        @error('catatan_admin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" name="keputusan" value="ditolak" class="flex-1 bg-red-50 text-red-700 border border-red-200 text-sm px-4 py-2 rounded-full">Tolak</button>
                        <button type="submit" name="keputusan" value="disetujui" class="flex-1 bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Setujui</button>
                    </div>
                </form>
            @else
                <p class="text-sm text-[#5C6B62]">Status: <b>{{ ucfirst($poktan->status) }}</b>@if ($poktan->disetujui_at) pada {{ $poktan->disetujui_at->translatedFormat('d M Y') }}@endif.</p>
                @if ($poktan->catatan_admin)
                    <p class="text-sm text-[#5C6B62] mt-3"><span class="font-medium text-[#1B4332]">Catatan:</span> {{ $poktan->catatan_admin }}</p>
                @endif
            @endif
        </div>
    </div>

@endsection