@extends('layouts.admin')

@section('title', 'Kelola Pengajuan Solar')

@section('content')

    @php
        $s = $suratSolar;
        $spkColor = match ($s->spk_label) {
            'layak' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'perlu_ditinjau' => 'bg-amber-50 text-amber-700 border-amber-200',
            'tidak_layak' => 'bg-red-50 text-red-700 border-red-200',
            default => 'bg-[#F5F3EC] text-[#5C6B62] border-[#E1DCC9]',
        };
        $spkText = match ($s->spk_label) {
            'layak' => 'Layak Diproses',
            'perlu_ditinjau' => 'Perlu Ditinjau Admin',
            'tidak_layak' => 'Tidak Layak (Skor Rendah)',
            default => 'Belum dinilai',
        };
        $langkah = [
            ['Disetujui & nomor surat terbit', $s->nomor_surat ? $s->disetujui_at : null],
            ['Dikirim ke ketua untuk tanda tangan', $s->ttd_dikirim_at],
            ['Ditandatangani ketua, scan diterima admin', $s->ttd_kembali_at],
            ['Dikirim ke petani (status Diterima)', $s->diserahkan_at],
        ];
        $input = 'w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm';

        // data lama berstatus Diterima tapi belum dikirim ke petani ditampilkan sebagai Diproses
        $statusSekarang = old('status', ($s->status === 'diterima' && ! $s->diserahkan_at) ? 'diproses' : $s->status);
        $opsiStatus = [
            'diajukan' => 'Diajukan (belum diperiksa)',
            'diproses' => 'Diproses (setujui & siapkan surat)',
            'ditolak' => 'Ditolak',
        ];
        if ($s->diserahkan_at) {
            $opsiStatus['diterima'] = 'Diterima (surat sudah dikirim ke petani)';
        }

        $tahapTerbuka = in_array($s->status, ['diproses', 'diterima'], true) && $s->nomor_surat && ! blank($s->alokasi_volume);
    @endphp

    <a href="{{ route('admin.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Kelola Pengajuan</h1>
            <p class="text-sm text-[#5C6B62]">{{ $s->nama_pemohon ?? $s->user->name }} · {{ $s->usulan_volume_konsumsi }} L/{{ str_replace('_', ' ', $s->volume_periode) }}</p>
        </div>
        <a href="{{ route('admin.surat-solar.show', $s) }}" class="text-sm border border-[#E1DCC9] text-[#1B4332] rounded-full px-4 py-2 hover:border-[#2D6A4F]">
            <i class="bi bi-eye"></i> Lihat Detail
        </a>
    </div>

    @if ($s->spk_skor !== null)
        <div class="flex items-center gap-3 rounded-2xl border px-5 py-4 mb-6 {{ $spkColor }}">
            <i class="bi bi-cpu text-xl"></i>
            <div>
                <p class="font-medium">Rekomendasi SPK: {{ $spkText }}</p>
                <p class="text-sm opacity-80">Skor {{ $s->spk_skor }}/100 (kelengkapan dokumen dan riwayat pengajuan). Keputusan akhir di tangan admin.</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Keputusan --}}
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h3 class="font-semibold text-[#1B4332] mb-4">1. Keputusan & Data Surat</h3>

                <form method="POST" action="{{ route('admin.surat-solar.update', $s) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">Status</label>
                            <select name="status" class="{{ $input }}">
                                @foreach ($opsiStatus as $nilai => $label)
                                    <option value="{{ $nilai }}" @selected($statusSekarang === $nilai)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-[#5C6B62] mt-1">Menjadi "Diterima" otomatis saat surat dikirim ke petani.</p>
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Nomor Surat</label>
                            @if ($s->nomor_surat)
                                <div class="{{ $input }} bg-[#F5F3EC] text-[#1B4332] font-medium">{{ $s->nomor_surat }}</div>
                            @else
                                <div class="{{ $input }} border-dashed text-[#8C9086] italic">Dibuat otomatis saat "Diproses"</div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-xl bg-[#F5F3EC] p-4 space-y-4">
                        <p class="text-sm font-medium text-[#1B4332]">Hasil verifikasi
                            <span class="font-normal text-[#5C6B62]">· terisi otomatis, ubah hanya jika perlu</span>
                        </p>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm mb-1">Alokasi Volume (Liter per Minggu)</label>
                                <input type="number" step="0.01" min="0" name="alokasi_volume" value="{{ old('alokasi_volume', $s->alokasi_volume ?? $isian['alokasi_volume']) }}" class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Berlaku Sampai</label>
                                <input type="date" name="berlaku_sampai" value="{{ old('berlaku_sampai', $s->berlaku_sampai?->format('Y-m-d') ?? $isian['berlaku_sampai']) }}" class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Tempat Pengambilan</label>
                                <input type="text" name="tempat_pengambilan" value="{{ old('tempat_pengambilan', $s->tempat_pengambilan ?? $isian['tempat_pengambilan']) }}" class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Nomor Penyalur</label>
                                <input type="text" name="nomor_penyalur" value="{{ old('nomor_penyalur', $s->nomor_penyalur ?? $isian['nomor_penyalur']) }}" class="{{ $input }} bg-white">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm mb-1">Alamat Penyalur</label>
                                <input type="text" name="alamat_penyalur" value="{{ old('alamat_penyalur', $s->alamat_penyalur ?? $isian['alamat_penyalur']) }}" class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-sm mb-1">Alat Pembelian</label>
                                <input type="text" name="alat_pembelian" value="{{ old('alat_pembelian', $s->alat_pembelian ?? $isian['alat_pembelian']) }}" class="{{ $input }} bg-white">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm mb-1">Catatan Admin <span class="text-[#5C6B62]">(wajib jika ditolak)</span></label>
                        <textarea name="catatan_admin" rows="3" class="{{ $input }}">{{ old('catatan_admin', $s->catatan_admin) }}</textarea>
                    </div>

                    <button type="submit" class="bg-[#1B4332] text-white text-sm px-5 py-2 rounded-full">Simpan Keputusan</button>
                </form>
            </div>

            {{-- 2. Tanda tangan & pengiriman --}}
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h3 class="font-semibold text-[#1B4332] mb-1">2. Tanda Tangan & Pengiriman</h3>
                <p class="text-sm text-[#5C6B62] mb-5">Alur: admin → ketua (tanda tangan basah, scan) → admin → petani.</p>

                @if (! $tahapTerbuka)
                    <div class="text-sm text-[#8C9086] space-y-1">
                        <p class="italic">Tahap ini belum terbuka. Yang masih kurang:</p>
                        <ul class="list-disc ml-5 text-amber-700">
                            @if (! in_array($s->status, ['diproses', 'diterima'], true)) <li>Status belum "Diproses". Pilih Diproses lalu simpan keputusan di atas</li> @endif
                            @if (! $s->nomor_surat) <li>Nomor surat belum terbit</li> @endif
                            @if (blank($s->alokasi_volume)) <li>Hasil verifikasi belum tersimpan</li> @endif
                        </ul>
                    </div>
                @else
                    <div class="flex flex-wrap gap-3 mb-5">
                        <a href="{{ route('admin.surat-solar.lembar-kerja', $s) }}" target="_blank" class="inline-flex items-center gap-2 border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full">
                            <i class="bi bi-file-earmark-pdf"></i> Lihat / Cetak Surat Rekomendasi
                        </a>
                    </div>

                    <ol class="space-y-3 mb-5">
                        @foreach ($langkah as [$teks, $waktu])
                            <li class="flex items-center gap-3 text-sm">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs shrink-0 {{ $waktu ? 'bg-emerald-100 text-emerald-700' : 'bg-[#F5F3EC] text-[#8C9086]' }}">
                                    <i class="bi {{ $waktu ? 'bi-check-lg' : 'bi-circle' }}"></i>
                                </span>
                                <span class="{{ $waktu ? 'text-[#14231C]' : 'text-[#8C9086]' }}">{{ $teks }}</span>
                                @if ($waktu) <span class="ml-auto text-xs text-[#5C6B62]">{{ $waktu->translatedFormat('d M Y, H:i') }}</span> @endif
                            </li>
                        @endforeach
                    </ol>

                    {{-- Satu slot tombol, label berganti sesuai tahap --}}
                    @if (! $s->ttd_dikirim_at)
                        <form method="POST" action="{{ route('admin.surat-solar.tahap', $s) }}">
                            @csrf <input type="hidden" name="aksi" value="kirim_ketua">
                            <button class="bg-[#1B4332] text-white text-sm px-5 py-2 rounded-full"><i class="bi bi-send"></i> Kirim ke Ketua</button>
                        </form>
                        <p class="text-xs text-[#5C6B62] mt-2">Surat muncul di halaman ketua untuk dicetak dan ditandatangani. Status tetap Diproses.</p>

                    @elseif (! $s->ttd_kembali_at)
                        <div class="rounded-xl bg-sky-50 border border-sky-200 px-4 py-3 text-sm text-sky-800 mb-4">
                            <i class="bi bi-hourglass-split"></i> Menunggu ketua mengunggah scan surat yang sudah ditandatangani.
                        </div>
                        <p class="text-xs text-[#5C6B62] mb-2">Jika scan diterima dengan cara lain, unggah sendiri:</p>
                        <form method="POST" action="{{ route('admin.surat-solar.upload-surat', $s) }}" enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="file" name="file_surat_resmi" accept=".pdf,.jpg,.jpeg,.png,image/*" class="text-sm flex-1">
                            <button type="submit" class="border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full shrink-0">Unggah</button>
                        </form>

                    @elseif (! $s->diserahkan_at)
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800 mb-4">
                            <i class="bi bi-check-circle"></i> Scan bertanda tangan sudah diterima. Periksa, lalu kirim ke petani.
                            @if ($s->file_surat_resmi)
                                <a href="{{ asset('storage/'.$s->file_surat_resmi) }}" target="_blank" class="underline font-medium ml-1">Lihat scan</a>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.surat-solar.tahap', $s) }}">
                            @csrf <input type="hidden" name="aksi" value="serahkan">
                            <button class="bg-emerald-700 text-white text-sm px-6 py-2.5 rounded-full"><i class="bi bi-send-check"></i> Kirim ke Petani</button>
                        </form>
                        <p class="text-xs text-[#5C6B62] mt-2">Setelah dikirim, status berubah menjadi Diterima dan petani bisa mengunduh surat.</p>

                        <details class="mt-4 text-sm">
                            <summary class="cursor-pointer text-[#5C6B62]">Scan kurang jelas? Ganti scan</summary>
                            <form method="POST" action="{{ route('admin.surat-solar.upload-surat', $s) }}" enctype="multipart/form-data" class="flex items-center gap-3 mt-3">
                                @csrf
                                <input type="file" name="file_surat_resmi" accept=".pdf,.jpg,.jpeg,.png,image/*" class="text-sm flex-1">
                                <button type="submit" class="border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2 rounded-full shrink-0">Ganti</button>
                            </form>
                        </details>

                    @else
                        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
                            <i class="bi bi-check-circle-fill"></i> Surat sudah dikirim ke petani pada {{ $s->diserahkan_at->translatedFormat('d M Y, H:i') }}. Petani dapat mengunduhnya di halaman Surat Resmi.
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 h-fit text-sm space-y-3">
            <h3 class="font-semibold text-[#1B4332]">Ringkasan</h3>
            <div><p class="text-[#5C6B62]">Pemohon</p><p class="font-medium">{{ $s->nama_pemohon ?? $s->user->name }}</p></div>
            <div><p class="text-[#5C6B62]">NIK</p><p class="font-medium">{{ $s->nik }}</p></div>
            <div><p class="text-[#5C6B62]">Poktan</p><p class="font-medium">{{ $s->poktan?->nama_kelompok ?? 'Mandiri (tanpa poktan)' }}</p></div>
            <div><p class="text-[#5C6B62]">Status</p>@include('admin._badge', ['status' => $s->status])</div>
        </div>
    </div>

@endsection