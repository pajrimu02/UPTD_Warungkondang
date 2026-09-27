<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - UPTD Pertanian Warungkondang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-[#F5F3EC] text-[#14231C]">

    <header class="bg-white border-b border-[#E1DCC9] sticky top-0 z-20">
        <div class="max-w-4xl mx-auto flex items-center justify-between px-4 py-4">
            <a href="{{ route('user.dashboard') }}" class="font-semibold text-[#1B4332]">UPTD Pertanian Warungkondang</a>

            {{-- Nav desktop --}}
            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Dashboard</a>
                <a href="{{ route('user.surat-solar.index') }}" class="{{ request()->routeIs('user.surat-solar.*') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Surat Solar</a>
                <a href="{{ route('user.profil') }}" class="{{ request()->routeIs('user.profil*') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Profil</a>
                <a href="{{ route('user.kritiksaran.create') }}" class="{{ request()->routeIs('user.kritiksaran.*') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Kritik & Saran</a>
            </nav>

            {{-- Kanan: nama user + logout (desktop), hamburger (mobile) --}}
            <div class="flex items-center gap-3">
                <span class="hidden sm:inline text-sm text-[#5C6B62]">{{ auth()->user()->name }}</span>

                <form method="POST" action="{{ route('logout') }}" class="hidden md:block">
                    @csrf
                    <button type="submit" class="text-sm bg-[#1B4332] text-white px-4 py-2 rounded-full">Keluar</button>
                </form>

                {{-- Tombol hamburger, cuma muncul di mobile --}}
                <button
                    type="button"
                    onclick="document.getElementById('mobile-menu').classList.toggle('hidden')"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg border border-[#E1DCC9]"
                    aria-label="Buka menu"
                >
                    <i class="bi bi-list text-xl text-[#1B4332]"></i>
                </button>
            </div>
        </div>

        {{-- Menu mobile (dropdown full-width), default tersembunyi --}}
        <nav id="mobile-menu" class="hidden md:hidden border-t border-[#E1DCC9] bg-white">
            <div class="px-4 py-3 flex flex-col gap-1 text-sm">
                <a href="{{ route('user.dashboard') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('user.dashboard') ? 'bg-[#F5F3EC] text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Dashboard</a>
                <a href="{{ route('user.surat-solar.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('user.surat-solar.*') ? 'bg-[#F5F3EC] text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Surat Solar</a>
                <a href="{{ route('user.profil') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('user.profil*') ? 'bg-[#F5F3EC] text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Profil</a>
                <a href="{{ route('user.kritiksaran.create') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('user.kritiksaran.*') ? 'bg-[#F5F3EC] text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Kritik & Saran</a>

                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-[#E1DCC9] mt-1">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-red-600">Keluar</button>
                </form>
            </div>
        </nav>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>