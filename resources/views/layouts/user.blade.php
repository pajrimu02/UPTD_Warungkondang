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

    <header class="bg-white border-b border-[#E1DCC9] sticky top-0 z-10">
        <div class="max-w-4xl mx-auto flex items-center justify-between px-4 py-4">
            <a href="{{ route('user.dashboard') }}" class="font-semibold text-[#1B4332]">UPTD Pertanian Warungkondang</a>

            <nav class="hidden md:flex items-center gap-6 text-sm">
                <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Dashboard</a>
                <a href="{{ route('user.surat-solar.index') }}" class="{{ request()->routeIs('user.surat-solar.*') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Surat Solar</a>
                <a href="{{ route('user.profil') }}" class="{{ request()->routeIs('user.profil') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Profil</a>
                <a href="{{ route('user.kritiksaran.create') }}" class="{{ request()->routeIs('user.kritiksaran.*') ? 'text-[#1B4332] font-medium' : 'text-[#5C6B62]' }}">Kritik & Saran</a>
            </nav>

            <div class="flex items-center gap-3">
                <span class="hidden sm:inline text-sm text-[#5C6B62]">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm bg-[#1B4332] text-white px-4 py-2 rounded-full">Keluar</button>
                </form>
            </div>
        </div>
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