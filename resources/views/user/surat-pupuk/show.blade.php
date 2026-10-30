@extends('layouts.user')

@section('title', 'Detail Pengajuan Pupuk')

@section('content')

    <a href="{{ route('user.surat-pupuk.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Detail Pengajuan</h1>
        @include('user.surat-pengajuan._badge', ['status' => $data->status])
    </div>

    @if ($data->catatan_admin)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-sm text-amber-900">
            <p class="font-medium mb-0.5">Catatan admin</p>{{ $data->catatan_admin }}
        </div>
    @endif

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 mb-4 grid sm:grid-cols-2 gap-4 text-sm">
        <div><span class="text-[#5C6B62]">Poktan:</span> {{ $data->poktan->nama_kelompok }}</div>
        <div><span class="text-[#5C6B62]">Penyuluh:</span> {{ $data->nama_penyuluh }}</div>
        <div><span class="text-[#5C6B62]">Kode Desa:</span> {{ $data->kode_desa ?: '-' }}</div>
        <div><span class="text-[#5C6B62]">Gapoktan:</span> {{ $data->gapoktan ?: '-' }}</div>
        <div class="sm:col-span-2"><span class="text-[#5C6B62]">Kios:</span> {{ trim(($data->kode_kios ? $data->kode_kios.' - ' : '').$data->nama_kios) ?: '-' }}</div>
    </div>

    <div class="space-y-4">
        @foreach ($data->items as $i => $it)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <p class="font-medium text-[#1B4332]">{{ $i + 1 }}. {{ $it->nama_petani }}</p>
                <p class="text-xs text-[#5C6B62] mt-0.5">NIK {{ $it->nik }} · {{ $it->tempat_lahir }}, {{ $it->tanggal_lahir->format('d-m-Y') }} · Ibu: {{ $it->nama_ibu_kandung }}</p>
                <p class="text-xs text-[#5C6B62]">{{ $it->alamat }} · {{ $it->subsektor }}</p>

                <div class="overflow-x-auto mt-3">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-[#5C6B62] border-b border-[#E1DCC9]">
                                <th class="py-1.5 pr-3">MT</th><th class="pr-3">Komoditas</th><th class="pr-3">Luas</th>
                                <th class="pr-3">Urea</th><th class="pr-3">SP36</th><th class="pr-3">ZA</th><th class="pr-3">NPK</th><th>Organik</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($it->musim as $n => $m)
                                <tr class="border-b border-[#E1DCC9]/60">
                                    <td class="py-1.5 pr-3">MT{{ $n + 1 }}</td>
                                    <td class="pr-3">{{ $m['komoditas'] }}</td>
                                    <td class="pr-3">{{ $m['luas'] }}</td>
                                    <td class="pr-3">{{ $m['urea'] }}</td>
                                    <td class="pr-3">{{ $m['sp36'] }}</td>
                                    <td class="pr-3">{{ $m['za'] }}</td>
                                    <td class="pr-3">{{ $m['npk'] }}</td>
                                    <td>{{ $m['organik'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <p class="text-xs text-[#8C9086] mt-4">Diajukan {{ $data->created_at->format('d M Y, H:i') }} WIB</p>

@endsection