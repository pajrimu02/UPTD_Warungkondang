@extends('layouts.admin')

@section('title', 'Detail Kelompok Tani')

@section('content')

    @php
        $fmt = fn ($n) => number_format($n, 2, ',', '.');
        $persen = $minLuas > 0 ? min(100, round($totalLuas / $minLuas * 100)) : 0;
        $penuh = $jumlah >= $maksAnggota;
        $input = 'w-full rounded-lg border border-[#E1DCC9] px-3 py-2 text-sm';
        $warnaJabatan = [
            'ketua' => 'bg-[#F6EDC9] text-[#7A5C12]',
            'sekretaris' => 'bg-sky-50 text-sky-700',
            'bendahara' => 'bg-sky-50 text-sky-700',
            'anggota' => 'bg-slate-100 text-slate-600',
        ];
    @endphp

    <a href="{{ route('admin.poktan.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Identitas --}}
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <h2 class="font-semibold text-xl text-[#1B4332]">{{ $poktan->nama_kelompok }}</h2>
                    @include('admin._badge', ['status' => $poktan->status])
                </div>
                <div class="mb-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.poktan.sk-preview', $poktan) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] text-[#1B4332] rounded-full px-3 py-1.5 hover:border-[#2D6A4F]">
                        <i class="bi bi-file-earmark-pdf"></i> {{ $poktan->status === 'disetujui' ? 'Lihat PDF SK' : 'Lihat draf SK' }}
                    </a>
                    @if ($poktan->file_sk_resmi)
                        <a href="{{ asset('storage/' . $poktan->file_sk_resmi) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-full px-3 py-1.5">
                            <i class="bi bi-patch-check"></i> Lihat Scan SK Bertanda Tangan
                        </a>
                    @endif
                </div>
                <dl class="grid sm:grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-[#5C6B62]">Desa</dt><dd class="font-medium">{{ $poktan->desa?->nama ?? '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Ketua</dt><dd class="font-medium">{{ $poktan->nama_ketua ?: '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-[#5C6B62]">Alamat</dt><dd class="font-medium">{{ $poktan->alamat ?: '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Diajukan oleh</dt><dd class="font-medium">{{ $poktan->pengaju?->name ?? '—' }}</dd></div>
                    <div><dt class="text-[#5C6B62]">Dibuat</dt><dd class="font-medium">{{ $poktan->created_at->translatedFormat('d M Y') }}</dd></div>
                    @if ($poktan->nomor_sk)
                        <div class="sm:col-span-2"><dt class="text-[#5C6B62]">Nomor SK Pengukuhan</dt><dd class="font-semibold text-[#1B4332]">{{ $poktan->nomor_sk }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Ringkasan --}}
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <p class="text-xs text-[#8C9086]">Anggota</p>
                    <p class="font-serif text-2xl text-[#1B4332] mt-1">{{ $jumlah }} <span class="text-base text-[#8C9086]">/ {{ $maksAnggota }}</span></p>
                </div>
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <p class="text-xs text-[#8C9086]">Total luas lahan</p>
                    <p class="font-serif text-2xl {{ $totalLuas >= $minLuas ? 'text-[#2D6A4F]' : 'text-[#1B4332]' }} mt-1">{{ $fmt($totalLuas) }} ha</p>
                    <div class="h-2 rounded-full bg-[#EAF2EC] mt-3 overflow-hidden">
                        <div class="h-full rounded-full {{ $totalLuas >= $minLuas ? 'bg-[#2D6A4F]' : 'bg-[#C9A227]' }}" style="width: {{ $persen }}%"></div>
                    </div>
                    <p class="text-[11px] mt-1.5 text-[#8C9086]">Minimal {{ $fmt($minLuas) }} ha</p>
                </div>
                <div class="bg-white border border-[#E1DCC9] rounded-2xl p-5">
                    <p class="text-xs text-[#8C9086]">Sisa batas desa</p>
                    @if ($sisaDesa !== null)
                        <p class="font-serif text-2xl {{ $totalLuas > $sisaDesa ? 'text-red-600' : 'text-[#1B4332]' }} mt-1">{{ $fmt($sisaDesa) }} ha</p>
                        <p class="text-[11px] mt-3 text-[#8C9086]">{{ $poktan->desa->memakaiBatasBawaan() ? 'Batas sementara' : 'Luas baku sawah' }}</p>
                    @else
                        <p class="font-serif text-2xl text-[#8C9086] mt-1">—</p>
                        <p class="text-[11px] mt-3 text-[#8C9086]">Belum ada batas desa</p>
                    @endif
                </div>
            </div>

            {{-- Anggota --}}
            <div id="anggota" class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h2 class="font-semibold text-xl text-[#1B4332] mb-4">Anggota</h2>

                <div class="divide-y divide-[#E1DCC9]">
                    @forelse ($poktan->anggota as $a)
                        <div class="py-4 first:pt-0 {{ $a->status === 'nonaktif' ? 'opacity-60' : '' }}">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="font-medium text-[#14231C]">{{ $a->nama ?: '(belum diisi)' }}</p>
                                        <span class="text-[11px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-0.5 {{ $warnaJabatan[$a->jabatan] ?? $warnaJabatan['anggota'] }}">{{ $a->jabatan }}</span>
                                        @if ($a->status === 'menunggu')
                                            <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-amber-50 text-amber-700">menunggu diisi</span>
                                        @elseif ($a->status === 'nonaktif')
                                            <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-slate-100 text-slate-600">nonaktif</span>
                                        @endif
                                        @if ($a->dibantu_petugas)
                                            <span class="text-[11px] font-semibold rounded-full px-2.5 py-0.5 bg-violet-50 text-violet-700">dibantu petugas</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#5C6B62] mt-1">NIK {{ $a->nik ?: '—' }} · KK {{ $a->nomor_kk ?: '—' }} · {{ $a->no_hp ?: '—' }}</p>
                                    @if ($a->tempat_lahir)
                                        <p class="text-xs text-[#5C6B62] mt-0.5">{{ $a->tempat_lahir }}, {{ $a->tanggal_lahir?->format('d-m-Y') }} · Ibu: {{ $a->nama_ibu_kandung }}</p>
                                    @endif
                                    <p class="text-xs text-[#8C9086] mt-0.5">{{ $a->alamat ?: '—' }} · <b class="text-[#14231C]">{{ $fmt($a->luas_lahan_ha ?? 0) }} ha</b></p>
                                </div>

                                @if ($bolehUbah && $a->jabatan !== 'ketua')
                                    <form method="POST" action="{{ route('admin.poktan.anggota.destroy', [$poktan, $a]) }}" onsubmit="return confirm('Hapus anggota {{ addslashes($a->nama) }} beserta berkasnya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Hapus anggota" class="w-9 h-9 flex items-center justify-center rounded-full text-[#8C9086] hover:text-red-600 hover:bg-red-50 transition">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 mt-2.5">
                                @foreach (['ktp' => 'KTP', 'kk' => 'KK', 'sppt' => 'SPPT'] as $j => $l)
                                    @if ($a->{'file_' . $j})
                                        <a href="{{ route('admin.poktan.berkas', [$poktan, $a, $j]) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs border border-[#E1DCC9] rounded-full px-3 py-1 text-[#1B4332] hover:border-[#2D6A4F]">
                                            <i class="bi bi-paperclip"></i> {{ $l }}
                                        </a>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs border border-red-200 bg-red-50 rounded-full px-3 py-1 text-red-700"><i class="bi bi-x-circle"></i> {{ $l }} belum ada</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="py-6 text-sm text-center text-[#5C6B62]">Belum ada anggota.</p>
                    @endforelse
                </div>

                {{-- Dibantu petugas --}}
                @if ($bolehUbah)
                    <details class="mt-5 border-t border-[#E1DCC9] pt-5" @if ($errors->any()) open @endif>
                        <summary class="cursor-pointer text-sm font-medium text-[#1B4332]">
                            <i class="bi bi-person-plus"></i> Tambah anggota (dibantu petugas)
                        </summary>
                        <p class="text-xs text-[#5C6B62] mt-2 mb-4">Untuk petani yang tidak bisa mendaftar sendiri. Data dan berkas diisi petugas dan ditandai "dibantu petugas".</p>

                        @if ($errors->any())
                            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                                <ul class="list-disc ml-5 space-y-0.5">@foreach ($errors->all() as $pesan)<li>{{ $pesan }}</li>@endforeach</ul>
                            </div>
                        @endif

                        @if ($penuh)
                            <p class="text-sm text-[#5C6B62]">Jumlah anggota sudah mencapai batas maksimal.</p>
                        @else
                            <form method="POST" action="{{ route('admin.poktan.anggota.store', $poktan) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div class="grid sm:grid-cols-2 gap-4">
                                    @foreach ([
                                        ['nama', 'Nama Lengkap', 'text'], ['nik', 'NIK (16 digit)', 'text'],
                                        ['nomor_kk', 'Nomor KK (16 digit)', 'text'], ['nama_ibu_kandung', 'Nama Ibu Kandung', 'text'],
                                        ['tempat_lahir', 'Tempat Lahir', 'text'], ['tanggal_lahir', 'Tanggal Lahir', 'date'],
                                        ['no_hp', 'No. HP', 'text'], ['luas_lahan_ha', 'Luas Lahan (ha) sesuai SPPT', 'number'],
                                    ] as [$f, $label, $tipe])
                                        <div>
                                            <label class="block text-sm mb-1">{{ $label }} *</label>
                                            <input type="{{ $tipe }}" name="{{ $f }}" value="{{ old($f) }}" @if ($tipe === 'number') step="0.01" min="0.01" @endif class="{{ $input }}">
                                        </div>
                                    @endforeach
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm mb-1">Alamat *</label>
                                        <input type="text" name="alamat" value="{{ old('alamat') }}" class="{{ $input }}">
                                    </div>
                                    <div>
                                        <label class="block text-sm mb-1">Jabatan *</label>
                                        <select name="jabatan" class="{{ $input }} bg-white">
                                            @foreach (['anggota' => 'Anggota', 'sekretaris' => 'Sekretaris', 'bendahara' => 'Bendahara', 'ketua' => 'Ketua'] as $v => $l)
                                                <option value="{{ $v }}" @selected(old('jabatan', 'anggota') === $v)>{{ $l }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="grid sm:grid-cols-3 gap-3">
                                    @foreach (['file_ktp' => 'KTP', 'file_kk' => 'KK', 'file_sppt' => 'SPPT Sawah'] as $f => $l)
                                        <div>
                                            <label class="block text-xs mb-1 text-[#5C6B62]">{{ $l }} *</label>
                                            <input type="file" name="{{ $f }}" required accept=".jpg,.jpeg,.png,.pdf" class="text-xs w-full">
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-[#8C9086]">JPG, PNG, atau PDF, maksimal 2 MB per berkas.</p>
                                <button type="submit" class="bg-[#1B4332] text-white text-sm px-5 py-2 rounded-full">Simpan Anggota</button>
                            </form>
                        @endif
                    </details>
                @endif
            </div>
        </div>

        {{-- Kolom kanan: syarat dan keputusan --}}
        <div class="space-y-6 h-fit">
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h3 class="font-semibold text-[#1B4332] mb-4">Syarat Persetujuan</h3>
                <ul class="space-y-3">
                    @foreach ($syarat as $s)
                        <li class="flex gap-2.5 text-sm">
                            <i class="bi {{ $s['ok'] ? 'bi-check-circle-fill text-emerald-600' : 'bi-x-circle-fill text-red-500' }} mt-0.5"></i>
                            <div>
                                <p class="{{ $s['ok'] ? 'text-[#14231C]' : 'text-red-700 font-medium' }}">{{ $s['label'] }}</p>
                                <p class="text-xs text-[#8C9086]">{{ $s['ket'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <h3 class="font-semibold text-[#1B4332] mb-4">Keputusan & Penerbitan SK</h3>

                @if ($poktan->status === 'diajukan')
                    <form method="POST" action="{{ route('admin.poktan.update', $poktan) }}" class="space-y-4">
                        @csrf @method('PUT')

                        <div>
                            <label class="block text-sm mb-1">Catatan <span class="text-[#5C6B62]">(wajib jika ditolak)</span></label>
                            <textarea name="catatan_admin" rows="4" class="{{ $input }}">{{ old('catatan_admin') }}</textarea>
                            @error('catatan_admin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" name="keputusan" value="ditolak" class="flex-1 bg-red-50 text-red-700 border border-red-200 text-sm px-4 py-2 rounded-full">Tolak</button>
                            <button type="submit" name="keputusan" value="disetujui" class="flex-1 bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Setujui</button>
                        </div>
                        <p class="text-xs text-[#8C9086]">Saat disetujui, nomor SK otomatis dibuat dan akun semua anggota terhubung ke poktan.</p>
                    </form>
                @elseif ($poktan->status === 'draft')
                    <p class="text-sm text-[#5C6B62]">Poktan masih <b>draft</b>: ketua belum mengajukan. Admin dapat membantu menambah anggota, tetapi keputusan baru bisa diambil setelah diajukan.</p>
                @elseif ($poktan->status === 'disetujui')
                    <p class="text-sm text-[#5C6B62]">
                        Status: <b>Disetujui</b>@if ($poktan->disetujui_at) pada {{ $poktan->disetujui_at->translatedFormat('d M Y') }}@endif.<br>
                        Nomor SK: <b class="text-[#1B4332]">{{ $poktan->nomor_sk ?? '-' }}</b>
                    </p>
                    @if ($poktan->catatan_admin)
                        <p class="text-sm text-[#5C6B62] mt-2"><span class="font-medium text-[#1B4332]">Catatan:</span> {{ $poktan->catatan_admin }}</p>
                    @endif

                    <div class="mt-4 pt-4 border-t border-[#E1DCC9] space-y-2">
                        @if (! $poktan->ttd_dikirim_at)
                            <form method="POST" action="{{ route('admin.poktan.tahap', $poktan) }}">
                                @csrf
                                <input type="hidden" name="aksi" value="kirim_kepala">
                                <button type="submit" class="w-full bg-[#1B4332] text-white text-sm px-4 py-2.5 rounded-full">
                                    Kirim SK ke Kepala UPTD (TTD)
                                </button>
                            </form>
                        @elseif (! $poktan->ttd_kembali_at)
                            <p class="text-xs bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-3.5 py-2.5">
                                <i class="bi bi-hourglass-split"></i> Menunggu tanda tangan Kepala UPTD (dikirim {{ $poktan->ttd_dikirim_at->translatedFormat('d M Y, H:i') }}).
                            </p>
                        @elseif (! $poktan->diserahkan_at)
                            <form method="POST" action="{{ route('admin.poktan.tahap', $poktan) }}">
                                @csrf
                                <input type="hidden" name="aksi" value="kirim_petani">
                                <button type="submit" class="w-full bg-[#2D6A4F] text-white text-sm px-4 py-2.5 rounded-full">
                                    Kirim SK Resmi ke Petani
                                </button>
                            </form>
                        @else
                            <p class="text-xs bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-3.5 py-2.5">
                                <i class="bi bi-check-circle-fill"></i> SK resmi sudah dikirim ke Poktan pada {{ $poktan->diserahkan_at->translatedFormat('d M Y, H:i') }}.
                            </p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-[#5C6B62]">Status: <b>{{ ucfirst($poktan->status) }}</b>.</p>
                    @if ($poktan->catatan_admin)
                        <p class="text-sm text-[#5C6B62] mt-3"><span class="font-medium text-[#1B4332]">Catatan:</span> {{ $poktan->catatan_admin }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

@endsection