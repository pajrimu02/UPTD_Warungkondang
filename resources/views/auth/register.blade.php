<x-guest-layout>
    <h2 class="font-display text-2xl text-[#1B4332]">Buat Akun</h2>
    <p class="text-sm text-[#5C6B62] mt-1 mb-6">Daftar sebagai petani/nelayan anggota kelompok tani.</p>

    @php
        $input = 'mt-1 w-full border border-[#E1DCC9] rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]';
        $label = 'block text-sm font-medium text-[#14231C]';
    @endphp

    <form method="POST" action="{{ route('register') }}" class="space-y-4"
          x-data="{ konsumen: '{{ old('konsumen_pengguna') }}' }">
        @csrf

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm p-3">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="name" class="{{ $label }}">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="{{ $input }}">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="nik" class="{{ $label }}">NIK</label>
                <input id="nik" type="text" name="nik" maxlength="16" inputmode="numeric" value="{{ old('nik') }}" required class="{{ $input }}">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="no_hp" class="{{ $label }}">No. HP/WA</label>
                <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}" required class="{{ $input }}">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="alamat" class="{{ $label }}">Alamat</label>
            <textarea id="alamat" name="alamat" rows="2" required class="{{ $input }}">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="konsumen_pengguna" class="{{ $label }}">Konsumen Pengguna</label>
            <select id="konsumen_pengguna" name="konsumen_pengguna" required x-model="konsumen" class="{{ $input }} bg-white">
                <option value="">-- Pilih --</option>
                @foreach ($konsumen as $key => $text)
                    <option value="{{ $key }}" @selected(old('konsumen_pengguna') === $key)>{{ $text }}</option>
                @endforeach
            </select>
            @error('konsumen_pengguna') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jenis_usaha" class="{{ $label }}">Jenis Usaha</label>
            <input id="jenis_usaha" type="text" name="jenis_usaha" value="{{ old('jenis_usaha') }}" required
                   placeholder="cth: Budidaya padi, Penangkapan ikan" class="{{ $input }}">
            @error('jenis_usaha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="['usaha_perikanan','transportasi_motor_tempel'].includes(konsumen)" x-cloak>
            <label for="nama_kapal" class="{{ $label }}">Nama Kapal</label>
            <input id="nama_kapal" type="text" name="nama_kapal" value="{{ old('nama_kapal') }}" class="{{ $input }}">
            @error('nama_kapal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="poktan_id" class="{{ $label }}">Kelompok Tani (Poktan)</label>
            <select id="poktan_id" name="poktan_id" required class="{{ $input }} bg-white">
                <option value="">-- Pilih Poktan --</option>
                @foreach ($poktans as $poktan)
                    <option value="{{ $poktan->id }}" @selected(old('poktan_id') == $poktan->id)>{{ $poktan->nama_kelompok }}</option>
                @endforeach
            </select>
            @error('poktan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            @if ($poktans->isEmpty())
                <p class="text-xs text-amber-600 mt-1">Belum ada data Poktan — hubungi admin.</p>
            @endif
        </div>

        <div>
            <label for="email" class="{{ $label }}">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="{{ $input }}">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="password" class="{{ $label }}">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="{{ $input }}">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="{{ $label }}">Ulangi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="{{ $input }}">
                @error('password_confirmation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="w-full bg-[#1B4332] text-white font-semibold py-2.5 rounded-lg hover:bg-[#153629] transition">
            Daftar
        </button>
    </form>

    <p class="text-sm text-[#5C6B62] text-center mt-6">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-[#2D6A4F] font-semibold hover:underline">Masuk di sini</a>
    </p>
</x-guest-layout>