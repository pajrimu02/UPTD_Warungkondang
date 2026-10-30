<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Dashboard') - UPTD Pertanian Warungkondang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; } }
    </style>
</head>
<body class="bg-[#F5F3EC] text-[#14231C] antialiased">

@php
    $user = auth()->user();

    $namaPoktan = null;
    if (method_exists($user, 'poktan') && $user->poktan) {
        $namaPoktan = $user->poktan->nama_kelompok ?? null;
    }

    $inisial = collect(explode(' ', trim($user->name)))->filter()->take(2)
        ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');

    // [label sidebar, label singkat (HP), ikon, ikon aktif, url, pola route aktif]
    $menu = [
        ['Dashboard', 'Dashboard', 'bi-grid-1x2', 'bi-grid-1x2-fill', route('user.dashboard'), 'user.dashboard'],
        ['Poktan', 'Poktan', 'bi-people', 'bi-people-fill', Route::has('user.poktan') ? route('user.poktan') : '#', 'user.poktan*'],
        ['Pengajuan Surat', 'Surat', 'bi-file-earmark-text', 'bi-file-earmark-text-fill', route('user.surat-pengajuan.index'), 'user.surat-*'],
        ['Kritik & Saran', 'Saran', 'bi-chat-square-text', 'bi-chat-square-text-fill', route('user.kritiksaran.create'), 'user.kritiksaran.*'],
        ['Profil', 'Profil', 'bi-person-circle', 'bi-person-circle', route('user.profil'), 'user.profil*'],
    ];
@endphp

{{-- ================= TOPBAR MOBILE ================= --}}
<header class="lg:hidden sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-[#E1DCC9]">
    <div class="flex items-center justify-between px-4 h-16">
        <a href="{{ route('user.dashboard') }}" class="flex items-center" aria-label="Dashboard">
            <img src="{{ asset('storage/logo/logo_uptd.png') }}" alt="Logo UPTD Pertanian Warungkondang" class="h-9 w-auto object-contain">
        </a>

        <div class="relative">
            <button type="button" id="akun-btn" aria-label="Menu akun" aria-expanded="false" aria-controls="akun-menu"
                    class="w-10 h-10 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-sm font-semibold ring-2 ring-[#C9A227]">
                {{ $inisial }}
            </button>

            <div id="akun-menu" class="hidden absolute right-0 top-12 w-64 bg-white border border-[#E1DCC9] rounded-2xl shadow-xl shadow-black/10 overflow-hidden">
                <div class="px-4 py-3.5 bg-[#EAF2EC]">
                    <p class="text-sm font-semibold text-[#14231C] truncate">{{ $user->name }}</p>
                    <p class="text-xs text-[#5C6B62] truncate">{{ $namaPoktan ?? 'Petani' }}</p>
                </div>
                <div class="p-2">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-[#5C6B62] hover:bg-[#F5F3EC] hover:text-[#1B4332] transition">
                        <i class="bi bi-box-arrow-up-right text-base"></i> Lihat Website
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition">
                            <i class="bi bi-box-arrow-right text-base"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- ================= SIDEBAR (DESKTOP) ================= --}}
