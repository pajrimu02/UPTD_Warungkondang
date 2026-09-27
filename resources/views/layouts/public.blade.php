<!DOCTYPE html>
<html lang="id" class="overflow-x-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'UPTD Pertanian Warungkondang')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-[#F5F3EC] text-[#14231C] overflow-x-hidden">

    <header class="border-b border-[#E1DCC9] bg-[#F5F3EC] sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex items-center justify-between px-6 py-4">
            <a href="{{ route('home') }}" class="font-semibold text-[#1B4332]">UPTD Pertanian Warungkondang</a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('tentang') }}">Tentang Kami</a>

                <div class="relative group">
                    <span class="cursor-pointer">Informasi Publik ▾</span>
                    <div class="absolute hidden group-hover:block bg-white border border-[#E1DCC9] rounded-lg p-2 min-w-[170px] shadow">
                        <a href="{{ route('infopublik.pelayanan') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">Pelayanan</a>
                        <a href="{{ route('infopublik.stokkuota') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">Stok & Kuota</a>
                        <a href="{{ route('infopublik.informasi') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">Informasi</a>
                        <a href="{{ route('infopublik.edukasi') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">Edukasi</a>
                        <a href="{{ route('faq') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">FAQ</a>
                    </div>
                </div>

                <div class="relative group">
                    <span class="cursor-pointer">Galeri ▾</span>
                    <div class="absolute hidden group-hover:block bg-white border border-[#E1DCC9] rounded-lg p-2 min-w-[140px] shadow">
                        <a href="{{ route('galeri.foto') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">Foto</a>
                        <a href="{{ route('galeri.video') }}" class="block px-3 py-2 rounded hover:bg-[#F5F3EC]">Video</a>
                    </div>
                </div>

                <a href="{{ route('kritiksaran.publik') }}">Kritik & Saran</a>
                <a href="{{ route('hubungi') }}">Hubungi Kami</a>
            </nav>

            {{-- Kanan: akun (desktop) + tombol hamburger (mobile) --}}
            <div class="flex items-center gap-3">
                <div class="hidden md:block">
                    @auth
                        <div class="flex items-center gap-3">
                            <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="text-sm font-medium text-[#1B4332]">
                                {{ auth()->user()->name }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Keluar</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Masuk</a>
                    @endauth
                </div>

                {{-- Tombol hamburger — cuma muncul di mobile --}}
                <button id="navToggle" aria-label="Buka menu" class="md:hidden w-10 h-10 flex items-center justify-center rounded-lg border border-[#E1DCC9] text-[#1B4332] text-xl">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>

        {{-- Panel menu mobile --}}
        <div id="navMobile" class="hidden md:hidden border-t border-[#E1DCC9] bg-[#F5F3EC]">
            <div class="px-6 py-4 space-y-1 text-sm">
                <a href="{{ route('home') }}" class="block py-2">Beranda</a>
                <a href="{{ route('tentang') }}" class="block py-2">Tentang Kami</a>

                <p class="pt-3 pb-1 text-xs font-semibold text-[#8C9086] uppercase">Informasi Publik</p>
                <a href="{{ route('infopublik.pelayanan') }}" class="block py-2 pl-3">Pelayanan</a>
                <a href="{{ route('infopublik.stokkuota') }}" class="block py-2 pl-3">Stok & Kuota</a>
                <a href="{{ route('infopublik.informasi') }}" class="block py-2 pl-3">Informasi</a>
                <a href="{{ route('infopublik.edukasi') }}" class="block py-2 pl-3">Edukasi</a>
                <a href="{{ route('faq') }}" class="block py-2 pl-3">FAQ</a>

                <p class="pt-3 pb-1 text-xs font-semibold text-[#8C9086] uppercase">Galeri</p>
                <a href="{{ route('galeri.foto') }}" class="block py-2 pl-3">Foto</a>
                <a href="{{ route('galeri.video') }}" class="block py-2 pl-3">Video</a>

                <a href="{{ route('kritiksaran.publik') }}" class="block py-2 pt-3 border-t border-[#E1DCC9] mt-2">Kritik & Saran</a>
                <a href="{{ route('hubungi') }}" class="block py-2">Hubungi Kami</a>

                <div class="pt-3">
                    @auth
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="block py-2 font-medium text-[#1B4332]">
                            {{ auth()->user()->name }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-center bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full mt-2">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center bg-[#1B4332] text-white text-sm px-4 py-2 rounded-full">Masuk</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-10">
        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <script>
        const navToggle = document.getElementById('navToggle');
        const navMobile = document.getElementById('navMobile');
        navToggle.addEventListener('click', () => {
            navMobile.classList.toggle('hidden');
            const icon = navToggle.querySelector('i');
            icon.classList.toggle('bi-list');
            icon.classList.toggle('bi-x-lg');
        });
    </script>

</body>
</html>