@extends('layouts.user')

@section('title', 'Ajukan Surat Pupuk')

@section('content')

    @php
        $inputClass = 'w-full rounded-lg border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20';
        $pupuk = ['luas' => 'Luas (Ha)', 'urea' => 'Urea (kg)', 'sp36' => 'SP36 (kg)', 'za' => 'ZA (kg)', 'npk' => 'NPK (kg)', 'organik' => 'Organik (kg)'];
    @endphp

    <a href="{{ route('user.surat-pupuk.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Ajukan Surat Rekomendasi Pupuk</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Data petani diambil dari anggota poktan. Lengkapi yang masih kosong. Kolom bertanda * wajib diisi.</p>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl p-4 mb-6">
            Ada isian yang belum lengkap atau salah. Periksa kolom yang ditandai.
            @error('items') <p class="mt-1">{{ $message }}</p> @enderror
        </div>
    @endif

    <form method="POST" action="{{ route('user.surat-pupuk.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4"><i class="bi bi-people"></i><span>Data Kelompok</span></p>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1.5">Nama Poktan</label>
                    <input type="text" value="{{ $poktan->nama_kelompok }}" readonly class="{{ $inputClass }} bg-[#F5F3EC]">
                </div>
                <div>
                    <label class="block text-sm mb-1.5">Nama Penyuluh *</label>
                    <input type="text" name="nama_penyuluh" value="{{ old('nama_penyuluh') }}" class="{{ $inputClass }}">
                    @error('nama_penyuluh') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-1.5">Kode Desa</label>
                    <input type="text" name="kode_desa" value="{{ old('kode_desa', $poktan->desa?->kode_desa) }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-sm mb-1.5">Gapoktan <span class="text-[#5C6B62] font-normal">(opsional)</span></label>
                    <input type="text" name="gapoktan" value="{{ old('gapoktan') }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-sm mb-1.5">Kode Kios Pengecer</label>
                    <input type="text" name="kode_kios" value="{{ old('kode_kios') }}" class="{{ $inputClass }}">
                </div>
                <div>
                    <label class="block text-sm mb-1.5">Nama Kios Pengecer</label>
                    <input type="text" name="nama_kios" value="{{ old('nama_kios') }}" class="{{ $inputClass }}">
                </div>
            </div>
        </div>

        @foreach ($poktan->anggota as $n => $a)
            @php
                $k = $a->id;
                $cek = old('items') === null || old("items.$k.include");
            @endphp
            <div class="bg-white border border-[#E1DCC9] rounded-2xl p-6">
                <label class="flex items-center gap-2.5 mb-4 cursor-pointer">
                    <input type="checkbox" name="items[{{ $k }}][include]" value="1" @checked($cek) class="rounded border-[#E1DCC9] text-[#1B4332]">
                    <span class="text-sm font-medium text-[#1B4332]">{{ $n + 1 }}. {{ $a->nama }}</span>
                    <span class="text-xs text-[#5C6B62]">· sertakan dalam pengajuan</span>
                </label>

                <div class="grid sm:grid-cols-2 gap-4">
                    @foreach ([
                        ['nama_petani', 'Nama Petani *', 'text', $a->nama],
                        ['nik', 'NIK (KTP) *', 'text', $a->nik],
                        ['tempat_lahir', 'Tempat Lahir *', 'text', $a->tempat_lahir],
                        ['tanggal_lahir', 'Tanggal Lahir *', 'date', $a->tanggal_lahir?->format('Y-m-d')],
                        ['nama_ibu_kandung', 'Nama Ibu Kandung *', 'text', $a->nama_ibu_kandung],
                        ['subsektor', 'Subsektor *', 'text', 'Tanaman Pangan'],
                    ] as [$f, $label, $tipe, $default])
                        <div>
                            <label class="block text-sm mb-1.5">{{ $label }}</label>
                            <input type="{{ $tipe }}" name="items[{{ $k }}][{{ $f }}]" value="{{ old("items.$k.$f", $default) }}"
                                   @if ($f === 'nik') maxlength="16" inputmode="numeric" @endif class="{{ $inputClass }}">
                            @error("items.$k.$f") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach
                    <div class="sm:col-span-2">
                        <label class="block text-sm mb-1.5">Alamat *</label>
                        <input type="text" name="items[{{ $k }}][alamat]" value="{{ old("items.$k.alamat", $a->alamat) }}" class="{{ $inputClass }}">
                        @error("items.$k.alamat") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-5 space-y-4">
                    @foreach ([0, 1, 2] as $m)
                        <div class="border border-[#E1DCC9] rounded-xl p-4">
                            <p class="text-sm font-medium text-[#1B4332] mb-3">Musim Tanam {{ $m + 1 }}</p>
                            <div class="mb-3">
                                <label class="block text-xs mb-1 text-[#5C6B62]">Komoditas (kosongkan jika tidak ditanami)</label>
                                <input type="text" name="items[{{ $k }}][musim][{{ $m }}][komoditas]" value="{{ old("items.$k.musim.$m.komoditas") }}" placeholder="mis. Padi" class="{{ $inputClass }}">
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                @foreach ($pupuk as $key => $label)
                                    <div>
                                        <label class="block text-xs mb-1 text-[#5C6B62]">{{ $label }}</label>
                                        <input type="number" step="0.01" min="0" name="items[{{ $k }}][musim][{{ $m }}][{{ $key }}]"
                                               value="{{ old("items.$k.musim.$m.$key", $key === 'luas' ? $a->luas_lahan_ha : '') }}" class="{{ $inputClass }}">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end gap-3">
            <a href="{{ route('user.surat-pupuk.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">
                <i class="bi bi-send"></i><span>Kirim Pengajuan</span>
            </button>
        </div>
    </form>

@endsection