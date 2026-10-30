 
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')

    <div class="mb-6">
        <h2 class="font-semibold text-xl text-[#1B4332] mb-1">
            Halo, {{ auth()->user()->name }}
        </h2>

        <p class="text-sm text-[#5C6B62]">
            Ringkasan aktivitas UPTD Pertanian Warungkondang.
        </p>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        @foreach ([
            ['Warga Terdaftar', $totalUser, 'text-[#1B4332]'],
            ['Solar Menunggu', $solarPending, 'text-yellow-700'],
            ['Pupuk Menunggu', $pupukPending, 'text-yellow-700'],
            ['Poktan Menunggu', $poktanPending, 'text-yellow-700'],
            ['Total Solar', $totalSuratSolar, 'text-[#1B4332]'],
            ['Total Pupuk', $totalSuratPupuk, 'text-[#1B4332]'],
        ] as [$label, $nilai, $warna])
            <div class="bg-white border border-[#E1DCC9] rounded-xl p-4">
                <p class="text-xs text-[#5C6B62]">{{ $label }}</p>
                <p class="text-2xl font-semibold {{ $warna }} mt-1">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>


    {{-- Dua Kolom Informasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Pengajuan Solar Terbaru --}}
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-5">

            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-[#1B4332]">
                    Pengajuan Solar Terbaru
                </h3>

                <a
                    href="{{ route('admin.surat-solar.index') }}"
                    class="text-xs text-[#1B4332] underline"
                >
                    Lihat semua
                </a>
            </div>

            @forelse ($suratTerbaru as $surat)

                <a
                    href="{{ route('admin.surat-solar.show', $surat) }}"
                    class="flex items-center justify-between py-2 border-b border-[#E1DCC9] last:border-0 text-sm"
                >

                    <div>
                        <p class="font-medium">
                            {{ $surat->user->name }}
                        </p>

                        <p class="text-xs text-[#5C6B62]">
                            {{ $surat->jumlah_liter_diajukan }} Liter
                            ·
                            {{ $surat->created_at->diffForHumans() }}
                        </p>
                    </div>

                    <span
                        @class([
                            'text-xs px-3 py-1 rounded-full font-medium',
                            'bg-yellow-100 text-yellow-800' => $surat->status === 'pending',
                            'bg-blue-100 text-blue-800' => $surat->status === 'diproses',
                            'bg-green-100 text-green-800' => $surat->status === 'diterima',
                            'bg-red-100 text-red-800' => $surat->status === 'ditolak',
                        ])
                    >
                        {{ ucfirst($surat->status) }}
                    </span>

                </a>

            @empty

                <p class="text-sm text-[#5C6B62
                    Belum ada pengajuan.
                </p>

            @endforelse

        </div>


        {{-- Kritik & Saran Terbaru --}}
        <div class="bg-white border border-[#E1DCC9] rounded-xl p-5">

            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-[#1B4332]">
                    Kritik & Saran Terbaru
                </h3>

                <a
                    href="{{ route('admin.kritiksaran.index') }}"
                    class="text-xs text-[#1B4332] underline"
                >
                    Lihat semua
                </a>
            </div>

            @forelse ($kritikTerbaru as $item)

                <a
                    href="{{ route('admin.kritiksaran.show', $item) }}"
                    class="flex items-center justify-between py-2 border-b border-[#E1DCC9] last:border-0 text-sm"
                >

                    <div>
                        <p class="font-medium">
                            {{ $item->user->name }}
                        </p>

                        <p class="text-xs text-[#5C6B62] line-clamp-1">
                            {{ Str::limit($item->isi, 40) }}
                        </p>
                    </div>

                    <span
                        @class([
                            'text-xs px-3 py-1 rounded-full font-medium',
                            'bg-yellow-100 text-yellow-800' => $item->status === 'pending',
                            'bg-blue-100 text-blue-800' => $item->status === 'diproses',
                            'bg-green-100 text-green-800' => $item->status === 'selesai',
                        ])
                    >
                        {{ ucfirst($item->status) }}
                    </span>

                </a>

            @empty

                <p class="text-sm text-[#5C6B62]">
                    Belum ada kritik/saran.
                </p>

            @endforelse

        </div>

    </div>

@endsection
 
