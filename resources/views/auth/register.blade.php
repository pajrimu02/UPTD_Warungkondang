<x-guest-layout>
    <h2 class="font-display text-2xl text-[#1B4332]">Buat Akun</h2>
    <p class="text-sm text-[#5C6B62] mt-1 mb-6">Daftar sebagai petani/anggota kelompok tani.</p>

    @php $inputClass = 'mt-1 w-full border border-[#E1DCC9] rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]'; @endphp

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-[#14231C]">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="{{ $inputClass }}">
            @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="nik" class="block text-sm font-medium text-[#14231C]">NIK</label>
                <input id="nik" type="text" name="nik" maxlength="16" value="{{ old('nik') }}" required class="{{ $inputClass }}">
                @error('nik') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="no_hp" class="block text-sm font-medium text-[#14231C]">No. HP/WA</label>
                <input id="no_hp" type="text" name="no_hp" placeholder="081234567890" value="{{ old('no_hp') }}" required class="{{ $inputClass }}">
                @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label for="alamat" class="block text-sm font-medium text-[#14231C]">Alamat</label>
            <textarea id="alamat" name="alamat" rows="2" required class="{{ $inputClass }}">{{ old('alamat') }}</textarea>
            @error('alamat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="konsumen_pengguna" class="block text-sm font-medium text-[#14231C]">Konsumen Pengguna</label>
            <select id="konsumen_pengguna" name="konsumen_pengguna" required class="{{ $inputClass }} bg-white"
                onchange="document.getElementById('wrap_kapal').classList.toggle('hidden', this.value !== 'usaha_perikanan')">
                <option value="">-- Pilih --</option>
                @foreach ($konsumen as $key => $label)
                    <option value="{{ $key }}" @selected(old('konsumen_pengguna') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('konsumen_pengguna') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="jenis_usaha" class="block text-sm font-medium text-[#14231C]">Jenis Usaha</label>
            <input id="jenis_usaha" type="text" name="jenis_usaha" placeholder="mis. Petani padi" value="{{ old('jenis_usaha') }}" required class="{{ $inputClass }}">
            @error('jenis_usaha') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div id="wrap_kapal" class="{{ old('konsumen_pengguna') == 'usaha_perikanan' ? '' : 'hidden' }}">
            <label for="nama_kapal" class="block text-sm font-medium text-[#14231C]">Nama Kapal <span class="text-[#5C6B62] font-normal">(khusus usaha perikanan)</span></label>
            <input id="nama_kapal" type="text" name="nama_kapal" value="{{ old('nama_kapal') }}" class="{{ $inputClass }}">
            @error('nama_kapal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="poktan_id" class="block text-sm font-medium text-[#14231C]">Kelompok Tani (Poktan)</label>
            <select id="poktan_id" name="poktan_id" required class="{{ $inputClass }} bg-white">
                <option value="">-- Pilih Poktan --</option>
                @foreach ($poktans as $poktan)
                    <option value="{{ $poktan->id }}" @selected(old('poktan_id') == $poktan->id)>{{ $poktan->nama_kelompok }}</option>
                @endforeach
            </select>
            @error('poktan_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-[#14231C]">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="{{ $inputClass }}">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-sm font-medium text-[#14231C]">Password</label>
                <input id="password" type="password" name="password" required class="{{ $inputClass }}">
                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-[#14231C]">Ulangi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="{{ $inputClass }}">
                @error('password_confirmation') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <button type="submit" class="w-full bg-[#1B4332] text-white font-semibold py-2.5 rounded-lg hover:bg-[#153629] transition">Daftar</button>
    </form>

    <p class="text-sm text-[#5C6B62] text-center mt-6">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-[#2D6A4F] font-semibold hover:underline">Masuk di sini</a>
    </p>
</x-guest-layout>