<aside class="hidden lg:flex fixed inset-y-0 left-0 z-40 w-72 bg-white border-r border-[#E1DCC9] flex-col">

    <div class="h-20 shrink-0 flex items-center px-6 border-b border-[#E1DCC9]">
        <a href="{{ route('user.dashboard') }}" class="flex items-center min-w-0" aria-label="Dashboard">
            <img src="{{ asset('storage/logo/logo_uptd.png') }}" alt="Logo UPTD Pertanian Warungkondang" class="h-11 w-auto max-w-[190px] object-contain">
        </a>
    </div>

    <div class="px-5 pt-5">
        <div class="flex items-center gap-3 bg-[#EAF2EC] rounded-2xl p-3.5">
            <span class="w-11 h-11 shrink-0 rounded-full bg-[#1B4332] text-white flex items-center justify-center text-sm font-semibold ring-2 ring-[#C9A227]">
                {{ $inisial }}
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-[#14231C] truncate">{{ $user->name }}</p>
                <p class="text-xs text-[#5C6B62] truncate">{{ $namaPoktan ?? 'Petani' }}</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-5 py-5" aria-label="Menu utama">
        <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-widest text-[#8C9086]">Menu</p>
        <ul class="space-y-1">
            @foreach ($menu as [$label, $singkat, $icon, $iconAktif, $url, $pola])
                @php $aktif = request()->routeIs($pola); @endphp
                <li>
                    <a href="{{ $url }}" @if ($aktif) aria-current="page" @endif
                       class="group flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium transition
                              {{ $aktif ? 'bg-[#1B4332] text-white shadow-md shadow-[#1B4332]/20' : 'text-[#5C6B62] hover:bg-[#F5F3EC] hover:text-[#1B4332]' }}">
                        <i class="bi {{ $icon }} text-lg {{ $aktif ? 'text-[#E9C85A]' : 'text-[#8C9086] group-hover:text-[#2D6A4F]' }}"></i>
                        <span>{{ $label }}</span>
                        @if ($aktif)
                            <span class="ml-auto w-1.5 h-1.5 rounded-full bg-[#E9C85A]"></span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>

        <p class="px-3 mt-7 mb-2 text-[11px] font-semibold uppercase tracking-widest text-[#8C9086]">Lainnya</p>
        <ul class="space-y-1">
            <li>
                <a href="{{ route('home') }}"
                   class="group flex items-center gap-3 px-3.5 py-3 rounded-xl text-sm font-medium text-[#5C6B62] hover:bg-[#F5F3EC] hover:text-[#1B4332] transition">
                    <i class="bi bi-box-arrow-up-right text-lg text-[#8C9086] group-hover:text-[#2D6A4F]"></i>
                    <span>Lihat Website</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="shrink-0 p-5 border-t border-[#E1DCC9]">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </button>
        </form>
    </div>
</aside>

{{-- ================= KONTEN ================= --}}
<div class="lg:pl-72 min-h-screen flex flex-col">

    {{-- Topbar desktop --}}
    <header class="hidden lg:flex items-center justify-between h-20 px-10 border-b border-[#E1DCC9] bg-[#F5F3EC]/90 backdrop-blur sticky top-0 z-20">
        <div>
            <p class="text-xs text-[#8C9086]">Selamat datang,</p>
            <p class="text-sm font-semibold text-[#14231C]">{{ $user->name }}</p>
        </div>
        <span class="inline-flex items-center gap-2 text-xs text-[#5C6B62] bg-white border border-[#E1DCC9] rounded-full px-3.5 py-1.5">
            <i class="bi bi-calendar3 text-[#2D6A4F]"></i> {{ now()->translatedFormat('l, d F Y') }}
        </span>
    </header>

    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-10 py-8">
        @foreach (['status', 'success'] as $kunci)
            @if (session($kunci))
                <div class="mb-6 flex items-start gap-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 text-sm">
                    <i class="bi bi-check-circle-fill mt-0.5"></i>
                    <span>{{ session($kunci) }}</span>
                </div>
            @endif
        @endforeach

        @if (session('error'))
            <div class="mb-6 flex items-start gap-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 px-4 py-3.5 text-sm">
                <i class="bi bi-exclamation-triangle-fill mt-0.5"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    {{-- pb-28 di HP supaya tidak tertutup tab bar bawah --}}
    <footer class="px-4 sm:px-6 lg:px-10 pt-6 pb-28 lg:pb-6 border-t border-[#E1DCC9] text-center text-xs text-[#8C9086]">
        &copy; {{ date('Y') }} UPTD Pelayanan Pertanian Warungkondang, Kabupaten Cianjur
    </footer>
</div>

{{-- ================= TAB BAR BAWAH (HP) ================= --}}
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur border-t border-[#E1DCC9] pb-[env(safe-area-inset-bottom)]" aria-label="Menu utama">
    <ul class="grid grid-cols-5 max-w-lg mx-auto">
        @foreach ($menu as [$label, $singkat, $icon, $iconAktif, $url, $pola])
            @php $aktif = request()->routeIs($pola); @endphp
            <li>
                <a href="{{ $url }}" @if ($aktif) aria-current="page" @endif
                   class="flex flex-col items-center gap-1 pt-2.5 pb-2 text-[11px] font-medium transition
                          {{ $aktif ? 'text-[#1B4332]' : 'text-[#8C9086] hover:text-[#1B4332]' }}">
                    <span class="w-14 h-8 rounded-full flex items-center justify-center transition
                                 {{ $aktif ? 'bg-[#1B4332] text-[#E9C85A]' : '' }}">
                        <i class="bi {{ $aktif ? $iconAktif : $icon }} text-xl"></i>
                    </span>
                    <span class="{{ $aktif ? 'font-semibold' : '' }}">{{ $singkat }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>

<script>
    (function () {
        const btn = document.getElementById('akun-btn');
        const menu = document.getElementById('akun-menu');
        if (!btn) return;

        function tutup() { menu.classList.add('hidden'); btn.setAttribute('aria-expanded', 'false'); }
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            const buka = menu.classList.toggle('hidden') === false;
            btn.setAttribute('aria-expanded', buka ? 'true' : 'false');
        });
        document.addEventListener('click', (e) => { if (!menu.contains(e.target)) tutup(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') tutup(); });
    })();
</script>

</body>
</html>