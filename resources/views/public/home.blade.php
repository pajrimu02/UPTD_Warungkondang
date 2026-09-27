@extends('layouts.public')
@section('title', 'Beranda - UPTD Pertanian Warungkondang')
@section('content')

    <section class="grid md:grid-cols-2 gap-10 items-center py-8">
        <div>
            <p class="text-[#2D6A4F] font-semibold text-sm mb-3">BPP KECAMATAN WARUNGKONDANG · KAB. CIANJUR</p>
            <h1 class="font-serif text-4xl text-[#1B4332] leading-tight">Melayani petani, lebih dekat dan lebih cepat</h1>
            <p class="mt-4 text-[#5C6B62] max-w-md">Ajukan surat rekomendasi solar, pantau informasi pupuk & kuota, dan akses edukasi pertanian dalam satu layanan digital.</p>
            <div class="mt-6 flex gap-3">
                <a href="{{ route('infopublik.pelayanan') }}" class="bg-[#C9A227] text-[#1B4332] font-semibold px-6 py-3 rounded-full">Lihat Pelayanan</a>
                <a href="{{ route('infopublik.stokkuota') }}" class="border border-[#1B4332] text-[#1B4332] font-semibold px-6 py-3 rounded-full">Info Pupuk & Solar</a>
            </div>
        </div>
        <div></div>
    </section>

    <section class="py-10">
        <h2 class="font-serif text-2xl text-[#1B4332] mb-6">Pelayanan</h2>
        <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
            @forelse ($daftarLayanan ?? [] as $layanan)
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <h3 class="font-semibold">{{ $layanan->nama_layanan }}</h3>
                    <p class="text-sm text-[#5C6B62] mt-2">{{ $layanan->deskripsi }}</p>
                </div>
            @empty
                <p class="text-sm text-[#5C6B62] col-span-4">Belum ada data layanan (isi lewat panel Admin).</p>
            @endforelse
        </div>
    </section>

@endsection