<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ketua') - UPTD Pertanian Warungkondang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#F7F5EF] text-[#26332B] antialiased">

    <div x-data="{ sidebarOpen: false }" class="min-h-screen">

        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-40 lg:hidden" style="display: none;"></div>

        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#1B4332] text-white transform transition-transform duration-300 lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <div class="h-20 flex items-center px-6 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center">
                        <span class="text-[#1B4332] font-bold text-lg">U</span>
                    </div>
                    <div>
                        <h1 class="font-semibold text-sm">UPTD Pertanian</h1>
                        <p class="text-xs text-white/60">Warungkondang</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-white/70 hover:text-white">✕</button>
            </div>

            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-5rem)]">
                <p class="px-3 pt-2 pb-2 text-[10px] uppercase tracking-wider text-white/40">Menu Utama</p>

                @foreach ([
                    ['ketua.dashboard', 'ketua.dashboard', 'bi-grid-1x2', 'Dashboard'],
                    ['ketua.surat.index', 'ketua.surat.*', 'bi-pen', 'Tanda Tangan'],
                    ['ketua.rekap', 'ketua.rekap', 'bi-bar-chart-line', 'Rekap Surat'],
                ] as [$rute, $pola, $ikon, $label])
                    <a href="{{ route($rute) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs($pola) ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <i class="bi {{ $ikon }} w-5 shrink-0 text-center"></i>
                        <span>{{ $label }}</span>
                    </a>
                @endforeach

                <div class="pt-5 pb-2">
                    <p class="px-3 text-[10px] uppercase tracking-wider text-white/40">Sistem</p>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="pt-1">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-white/70 hover:bg-red-500/20 hover:text-red-200 transition">
                        <i class="bi bi-box-arrow-right w-5 shrink-0 text-center"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </nav>
        </aside>

        <div class="lg:ml-64 min-h-screen">
            <header class="h-20 bg-white border-b border-[#E1DCC9] sticky top-0 z-30">
                <div class="h-full px-4 sm:px-6 flex items-center justify-between">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-[#1B4332] hover:bg-[#F7F5EF]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    </button>

                    <div class="hidden sm:block">
                        <p class="text-xs text-[#5C6B62]">Panel Ketua</p>
                        <h2 class="font-semibold text-[#1B4332]">UPTD Pertanian Warungkondang</h2>
                    </div>

                    <div class="flex items-center gap-3 ml-auto">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-[#26332B]">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-[#5C6B62]">Ketua</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-[#D8E7DE] flex items-center justify-center text-[#1B4332] font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
                @endif

                @yield('content')
            </main>

            <footer class="px-4 sm:px-6 lg:px-8 pb-6">
                <div class="border-t border-[#E1DCC9] pt-4 text-xs text-[#5C6B62]">© {{ date('Y') }} UPTD Pertanian Warungkondang</div>
            </footer>
        </div>
    </div>

</body>
</html>