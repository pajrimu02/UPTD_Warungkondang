@extends('layouts.user')

@section('title', 'Ajukan Surat Pupuk')

@section('content')

    @php
        $inputClass = 'w-full rounded-lg border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20';
        $pupuk = ['luas' => 'Luas (Ha)', 'urea' => 'Urea (kg)', 'sp36' => 'SP36 (kg)', 'za' => 'ZA (kg)', 'npk' => 'NPK (kg)', 'organik' => 'Organik (kg)'];
        $subsektor = ['Tanaman Pangan', 'Hortikultura', 'Perkebunan', 'Peternakan', 'Petambak'];
    @endphp

    <a href="{{ route('user.surat-pupuk.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i><span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Ajukan Surat Rekomendasi Pupuk</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Data petani diambil dari anggota poktan. Lengkapi yang masih kosong. Kolom bertanda * wajib diisi.</p>

    <div class="flex items-start gap-3 bg-[#EBF5EE] border border-[#2D6A4F]/25 text-[#1B4332] rounded-2xl p-4 mb-6">
        <i class="bi bi-patch-check-fill text-lg text-[#2D6A4F] mt-0.5"></i>
        <div class="text-sm leading-relaxed">
            <p class="font-medium">
                Terdaftar sebagai pengelola Kelompok Tani <strong>{{ $poktan->nama_kelompok }}</strong>
                @if ($poktan->desa)
                    (Desa {{ $poktan->desa->nama_desa ?? $poktan->desa->nama }})
                @endif
            </p>
            <p class="text-xs text-[#2D6A4F] mt-0.5">
                Pengajuan e-RDKK pupuk bersubsidi ini diajukan secara kolektif untuk <strong>{{ $poktan->anggota->count() }} anggota</strong> terdaftar. Centang anggota yang akan diikutsertakan pada periode ini.
            </p>
        </div>
    </div>

    
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
                        <label class="block text-sm mb-1.5">Nama Penyuluh (PPL) *</label>
                        @if ($penyuluhOtomatis)
                            <input type="text" name="nama_penyuluh" value="{{ $penyuluhOtomatis['nama'] }}"
                                readonly class="{{ $inputClass }} bg-[#F5F3EC] text-[#1B4332] font-medium">
                            <p class="text-xs text-[#5C6B62] mt-1">
                                Otomatis sesuai wilayah Desa {{ $poktan->desa?->nama_desa ?? $poktan->desa?->nama }}
                            </p>
                        @else
                            <select name="nama_penyuluh" class="{{ $inputClass }} bg-white">
                                <option value="">-- Pilih Penyuluh Pertanian --</option>
                                @foreach ($penyuluhList as $ppl)
                                    <option value="{{ $ppl['nama'] }}" @selected(old('nama_penyuluh') === $ppl['nama'])>
                                        {{ $ppl['nama'] }} (Desa: {{ implode(', ', $ppl['wilayah_desa'] ?? []) }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
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
                    <label class="block text-sm mb-1.5">Nama Kios Pengecer</label>
                    <select id="nama_kios" name="nama_kios" class="{{ $inputClass }} bg-white">
                        <option value="" data-kode="">-- Pilih Kios Pengecer --</option>
                        @foreach ($kiosList as $kios)
                            <option value="{{ $kios['nama'] }}"
                                    data-kode="{{ $kios['kode'] }}"
                                    @selected(old('nama_kios') === $kios['nama'])>
                                {{ $kios['nama'] }} ({{ $kios['desa'] }})
                            </option>
                        @endforeach
                    </select>
                    @error('nama_kios') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm mb-1.5">Kode Kios Pengecer</label>
                    <input type="text" id="kode_kios" name="kode_kios" value="{{ old('kode_kios') }}"
                           readonly placeholder="Otomatis terisi saat kios dipilih"
                           class="{{ $inputClass }} bg-[#F5F3EC] text-[#5C6B62]">
                    @error('kode_kios') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
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
                    ] as [$f, $label, $tipe, $default])
                        <div>
                            <label class="block text-sm mb-1.5">{{ $label }}</label>
                            <input type="{{ $tipe }}" name="items[{{ $k }}][{{ $f }}]" value="{{ old("items.$k.$f", $default) }}"
                                   @if ($f === 'nik') maxlength="16" inputmode="numeric" @endif class="{{ $inputClass }}">
                            @error("items.$k.$f") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    @endforeach

                    <div>
                        <label class="block text-sm mb-1.5">Subsektor *</label>
                        <select name="items[{{ $k }}][subsektor]" class="{{ $inputClass }} bg-white">
                            @foreach ($subsektor as $opsi)
                                <option value="{{ $opsi }}" @selected(old("items.$k.subsektor", 'Tanaman Pangan') === $opsi)>{{ $opsi }}</option>
                            @endforeach
                        </select>
                        @error("items.$k.subsektor") <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectKios = document.getElementById('nama_kios');
            const inputKode = document.getElementById('kode_kios');

            function syncKodeKios() {
                const selected = selectKios.options[selectKios.selectedIndex];
                inputKode.value = selected ? (selected.getAttribute('data-kode') || '') : '';
            }

            selectKios.addEventListener('change', syncKodeKios);
            syncKodeKios();
        });
    </script>

@endsection