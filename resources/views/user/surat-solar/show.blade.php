@extends('layouts.user')

@section('title', 'Detail Pengajuan Surat Solar')

@section('content')

    <a href="{{ route('user.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-[#1B4332]">Detail Pengajuan</h1>
        <span class="text-xs px-3 py-1.5 rounded-full
            @class([
                'bg-amber-100 text-amber-800' => $data->status === 'diajukan',
                'bg-sky-100 text-sky-800' => $data->status === 'diproses',
                'bg-emerald-100 text-emerald-800' => $data->status === 'diterima',
                'bg-red-100 text-red-800' => $data->status === 'ditolak',
            ])">
            {{ ucfirst($data->status) }}
        </span>
    </div>

    @if ($data->file_surat_resmi)
        <a href="{{ asset('storage/'.$data->file_surat_resmi) }}" target="_blank" class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-5 py-4 rounded-2xl mb-6">
            <i class="bi bi-file-earmark-check text-xl"></i>
            <div class="flex-1">
                <p class="font-medium">Surat resmi sudah terbit</p>
                <p class="text-xs opacity-80">Klik untuk lihat atau unduh surat rekomendasi kamu</p>
            </div>
            <i class="bi bi-box-arrow-up-right"></i>
        </a>
    @elseif ($data->status === 'diterima')
        <div class="flex items-center gap-3 bg-sky-50 border border-sky-200 text-sky-800 text-sm px-5 py-4 rounded-2xl mb-6">
            <i class="bi bi-hourglass-split text-xl"></i>
            <div>
                <p class="font-medium">Pengajuan diterima, surat sedang diproses</p>
                <p class="text-xs opacity-80">Surat resmi akan muncul di sini begitu admin selesai menerbitkannya.</p>
            </div>
        </div>
    @endif

    <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-6">

        <div>
            <p class="text-sm font-medium text-[#1B4332] mb-3">Data Pemohon</p>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-[#5C6B62]">Nama:</span> {{ $data->nama_pemohon }}</div>
                <div><span class="text-[#5C6B62]">NIK:</span> {{ $data->nik }}</div>
                <div class="sm:col-span-2"><span class="text-[#5C6B62]">Alamat:</span> {{ $data->alamat }}</div>
                <div><span class="text-[#5C6B62]">No. HP:</span> {{ $data->no_hp }}</div>
                <div><span class="text-[#5C6B62]">Jenis Usaha:</span> {{ $data->jenis_usaha }}</div>
                @if ($data->nama_kapal)
                    <div><span class="text-[#5C6B62]">Nama Kapal:</span> {{ $data->nama_kapal }}</div>
                @endif
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <p class="text-sm font-medium text-[#1B4332] mb-3">Data Alat/Mesin</p>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-[#5C6B62]">Jenis:</span> {{ $data->jenis_alat_mesin }}</div>
                <div><span class="text-[#5C6B62]">Fungsi:</span> {{ $data->fungsi_alat_mesin }}</div>
                <div><span class="text-[#5C6B62]">Jumlah:</span> {{ $data->jumlah_alat_mesin }}</div>
                <div><span class="text-[#5C6B62]">Daya:</span> {{ $data->daya_alat_mesin ?? '-' }}</div>
                <div><span class="text-[#5C6B62]">Lama Penggunaan:</span> {{ $data->lama_penggunaan ?? '-' }}</div>
                <div><span class="text-[#5C6B62]">Lama Operasi:</span> {{ $data->lama_operasi ?? '-' }}</div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <p class="text-sm font-medium text-[#1B4332] mb-3">Usulan Volume BBM</p>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div><span class="text-[#5C6B62]">Volume:</span> {{ $data->usulan_volume_konsumsi }} Liter / {{ $data->volume_periode }}</div>
                <div><span class="text-[#5C6B62]">Estimasi Sisa:</span> {{ $data->estimasi_sisa_liter ?? '-' }} Liter</div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        <div>
            <p class="text-sm font-medium text-[#1B4332] mb-3">Berkas Pendukung</p>
            <div class="grid sm:grid-cols-3 gap-3">
                @foreach (['file_ktp' => 'KTP', 'file_sku' => 'SKU', 'file_foto_mesin' => 'Foto Mesin'] as $field => $label)
                    @if ($data->$field)
                        <a href="{{ asset('storage/' . $data->$field) }}" target="_blank" class="border border-[#E1DCC9] rounded-xl p-3 text-center hover:border-[#2D6A4F] transition">
                            <i class="bi bi-file-earmark-image text-xl text-[#2D6A4F]"></i>
                            <p class="text-xs text-[#5C6B62] mt-1">{{ $label }}</p>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>

        @if ($data->catatan_admin)
            <hr class="border-[#E1DCC9]">
            <div>
                <p class="text-sm font-medium text-[#1B4332] mb-2">Catatan Admin</p>
                <p class="text-sm text-[#5C6B62]">{{ $data->catatan_admin }}</p>
            </div>
        @endif

        <p class="text-xs text-[#8C9086]">Diajukan {{ $data->created_at->format('d M Y, H:i') }} WIB</p>
    </div>

@endsection