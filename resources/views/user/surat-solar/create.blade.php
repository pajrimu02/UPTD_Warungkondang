@extends('layouts.user')

@section('title', 'Ajukan Surat Solar')

@section('content')

    <a href="{{ route('user.surat-solar.index') }}" class="flex items-center gap-1.5 text-sm text-[#5C6B62] mb-4">
        <i class="bi bi-arrow-left"></i>
        <span>Kembali</span>
    </a>

    <h1 class="text-2xl font-semibold text-[#1B4332] mb-1">Ajukan Surat Rekomendasi Solar</h1>
    <p class="text-sm text-[#5C6B62] mb-6">Lengkapi data di bawah ini. Semua kolom bertanda * wajib diisi.</p>

    <form method="POST" action="{{ route('user.surat-solar.store') }}" enctype="multipart/form-data" class="bg-white border border-[#E1DCC9] rounded-2xl p-6 space-y-8">
        @csrf

        @php
            $inputClass = 'w-full rounded-lg border border-[#E1DCC9] px-3.5 py-2.5 text-sm focus:outline-none focus:border-[#2D6A4F] focus:ring-2 focus:ring-[#2D6A4F]/20';
        @endphp

        {{-- Data pemohon --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-person-vcard"></i>
                <span>Data Pemohon</span>
            </p>

            <div class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Nama *</label>
                        <input type="text" name="nama_pemohon" value="{{ old('nama_pemohon') }}" class="{{ $inputClass }}">
                        @error('nama_pemohon') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">NIK *</label>
                        <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" class="{{ $inputClass }}">
                        @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">Alamat *</label>
                    <input type="text" name="alamat" value="{{ old('alamat') }}" class="{{ $inputClass }}">
                    @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">No. HP/WA *</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="{{ $inputClass }}">
                    @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">Konsumen Pengguna *</label>
                    <select name="status_konsumen" id="status_konsumen" class="{{ $inputClass }} bg-white" onchange="document.getElementById('wrap_kapal').classList.toggle('hidden', this.value !== 'usaha_perikanan')">
                        <option value="">-- Pilih --</option>
                        <option value="usaha_pertanian" @selected(old('status_konsumen') == 'usaha_pertanian')>Usaha Pertanian</option>
                        <option value="usaha_perikanan" @selected(old('status_konsumen') == 'usaha_perikanan')>Usaha Perikanan</option>
                        <option value="transportasi_motor_tempel" @selected(old('status_konsumen') == 'transportasi_motor_tempel')>Transportasi Motor Tempel</option>
                        <option value="pekerjaan_umum" @selected(old('status_konsumen') == 'pekerjaan_umum')>Pekerjaan Umum</option>
                    </select>
                    @error('status_konsumen') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">Jenis Usaha *</label>
                    <input type="text" name="jenis_usaha" value="{{ old('jenis_usaha') }}" placeholder="mis. Petani padi, penggilingan gabah, dll" class="{{ $inputClass }}">
                    @error('jenis_usaha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div id="wrap_kapal" class="{{ old('status_konsumen') == 'usaha_perikanan' ? '' : 'hidden' }}">
                    <label class="block text-sm mb-1.5 text-[#14231C]">Nama Kapal <span class="text-[#5C6B62] font-normal">(khusus usaha perikanan)</span></label>
                    <input type="text" name="nama_kapal" value="{{ old('nama_kapal') }}" class="{{ $inputClass }}">
                    @error('nama_kapal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        {{-- Data alat/mesin --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-gear-wide-connected"></i>
                <span>Data Alat/Mesin</span>
            </p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">Jenis Alat/Mesin *</label>
                    <select name="jenis_alat_mesin" id="jenis_alat_mesin" class="{{ $inputClass }} bg-white" onchange="document.getElementById('wrap_jenis_lain').classList.toggle('hidden', this.value !== 'lainnya')">
                        <option value="">-- Pilih --</option>
                        <option value="traktor_roda_dua" @selected(old('jenis_alat_mesin') == 'traktor_roda_dua')>Traktor Roda Dua (Hand Tractor)</option>
                        <option value="traktor_roda_empat" @selected(old('jenis_alat_mesin') == 'traktor_roda_empat')>Traktor Roda Empat</option>
                        <option value="pompa_air" @selected(old('jenis_alat_mesin') == 'pompa_air')>Pompa Air</option>
                        <option value="rmu" @selected(old('jenis_alat_mesin') == 'rmu')>RMU (Rice Milling Unit / Mesin Penggilingan Padi)</option>
                        <option value="mesin_pemotong_rumput" @selected(old('jenis_alat_mesin') == 'mesin_pemotong_rumput')>Mesin Pemotong Rumput</option>
                        <option value="power_thresher" @selected(old('jenis_alat_mesin') == 'power_thresher')>Power Thresher (Mesin Perontok Padi)</option>
                        <option value="lainnya" @selected(old('jenis_alat_mesin') == 'lainnya')>Lainnya</option>
                    </select>
                    @error('jenis_alat_mesin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div id="wrap_jenis_lain" class="{{ old('jenis_alat_mesin') == 'lainnya' ? '' : 'hidden' }}">
                    <label class="block text-sm mb-1.5 text-[#14231C]">Sebutkan Jenis Alat/Mesin Lainnya</label>
                    <input type="text" name="jenis_alat_mesin_lainnya" value="{{ old('jenis_alat_mesin_lainnya') }}" class="{{ $inputClass }}">
                </div>

                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">Fungsi Alat/Mesin *</label>
                    <input type="text" name="fungsi_alat_mesin" value="{{ old('fungsi_alat_mesin') }}" placeholder="mis. Menggiling gabah menjadi beras" class="{{ $inputClass }}">
                    @error('fungsi_alat_mesin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Jumlah Alat/Mesin *</label>
                        <input type="number" min="1" name="jumlah_alat_mesin" value="{{ old('jumlah_alat_mesin', 1) }}" class="{{ $inputClass }}">
                        @error('jumlah_alat_mesin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Daya Alat/Mesin</label>
                        <input type="text" name="daya_alat_mesin" value="{{ old('daya_alat_mesin') }}" placeholder="mis. 8.5 HP" class="{{ $inputClass }}">
                        @error('daya_alat_mesin') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Lama Penggunaan Alat/Mesin</label>
                        <input type="text" name="lama_penggunaan" value="{{ old('lama_penggunaan') }}" placeholder="mis. 8 jam/hari" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Lama Operasi Alat/Mesin</label>
                        <input type="text" name="lama_operasi" value="{{ old('lama_operasi') }}" placeholder="mis. 6 hari/minggu" class="{{ $inputClass }}">
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        {{-- Volume BBM --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-fuel-pump"></i>
                <span>Usulan Volume BBM</span>
            </p>

            <div class="space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Usulan Volume Konsumsi (Liter) *</label>
                        <input type="number" step="0.01" name="usulan_volume_konsumsi" value="{{ old('usulan_volume_konsumsi') }}" class="{{ $inputClass }}">
                        @error('usulan_volume_konsumsi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-1.5 text-[#14231C]">Periode *</label>
                        <select name="volume_periode" class="{{ $inputClass }} bg-white">
                            <option value="minggu" @selected(old('volume_periode') == 'minggu')>Per Minggu</option>
                            <option value="bulan" @selected(old('volume_periode') == 'bulan')>Per Bulan</option>
                            <option value="tiga_bulan" @selected(old('volume_periode') == 'tiga_bulan')>Per 3 Bulan (1 Musim Tanam)</option>
                        </select>
                        @error('volume_periode') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-1.5 text-[#14231C]">Estimasi Sisa JBT/JBKP (Liter) <span class="text-[#5C6B62] font-normal">(opsional)</span></label>
                    <input type="number" step="0.01" name="estimasi_sisa_liter" value="{{ old('estimasi_sisa_liter') }}" class="{{ $inputClass }}">
                </div>
            </div>
        </div>

        <hr class="border-[#E1DCC9]">

        {{-- Berkas pendukung --}}
        <div>
            <p class="flex items-center gap-2 text-sm font-medium text-[#1B4332] mb-4">
                <i class="bi bi-paperclip"></i>
                <span>Berkas Pendukung</span>
            </p>

            <div class="space-y-3">
                @foreach ([
                    'file_ktp' => 'Upload KTP',
                    'file_sku' => 'Upload Surat Keterangan Usaha (SKU)',
                    'file_foto_mesin' => 'Upload Foto Alat/Mesin yang Digunakan',
                ] as $field => $label)
                    <label class="flex items-center gap-3 border border-dashed border-[#E1DCC9] rounded-xl px-4 py-4 cursor-pointer hover:border-[#2D6A4F] transition-colors">
                        <i class="bi bi-cloud-arrow-up text-xl text-[#5C6B62]"></i>
                        <span class="text-sm text-[#5C6B62]">{{ $label }} <span class="text-xs">(JPG/PNG{{ $field === 'file_foto_mesin' ? '' : '/PDF' }}, maks 2MB) *</span></span>
                        <input type="file" name="{{ $field }}" class="hidden" onchange="this.previousElementSibling.textContent = this.files[0]?.name ?? this.previousElementSibling.textContent">
                    </label>
                    @error($field) <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('user.surat-solar.index') }}" class="text-sm text-[#5C6B62] px-4 py-2.5">Batal</a>
            <button type="submit" class="flex items-center gap-2 bg-[#1B4332] text-white text-sm px-6 py-2.5 rounded-full">
                <i class="bi bi-send"></i>
                <span>Kirim Pengajuan</span>
            </button>
        </div>
    </form>

@endsection