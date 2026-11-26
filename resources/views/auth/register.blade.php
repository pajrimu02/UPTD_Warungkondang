<x-guest-layout>
    <h2 class="font-display text-2xl text-[#1B4332]">Buat Akun</h2>
    <p class="text-sm text-[#5C6B62] mt-1 mb-6">Daftar akun untuk mengakses layanan pertanian UPTD Warungkondang.</p>

    @php
        $inputClass = 'mt-1 w-full border border-[#E1DCC9] rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]';
        $perluKapal = in_array(old('konsumen_pengguna'), ['usaha_perikanan', 'transportasi_motor_tempel'], true);
    @endphp

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-[#14231C]">Nama Lengkap *</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="{{ $inputClass }}">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="nik" class="block text-sm font-medium text-[#14231C]">NIK (16 Digit) *</label>
                <input id="nik" type="text" name="nik" maxlength="16" inputmode="numeric" value="{{ old('nik') }}" required class="{{ $inputClass }}">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="no_hp" class="block text-sm font-medium text-[#14231C]">No. HP/WA *</label>
                <input id="no_hp" type="text" name="no_hp" placeholder="081234567890" value="{{ old('no_hp') }}" required class="{{ $inputClass }}">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="alamat" class="block text-sm font-medium text-[#14231C]">Alamat Lengkap *</label>
            <textarea id="alamat" name="alamat" rows="2" required class="{{ $inputClass }}">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="konsumen_pengguna" class="block text-sm font-medium text-[#14231C]">Konsumen Pengguna *</label>
            <select id="konsumen_pengguna" name="konsumen_pengguna" required class="{{ $inputClass }} bg-white"
                onchange="document.getElementById('wrap_kapal').classList.toggle('hidden', !['usaha_perikanan', 'transportasi_motor_tempel'].includes(this.value))">
                <option value="">-- Pilih Sektor Usaha --</option>
                @foreach ($konsumen as $key => $label)
                    <option value="{{ $key }}" @selected(old('konsumen_pengguna') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('konsumen_pengguna') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jenis_usaha" class="block text-sm font-medium text-[#14231C]">Jenis Usaha *</label>
            <input id="jenis_usaha" type="text" name="jenis_usaha" placeholder="mis. Petani padi, penggilingan gabah" value="{{ old('jenis_usaha') }}" required class="{{ $inputClass }}">
            @error('jenis_usaha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div id="wrap_kapal" class="{{ $perluKapal ? '' : 'hidden' }}">
            <label for="nama_kapal" class="block text-sm font-medium text-[#14231C]">Nama Kapal <span class="text-[#5C6B62] font-normal">(khusus perikanan / motor tempel)</span></label>
            <input id="nama_kapal" type="text" name="nama_kapal" value="{{ old('nama_kapal') }}" class="{{ $inputClass }}">
            @error('nama_kapal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[#14231C]">Email *</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="{{ $inputClass }}">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-sm font-medium text-[#14231C]">Password *</label>
                <div class="relative mt-1">
                    <input id="password" type="password" name="password" required
                           class="w-full border border-[#E1DCC9] rounded-lg pl-3.5 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]">
                    <button type="button" onclick="togglePw('password', this)" aria-label="Tampilkan atau sembunyikan password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#5C6B62] hover:text-[#1B4332]">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#14231C]">Ulangi Password *</label>
                <div class="relative mt-1">
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="w-full border border-[#E1DCC9] rounded-lg pl-3.5 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]">
                    <button type="button" onclick="togglePw('password_confirmation', this)" aria-label="Tampilkan atau sembunyikan konfirmasi password"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#5C6B62] hover:text-[#1B4332]">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
                @error('password_confirmation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="w-full bg-[#1B4332] text-white font-semibold py-2.5 rounded-lg hover:bg-[#153629] transition">Daftar</button>
    </form>

    <p class="text-sm text-[#5C6B62] text-center mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-[#2D6A4F] font-semibold hover:underline">Masuk di sini</a>
    </p>

    <script>
        function togglePw(id, btn) {
            const input = document.getElementById(id);
            const icon = btn.querySelector('i');
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        }
    </script>
</x-guest-layout>