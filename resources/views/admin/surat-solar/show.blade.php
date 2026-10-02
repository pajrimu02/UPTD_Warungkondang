@extends('layouts.admin')

@section('title', 'Detail Pengajuan Solar')

@section('content')

<a href="{{ route('admin.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
    <i class="bi bi-arrow-left"></i><span>Kembali</span>
</a>
     

    @php
        $spkColor = match ($suratSolar->spk_label) {
            'layak' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'perlu_ditinjau' => 'bg-amber-50 text-amber-700 border-amber-200',
            'tidak_layak' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-[#F5F3EC] text-[#5C6B62] border-[#E1DCC9]',
        };
        $spkLabelText = match ($suratSolar->spk_label) {
            'layak' => 'Layak Diproses',
            'perlu_ditinjau' => 'Perlu Ditinjau Admin',
            'tidak_layak' => 'Tidak Layak (Skor Rendah)',
            default => 'Belum dinilai',
        };
    @endphp


    @if ($suratSolar->spk_skor !== null)
        <div class="flex items-center gap-3 rounded-2xl border px-5 py-4 mb-6 {{ $spkColor }}">
            <i class="bi bi-cpu text-xl"></i>
            <div>
                <p class="font-medium">Rekomendasi SPK: {{ $spkLabelText }}</p>
                <p class="text-sm opacity-80">Skor kelayakan otomatis: {{ $suratSolar->spk_skor }}/100 (berdasarkan kelengkapan dokumen & riwayat pengajuan sebelumnya). Keputusan akhir tetap di tangan admin.</p>
            </div>
        </div>
    @endif

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.surat-solar.lembar-kerja', $suratSolar) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2.5 rounded-full">
            <i class="bi bi-file-earmark-text"></i>
            <span>Cetak Lembar Kerja (buat input ke Apk Solar)</span>
        </a>
    </div>

    @if ($suratSolar->status === 'diterima')
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 mb-6">
            <h3 class="font-semibold text-[#1B4332] mb-1">Arsip Surat Resmi</h3>
            <p class="text-sm text-[#5C6B62] mb-4">Upload hasil scan/foto surat resmi yang sudah diterbitkan dari Apk Solar dan ditandatangani, supaya petani bisa lihat di dashboard-nya.</p>

            @if ($suratSolar->file_surat_resmi)
                <a href="{{ asset('storage/'.$suratSolar->file_surat_resmi) }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-[#1B4332] underline mb-4">
                    <i class="bi bi-file-earmark-check"></i> Lihat surat resmi yang sudah diarsipkan
                </a>
            @endif

            <form method="POST" action="{{ route('admin.surat-solar.upload-surat', $suratSolar) }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="file_surat_resmi" accept=".pdf,.jpg,.jpeg,.png,image/*" capture="environment" class="text-sm flex-1">
                <button type="submit" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full shrink-0">Upload</button>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-5">
            <h2 class="font-semibold text-xl text-[#1B4332]">Detail Pengajuan</h2>

            <div>
                <p class="text-xs text-[#5C6B62] mb-2">Data Pemohon</p>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-[#5C6B62]">Nama</dt><dd class="font-medium">{{ $suratSolar->nama_pemohon ?? $suratSolar->user->name }}</dd></div>
                    <div><dt class="text-[#5C6B62]">NIK</dt><dd class="font-medium">{{ $suratSolar->nik }}</dd></div>
                    <div><dt class="text-[#5C6B62]">No. HP</dt><dd class="font-medium">{{ $suratSolar->no_hp }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Alamat</dt><dd class="font-medium">{{ $suratSolar->alamat }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Poktan</dt><dd class="font-medium">{{ $suratSolar->poktan->nama_poktan ?? '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Jenis Usaha</dt><dd class="font-medium">{{ $suratSolar->jenis_usaha }}</dd></div>
                </dl>
            </div>

            <hr class="border-[#E1DCC9]">

            <div>
                <p class="text-xs text-[#5C6B62] mb-2">Data Alat/Mesin</p>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-[#5C6B62]">Jenis Alat/Mesin</dt><dd class="font-medium">{{ $suratSolar->jenis_alat_mesin }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Fungsi</dt><dd class="font-medium">{{ $suratSolar->fungsi_alat_mesin }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Jumlah</dt><dd class="font-medium">{{ $suratSolar->jumlah_alat_mesin }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Daya</dt><dd class="font-medium">{{ $suratSolar->daya_alat_mesin ?: '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Lama Penggunaan</dt><dd class="font-medium">{{ $suratSolar->lama_penggunaan ?: '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Lama Operasi</dt><dd class="font-medium">{{ $suratSolar->lama_operasi ?: '—' }}</dd></div>
                </dl>
            </div>

            <hr class="border-[#E1DCC9]">

            <div>
                <p class="text-xs text-[#5C6B62] mb-2">Usulan Volume</p>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-[#5C6B62]">Volume Diajukan</dt><dd class="font-medium">{{ $suratSolar->usulan_volume_konsumsi }} L / {{ $suratSolar->volume_periode }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Estimasi Sisa</dt><dd class="font-medium">{{ $suratSolar->estimasi_sisa_liter ?? '—' }} L</dd></div>
                </dl>
            </div>

            <hr class="border-[#E1DCC9]">

            <div>
                <p class="text-xs text-[#5C6B62] mb-2">Berkas Pendukung</p>
                <div class="flex flex-wrap gap-3">
                    @foreach (['file_ktp' => 'KTP', 'file_sku' => 'SKU', 'file_foto_mesin' => 'Foto Mesin'] as $field => $label)
                        @if ($suratSolar->$field)
                            <a href="{{ asset('storage/'.$suratSolar->$field) }}" target="_blank" class="flex items-center gap-1.5 text-sm text-[#1B4332] underline">
                                <i class="bi bi-paperclip"></i>{{ $label }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
            <h3 class="font-semibold text-[#1B4332] mb-4">Kelola Status</h3>

            <form method="POST" action="{{ route('admin.surat-solar.update', $suratSolar) }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm">
                        @foreach (['diajukan','diproses','diterima','ditolak'] as $s)
                            <option value="{{ $s }}" {{ $suratSolar->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

               <div>
    <label class="block text-sm mb-1">Nomor Surat</label>
    @if ($suratSolar->nomor_surat)
        <div class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm bg-[#F5F3EC] text-[#1B4332] font-medium">
            {{ $suratSolar->nomor_surat }}
        </div>
    @else
        <div class="w-full rounded-lg border border-dashed border-[#E1DCC9] px-3 py-2 text-sm text-[#8C9086] italic">
            Akan dibuat otomatis saat status diubah ke "Diterima"
        </div>
    @endif
</div>

                <div>
                    <label class="block text-sm mb-1">Catatan Admin</label>
                    <textarea name="catatan_admin" rows="4" class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm">{{ old('catatan_admin', $suratSolar->catatan_admin) }}</textarea>
                </div>

                <button type="submit" class="w-full bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Simpan</button>
            </form>
        </div>
    </div>

@endsection