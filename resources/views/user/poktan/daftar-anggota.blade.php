@extends('layouts.user')

@section('title', 'Daftar Anggota Poktan')

@section('content')

    @php
        $aktif = $poktan->anggota;
        $totalLuas = $aktif->sum('luas_lahan_ha');
        $fmt = fn ($n) => number_format((float) $n, 2, ',', '.');
        $badgeJabatan = [
            'ketua' => 'bg-[#1B4332] text-white',
            'sekretaris' => 'bg-[#EAF2EC] text-[#1B4332] border border-[#2D6A4F]/30',
            'bendahara' => 'bg-[#F6EDC9] text-[#7A5C12] border border-[#D4B44A]/40',
            'anggota' => 'bg-[#F5F3EC] text-[#5C6B62] border border-[#E1DCC9]',
        ];
    @endphp

    <x-tombol-kembali :href="route('user.poktan')" />

    <div class="flex items-start justify-between gap-4 flex-wrap mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Daftar Anggota Poktan</h1>
            <p class="text-sm text-[#5C6B62]">
                {{ $poktan->nama_kelompok }} · {{ $aktif->count() }} anggota aktif · Total lahan <b class="text-[#1B4332]">{{ $fmt($totalLuas) }} ha</b>
            </p>
        </div>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[#FAF9F4] border-b border-[#E1DCC9] text-left text-xs uppercase tracking-wider text-[#5C6B62]">
                    <tr>
                        <th class="py-3.5 px-4 w-12">No</th>
                        <th class="py-3.5 px-4">Nama Anggota</th>
                        <th class="py-3.5 px-4">Jabatan</th>
                        <th class="py-3.5 px-4">Alamat</th>
                        <th class="py-3.5 px-4 text-right">Luas Lahan</th>
                        <th class="py-3.5 px-4 text-center">Berkas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#E1DCC9]/60">
                    @forelse ($aktif as $i => $a)
                        @php
                            $adalahSaya = $saya && $saya->id === $a->id;
                            $bisaLihatBerkas = $isKetua || $adalahSaya;
                        @endphp
                        <tr class="{{ $adalahSaya ? 'bg-[#EAF2EC]/45' : 'hover:bg-[#FAF9F4]/70' }}">
                            <td class="py-3.5 px-4 text-[#5C6B62]">{{ $i + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-[#14231C] flex items-center gap-2 flex-wrap">
                                    <span>{{ $a->nama }}</span>
                                    @if ($adalahSaya)
                                        <span class="text-[11px] bg-[#2D6A4F] text-white px-2 py-0.5 rounded-full">Anda</span>
                                    @endif
                                </div>
                                @if ($bisaLihatBerkas && $a->nik)
                                    <p class="text-xs text-[#5C6B62] mt-0.5">NIK: {{ $a->nik }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full {{ $badgeJabatan[$a->jabatan] ?? $badgeJabatan['anggota'] }}">
                                    {{ ucfirst($a->jabatan) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-[#5C6B62]">{{ $a->alamat ?: '-' }}</td>
                            <td class="py-3.5 px-4 text-right font-medium text-[#1B4332] whitespace-nowrap">
                                {{ $fmt($a->luas_lahan_ha) }} ha
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if ($bisaLihatBerkas)
                                    <div class="inline-flex items-center gap-1.5 flex-wrap justify-center">
                                        @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $jenis => $label)
                                            @if ($a->{"file_$jenis"})
                                                <a href="{{ route('user.poktan.berkas', [$poktan, $a, $jenis]) }}" target="_blank"
                                                   class="text-[11px] border border-[#E1DCC9] bg-white hover:border-[#2D6A4F] text-[#1B4332] px-2 py-0.5 rounded-md">
                                                    {{ $label }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-[#2D6A4F]"><i class="bi bi-check-circle-fill"></i> Terverifikasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-sm text-[#5C6B62]">Belum ada anggota aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-[#FAF9F4] border-t border-[#E1DCC9] text-sm font-semibold text-[#1B4332]">
                    <tr>
                        <td colspan="4" class="py-3 px-4 text-right">Total Keseluruhan ({{ $aktif->count() }} Anggota)</td>
                        <td class="py-3 px-4 text-right whitespace-nowrap">{{ $fmt($totalLuas) }} ha</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

@endsection