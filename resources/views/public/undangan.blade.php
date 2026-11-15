@extends('layouts.public')

@section('title', 'Undangan Bergabung Poktan')

@section('content')

    <div class="max-w-xl mx-auto py-6">
        @if ($poktan)
            <div class="bg-white border border-[#E1DCC9] rounded-3xl p-7 md:p-8 shadow-sm">
                <span class="inline-flex items-center gap-2 rounded-full bg-[#EAF2EC] text-[#1B4332] text-xs font-semibold px-3 py-1.5">
                    <i class="bi bi-envelope-open"></i> Undangan bergabung
                </span>

                <h1 class="font-serif text-3xl text-[#1B4332] mt-4 leading-tight">{{ $poktan->nama_kelompok }}</h1>

                <dl class="mt-5 space-y-2.5 text-sm">
                    <div class="flex gap-3"><dt class="text-[#8C9086] w-28 shrink-0">Desa</dt><dd>{{ $poktan->desa->nama ?? '-' }}</dd></div>
                    <div class="flex gap-3"><dt class="text-[#8C9086] w-28 shrink-0">Ketua</dt><dd>{{ $ketuaNama ?? '-' }}</dd></div>
                    <div class="flex gap-3"><dt class="text-[#8C9086] w-28 shrink-0">Anggota saat ini</dt><dd>{{ $jumlah }} orang</dd></div>
                </dl>

                @if ($peran === null)
                    <div class="mt-6 text-sm bg-[#EAF2EC] rounded-xl px-4 py-3 text-[#3F5148] leading-relaxed">
                        <i class="bi bi-info-circle text-[#2D6A4F]"></i>
                        Untuk bergabung, masuk dengan akun petani Anda, atau daftar jika belum punya akun. Setelah itu Anda diminta mengonfirmasi, lalu mengisi data diri dan mengunggah KTP, KK, dan SPPT Anda sendiri.
                    </div>

                    <div class="flex flex-wrap gap-3 mt-6">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-[#1B4332] text-white text-sm font-semibold px-6 py-3 rounded-full">
                            Daftar Akun <i class="bi bi-arrow-right"></i>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 border border-[#1B4332] text-[#1B4332] text-sm font-semibold px-6 py-3 rounded-full">
                            Saya Sudah Punya Akun
                        </a>
                    </div>
                @else
                    <div class="mt-6 text-sm bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 leading-relaxed">
                        Anda sedang masuk dengan akun non-petani. Keluar dulu, lalu masuk dengan akun petani untuk bergabung.
                    </div>
                @endif
            </div>
        @else
            <div class="bg-white border border-[#E1DCC9] rounded-3xl px-7 py-12 text-center">
                <span class="w-16 h-16 mx-auto rounded-2xl bg-red-50 text-red-600 flex items-center justify-center text-3xl"><i class="bi bi-link-45deg"></i></span>
                <h1 class="font-serif text-2xl text-[#1B4332] mt-5">Tautan tidak berlaku</h1>
                <p class="text-sm text-[#5C6B62] mt-2 max-w-sm mx-auto leading-relaxed">
                    Kode undangan salah, sudah diganti ketua, atau poktan ini sudah diajukan dan tidak menerima anggota baru. Minta tautan terbaru kepada ketua poktan Anda.
                </p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-6 text-sm font-semibold text-[#1B4332] border-b-2 border-[#C9A227]">Ke Beranda</a>
            </div>
        @endif
    </div>

@endsection