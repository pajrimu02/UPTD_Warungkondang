@extends('layouts.admin')

@section('title', 'Detail Pengajuan Pupuk')

@section('content')

    @php
        $pupuk = [
            'luas' => 'Luas (Ha)',
            'urea' => 'Urea',
            'npk' => 'NPK',
            'npk_formula' => 'NPK Form.',
            'organik' => 'Organik',
            'za' => 'ZA',
        ];
        $fmt = fn ($n) => rtrim(rtrim(number_format((float) $n, 2, ',', '.'), '0'), ',') ?: '0';
    @endphp

    <x-tombol-kembali :href="route('admin.surat-pupuk.index')" />

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-semibold text-[#1B4332]">Detail Pengajuan</h1>
            @include('admin._badge', ['status' => $suratPupuk->status])
        </div>
        <a href="{{ route('admin.surat-pupuk.kelola', $suratPupuk) }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
            <i class="bi bi-sliders"></i><span>Kelola Pengajuan</span>
        </a>
    </div>

    <div class="space-y-6">

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
            <h2 class="font-semibold text-xl text-[#1B4332] mb-4">Data Kelompok</h2>
            <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><dt class="text-[#5C6B62]">Poktan</dt><dd class="font-medium">{{ $suratPupuk->poktan->nama_kelompok }}</dd></div>
                <div><dt class="text-[#5C6B62]">Ketua</dt><dd class="font-medium">{{ $suratPupuk->poktan->nama_ketua ?: '—' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Desa</dt><dd class="font-medium">{{ $suratPupuk->poktan->desa?->nama ?? '—' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Kode Desa</dt><dd class="font-medium">{{ $suratPupuk->kode_desa ?: '—' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Penyuluh</dt><dd class="font-medium">{{ $suratPupuk->nama_penyuluh }}</dd></div>
                <div><dt class="text-[#5C6B62]">Gapoktan</dt><dd class="font-medium">{{ $suratPupuk->gapoktan ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-[#5C6B62]">Kios Pengecer</dt><dd class="font-medium">{{ trim(($suratPupuk->kode_kios ? $suratPupuk->kode_kios.' - ' : '').$suratPupuk->nama_kios) ?: '—' }}</dd></div>
                <div><dt class="text-[#5C6B62]">Diajukan oleh</dt><dd class="font-medium">{{ $suratPupuk->user->name }}</dd></div>
                <div><dt class="text-[#5C6B62]">Tanggal</dt><dd class="font-medium">{{ $suratPupuk->created_at->translatedFormat('d M Y, H:i') }}</dd></div>
                @if ($suratPupuk->nomor_surat)
                    <div><dt class="text-[#5C6B62]">Nomor Surat</dt><dd class="font-medium">{{ $suratPupuk->nomor_surat }}</dd></div>
                @endif
                @if ($suratPupuk->catatan_admin)
                    <div class="sm:col-span-2"><dt class="text-[#5C6B62]">Catatan Admin</dt><dd class="font-medium">{{ $suratPupuk->catatan_admin }}</dd></div>
                @endif
            </dl>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
            <h2 class="font-semibold text-xl text-[#1B4332] mb-4">Total Kebutuhan ({{ $suratPupuk->items->count() }} petani)</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-[#5C6B62] border-b border-[#E1DCC9]">
                            <th class="py-2 pr-3">MT</th>
                            @foreach ($pupuk as $label) <th class="pr-3 text-right">{{ $label }}</th> @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekap as $m => $row)
                            <tr class="border-b border-[#E1DCC9]/60">
                                <td class="py-2 pr-3 font-medium">MT{{ $m + 1 }}</td>
                                @foreach (array_keys($pupuk) as $k) <td class="pr-3 text-right">{{ $fmt($row[$k]) }}</td> @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="text-xs text-[#8C9086] mt-2">Pupuk dalam kg.</p>
        </div>

        @foreach ($suratPupuk->items as $i => $it)
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                <p class="font-medium text-[#1B4332]">{{ $i + 1 }}. {{ $it->nama_petani }}</p>
                <p class="text-xs text-[#5C6B62] mt-0.5">NIK {{ $it->nik }} · {{ $it->tempat_lahir }}, {{ $it->tanggal_lahir->format('d-m-Y') }} · Ibu: {{ $it->nama_ibu_kandung }}</p>
                <p class="text-xs text-[#5C6B62]">{{ $it->alamat }} · {{ $it->subsektor }}</p>

                <div class="overflow-x-auto mt-3">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-[#5C6B62] border-b border-[#E1DCC9]">
                                <th class="py-1.5 pr-3">MT</th><th class="pr-3">Komoditas</th>
                                @foreach ($pupuk as $label) <th class="pr-3">{{ $label }}</th> @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($it->musim as $n => $m)
                                <tr class="border-b border-[#E1DCC9]/60">
                                    <td class="py-1.5 pr-3">MT{{ $n + 1 }}</td>
                                    <td class="pr-3">{{ $m['komoditas'] ?? '-' }}</td>
                                    @foreach (array_keys($pupuk) as $k) <td class="pr-3">{{ $fmt($m[$k] ?? 0) }}</td> @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

@endsection