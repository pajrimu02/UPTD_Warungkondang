<x-guest-layout>
    <h2 class="font-display text-2xl text-[#1B4332]">Masuk ke Akun</h2>
    <p class="text-sm text-[#5C6B62] mt-1 mb-6">Akses dashboard untuk ajukan layanan & pantau status.</p>

    @if (session('status'))
        <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-[#14231C]">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 w-full border border-[#E1DCC9] rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]">
            @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-[#14231C]">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-[#2D6A4F] hover:underline">Lupa password?</a>
                @endif
            </div>
            <div class="relative mt-1">
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full border border-[#E1DCC9] rounded-lg pl-3 pr-10 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#2D6A4F]/40 focus:border-[#2D6A4F]">
                <button type="button" onclick="togglePassword('password', 'eye-icon')" aria-label="Tampilkan password"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-[#5C6B62] hover:text-[#1B4332] transition">
                    <i id="eye-icon" class="bi bi-eye text-base"></i>
                </button>
            </div>
            @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-[#5C6B62]">
            <input type="checkbox" name="remember" class="rounded border-[#E1DCC9] text-[#1B4332] focus:ring-[#2D6A4F]/40">
            Ingat saya
        </label>

        <button type="submit" class="w-full bg-[#1B4332] text-white font-semibold py-2.5 rounded-lg hover:bg-[#153629] transition">
            Masuk
        </button>
    </form>

    <p class="text-sm text-[#5C6B62] text-center mt-6">
        Belum punya akun?
        <a href="{{ route('register') }}" class="text-[#2D6A4F] font-semibold hover:underline">Daftar di sini</a>
    </p>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }
    </script>
</x-guest-layout>