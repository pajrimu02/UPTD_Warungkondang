@extends('layouts.user')

@section('title', 'Pengajuan Surat Rekomendasi')

@section('content')

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Pengajuan Surat Rekomendasi</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Pilih jenis surat yang ingin kamu ajukan.</p>

    @unless ($bolehPupuk)
        <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-4 mb-6 text-sm flex items-start gap-3">
            <i class="bi bi-info-circle-fill mt-0.5"></i>
            <div>
                <p class="font-medium">Surat pupuk memerlukan poktan.</p>
                <p class="mt-0.5">Surat solar (BBM) tetap bisa kamu ajukan dengan akun sendiri. Untuk pupuk, <a href="{{ route('user.poktan') }}" class="underline font-medium">ajukan poktan</a> atau bergabung dengan poktan.</p>
            </div>
        </div>
    @endunless

    <div class="grid sm:grid-cols-2 gap-4">
        @foreach ([
            ['BBM (Solar)', 'bi-fuel-pump', 'Surat rekomendasi solar bersubsidi untuk alat/mesin pertanian.', route('user.surat-solar.index'), $totalSolar, $terakhirSolar],
            ['Pupuk', 'bi-flower2', 'Surat rekomendasi pupuk bersubsidi (RDKK) berdasarkan data anggota poktan.', route('user.surat-pupuk.index'), $totalPupuk, $terakhirPupuk],
        ] as [$judul, $ikon, $desk, $url, $total, $status])
            <a href="{{ $url }}" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 hover:border-[#2D6A4F] transition flex flex-col">
                <span class="w-12 h-12 rounded-xl bg-[#EAF2EC] text-[#2D6A4F] flex items-center justify-center text-2xl mb-4">
                    <i class="bi {{ $ikon }}"></i>
                </span>
                <p class="font-semibold text-[#1B4332] text-lg">{{ $judul }}</p>
                <p class="text-sm text-[#5C6B62] mt-1 flex-1">{{ $desk }}</p>
                <div class="flex items-center justify-between mt-4 text-xs text-[#5C6B62]">
                    <span>{{ $total }} pengajuan</span>
                    @if ($status) @include('user.surat-pengajuan._badge', ['status' => $status]) @endif
                </div>
            </a>
        @endforeach
    </div>

@endsection