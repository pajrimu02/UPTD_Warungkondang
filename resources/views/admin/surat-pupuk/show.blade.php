@extends('layouts.admin')

@section('title', 'Detail Pengajuan Pupuk')

@section('content')

    @php
        $pupuk = ['luas' => 'Luas (Ha)', 'urea' => 'Urea', 'sp36' => 'SP36', 'za' => 'ZA', 'npk' => 'NPK', 'organik' => 'Organik'];
        $fmt = fn ($n) => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',') ?: '0';
    @endphp

    <a href="{{ route('admin.surat-pupuk.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="flex flex-wrap gap-3 mb-6">
        <a href="{{ route('admin.surat-pupuk.lembar-kerja', $suratPupuk) }}" target="_blank" class="inline-flex items-center gap-2 bg-white border border-[#E1DCC9] text-[#1B4332] text-sm px-4 py-2.5 rounded-full">
            <i class="bi bi-file-earmark-text"></i><span>Cetak Lembar Kerja (untuk input RDKK)</span>
        </a>
    </div>

    @if ($suratPupuk->status === 'diterima')
        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 mb-6">
            <h3 class="font-semibold text-[#1B4332] mb-1">Arsip Surat Resmi</h3>
            <p class="text-sm text-[#5C6B62] mb-4">Unggah surat resmi yang sudah ditandatangani supaya pemohon bisa mengunduhnya.</p>

            @if ($suratPupuk->file_surat_resmi)
                <a href="{{ asset('storage/'.$suratPupuk->file_surat_resmi) }}" target="_blank" class="inline-flex items-center gap-2 text-sm text-[#1B4332] underline mb-4">
                    <i class="bi bi-file-earmark-check"></i> Lihat surat resmi yang sudah diarsipkan
                </a>
            @endif

            <form method="POST" action="{{ route('admin.surat-pupuk.upload-surat', $suratPupuk) }}" enctype="multipart/form-data" class="flex items-center gap-3">
                @csrf
                <input type="file" name="file_surat_resmi" accept=".pdf,.jpg,.jpeg,.png" class="text-sm flex-1">
                <button type="submit" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full shrink-0">Upload</button>
            </form>
            @error('file_surat_resmi') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

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
                                        <td class="pr-3">{{ $m['komoditas'] }}</td>
                                        @foreach (array_keys($pupuk) as $k) <td class="pr-3">{{ $fmt($m[$k]) }}</td> @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6 h-fit">
            <h3 class="font-semibold text-[#1B4332] mb-4">Kelola Status</h3>

            <form method="POST" action="{{ route('admin.surat-pupuk.update', $suratPupuk) }}" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm">
                        @foreach (['diajukan', 'diproses', 'diterima', 'ditolak'] as $s)
                            <option value="{{ $s }}" @selected($suratPupuk->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Nomor Surat</label>
                    @if ($suratPupuk->nomor_surat)
                        <div class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm bg-[#F5F3EC] text-[#1B4332] font-medium">{{ $suratPupuk->nomor_surat }}</div>
                    @else
                        <div class="w-full rounded-lg border border-dashed border-[#E1DCC9] px-3 py-2 text-sm text-[#8C9086] italic">Dibuat otomatis saat status "Diterima"</div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm mb-1">Catatan Admin <span class="text-[#5C6B62]">(wajib jika ditolak)</span></label>
                    <textarea name="catatan_admin" rows="4" class="w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm">{{ old('catatan_admin', $suratPupuk->catatan_admin) }}</textarea>
                    @error('catatan_admin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Simpan</button>
            </form>
        </div>
    </div>

@endsection