@extends('layouts.user')

@section('title', 'Detail Pengajuan')

@section('content')

    @php
        $steps = ['pending' => 1, 'diproses' => 2, 'diterima' => 3, 'ditolak' => 3];
        $currentStep = $steps[$suratSolar->status];
        $isRejected = $suratSolar->status === 'ditolak';
    @endphp

    <a href="{{ route('user.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">{{ $suratSolar->jumlah_liter_diajukan }} Liter</h1>
        <span class="text-sm text-[#5C6B62]">{{ $suratSolar->created_at->translatedFormat('d M Y, H:i') }}</span>
    </div>

    {{-- Stepper status --}}
    <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-6 mb-6">
        <div class="flex items-center">
            @foreach ([1 => 'Diajukan', 2 => 'Diproses', 3 => $isRejected ? 'Ditolak' : 'Selesai'] as $step => $label)
                @php
                    $done = $currentStep > $step || ($currentStep === $step && $step < 3);
                    $active = $currentStep === $step;
                @endphp
                <div class="flex-1 flex flex-col items-center text-center relative">
                    @if ($step > 1)
                        <div class="absolute top-4 right-1/2 w-full h-0.5 -z-10 {{ $currentStep >= $step ? ($isRejected && $step === 3 ? 'bg-red-300' : 'bg-[#2D6A4F]') : 'bg-[#E1DCC9]' }}"></div>
                    @endif
                    <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm shrink-0
                        {{ $currentStep > $step ? 'bg-[#1B4332] text-white' : '' }}
                        {{ $currentStep === $step && !($isRejected && $step === 3) ? 'bg-[#1B4332] text-white' : '' }}
                        {{ $currentStep === $step && $isRejected && $step === 3 ? 'bg-red-600 text-white' : '' }}
                        {{ $currentStep < $step ? 'bg-[#F5F3EC] text-[#5C6B62] border border-[#E1DCC9]' : '' }}">
                        @if ($currentStep > $step)
                            <i class="bi bi-check-lg"></i>
                        @elseif ($isRejected && $step === 3)
                            <i class="bi bi-x-lg"></i>
                        @else
                            {{ $step }}
                        @endif
                    </span>
                    <span class="text-xs mt-2 {{ $active ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
        <dl class="grid sm:grid-cols-2 gap-5 text-sm">
            <div>
                <dt class="flex items-center gap-1.5 text-[#5C6B62] mb-1"><i class="bi bi-people"></i>Kelompok Tani</dt>
                <dd class="font-medium">{{ $suratSolar->nama_kelompok_tani ?: '—' }}</dd>
            </div>
            <div>
                <dt class="flex items-center gap-1.5 text-[#5C6B62] mb-1"><i class="bi bi-person-vcard"></i>NIK</dt>
                <dd class="font-medium">{{ $suratSolar->nik }}</dd>
            </div>
            <div>
                <dt class="flex items-center gap-1.5 text-[#5C6B62] mb-1"><i class="bi bi-geo-alt"></i>Alamat</dt>
                <dd class="font-medium">{{ $suratSolar->alamat }}</dd>
            </div>
            <div>
                <dt class="flex items-center gap-1.5 text-[#5C6B62] mb-1"><i class="bi bi-rulers"></i>Luas Lahan</dt>
                <dd class="font-medium">{{ $suratSolar->luas_lahan }} ha</dd>
            </div>
            @if ($suratSolar->nomor_surat)
                <div>
                    <dt class="flex items-center gap-1.5 text-[#5C6B62] mb-1"><i class="bi bi-hash"></i>Nomor Surat</dt>
                    <dd class="font-medium">{{ $suratSolar->nomor_surat }}</dd>
                </div>
            @endif
        </dl>

        <div>
            <dt class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-1"><i class="bi bi-card-text"></i>Keperluan</dt>
            <dd class="text-sm">{{ $suratSolar->keperluan }}</dd>
        </div>

        @if ($suratSolar->file_pendukung)
            <a href="{{ asset('storage/'.$suratSolar->file_pendukung) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm text-[#1B4332] underline">
                <i class="bi bi-paperclip"></i>
                <span>Lihat file pendukung</span>
            </a>
        @endif

        @if ($suratSolar->catatan_admin)
            <div class="bg-[#F5F3EC] rounded-xl p-4">
                <p class="flex items-center gap-1.5 text-xs text-[#5C6B62] mb-1"><i class="bi bi-chat-left-text"></i>Catatan Admin</p>
                <p class="text-sm">{{ $suratSolar->catatan_admin }}</p>
            </div>
        @endif
    </div>

@endsection