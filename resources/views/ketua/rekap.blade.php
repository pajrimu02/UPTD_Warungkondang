@extends('layouts.ketua')

@section('title', 'Rekap Surat')

@section('content')

    @php
        $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $total = fn ($k) => array_sum(array_column($rekap, $k));
        $labelJenis = $jenis === 'solar' ? 'BBM (Solar)' : 'Pupuk';
    @endphp

    <div class="flex items-center justify-between flex-wrap gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Rekap Surat Rekomendasi</h1>
            <p class="text-sm text-[#5C6B62]">Jumlah pengajuan per bulan berdasarkan tanggal pengajuan.</p>
        </div>

        <form method="GET" class="flex items-center gap-2">
            <select name="jenis" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                <option value="solar" @selected($jenis === 'solar')>BBM (Solar)</option>
                <option value="pupuk" @selected($jenis === 'pupuk')>Pupuk</option>
            </select>
            <select name="tahun" onchange="this.form.submit()" class="text-sm rounded-lg border-[#E1DCC9]">
                @foreach (range(now()->year, now()->year - 4) as $t)
                    <option value="{{ $t }}" @selected($tahun === $t)>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @foreach ([
            ['Total Pengajuan', $ringkasan['total'], 'text-[#1B4332]'],
            ['Diterima', $ringkasan['diterima'], 'text-emerald-700'],
            ['Ditolak', $ringkasan['ditolak'], 'text-red-700'],
            ['Menunggu', $ringkasan['menunggu'], 'text-yellow-700'],
        ] as [$label, $nilai, $warna])
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
                <p class="text-xs text-[#5C6B62]">{{ $label }} {{ $tahun }}</p>
                <p class="text-2xl font-semibold {{ $warna }} mt-1">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tabel per bulan --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-x-auto">
        <div class="px-5 py-3 border-b border-[#E1DCC9] text-sm font-medium text-[#1B4332]">{{ $labelJenis }} · {{ $tahun }}</div>
        <table class="w-full text-sm">
            <thead class="bg-[#F5F3EC] text-xs text-[#5C6B62] text-left">
                <tr>
                    <th class="px-5 py-3">Bulan</th>
                    <th class="px-3 py-3 text-right">Diajukan</th>
                    <th class="px-3 py-3 text-right">Diproses</th>
                    <th class="px-3 py-3 text-right">Diterima</th>
                    <th class="px-3 py-3 text-right">Ditolak</th>
                    @if ($jenis === 'solar')
                        <th class="px-5 py-3 text-right">Liter Diterima</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E1DCC9]">
                @foreach ($rekap as $b => $r)
                    <tr class="{{ ($r['diajukan'] + $r['diproses'] + $r['diterima'] + $r['ditolak']) === 0 ? 'text-[#8C9086]' : '' }}">
                        <td class="px-5 py-2.5">{{ $namaBulan[$b] }}</td>
                        <td class="px-3 text-right">{{ $r['diajukan'] }}</td>
                        <td class="px-3 text-right">{{ $r['diproses'] }}</td>
                        <td class="px-3 text-right">{{ $r['diterima'] }}</td>
                        <td class="px-3 text-right">{{ $r['ditolak'] }}</td>
                        @if ($jenis === 'solar')
                            <td class="px-5 text-right">{{ number_format($r['liter'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-[#F5F3EC] font-medium">
                <tr>
                    <td class="px-5 py-3">Total</td>
                    <td class="px-3 text-right">{{ $total('diajukan') }}</td>
                    <td class="px-3 text-right">{{ $total('diproses') }}</td>
                    <td class="px-3 text-right">{{ $total('diterima') }}</td>
                    <td class="px-3 text-right">{{ $total('ditolak') }}</td>
                    @if ($jenis === 'solar')
                        <td class="px-5 text-right">{{ number_format($total('liter'), 0, ',', '.') }}</td>
                    @endif
                </tr>
            </tfoot>
        </table>
    </div>

@endsection