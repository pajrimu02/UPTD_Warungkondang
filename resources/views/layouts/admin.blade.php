 
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Admin') - UPTD Pertanian Warungkondang
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-[#F7F5EF] text-[#26332B] antialiased">

    <div
        x-data="{ sidebarOpen: false }"
        class="min-h-screen"
    >

        {{-- =========================
             MOBILE OVERLAY
        ========================== --}}
        <div
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/40 z-40 lg:hidden"
            style="display: none;"
        ></div>


        {{-- =========================
             SIDEBAR
        ========================== --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-[#1B4332] text-white
                   transform transition-transform duration-300
                   lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >

            {{-- Logo / Brand --}}
            <div class="h-20 flex items-center px-6 border-b border-white/10">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl bg-white flex items-center justify-center"
                    >
                        <span class="text-[#1B4332] font-bold text-lg">
                            U
                        </span>
                    </div>

                    <div>
                        <h1 class="font-semibold text-sm">
                            UPTD Pertanian
                        </h1>

                        <p class="text-xs text-white/60">
                            Warungkondang
                        </p>
                    </div>

                </div>

                {{-- Close mobile sidebar --}}
                <button
                    @click="sidebarOpen = false"
                    class="ml-auto lg:hidden text-white/70 hover:text-white"
                >
                    ✕
                </button>

            </div>


            {{-- =========================
                 NAVIGATION
            ========================== --}}
            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-5rem)]">

                <p class="px-3 pt-2 pb-2 text-[10px] uppercase tracking-wider text-white/40">
                    Menu Utama
                </p>


                {{-- Dashboard --}}
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.dashboard')
                        ? 'bg-white/15 text-white'
                        : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                >

                    <svg
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h4v-6h4v6h4a1 1 0 001-1V10"
                        />
                    </svg>

                    <span>Dashboard</span>

                </a>


                {{-- Pengajuan Solar --}}
                <a
                    href="{{ route('admin.surat-solar.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.surat-solar.*')
                        ? 'bg-white/15 text-white'
                        : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                >

                    <svg
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M9 3h6M9 3v3m6-3v3M7 6h10l1 15H6L7 6zm2 5h6m-6 4h6"
                        />
                    </svg>

                    <span>Pengajuan Solar</span>

                </a>


                {{-- Kritik & Saran --}}
                <a
                    href="{{ route('admin.kritiksaran.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition
                    {{ request()->routeIs('admin.kritiksaran.*')
                        ? 'bg-white/15 text-white'
                        : 'text-white/70 hover:bg-white/10 hover:text-white' }}"
                >

                    <svg
                        class="w-5 h-5 shrink-0"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="1.8"
                            d="M8 10h8M8 14h5M5 4h14a2 2 0 012 2v10a2 2 0 01-2 2h-6l-4 3v-3H5a2 2 0 01-2-2V6a2 2 0 012-2z"
                        />
                    </svg>

                    <span>Kritik & Saran</span>

                </a>


               
  
                <div class="pt-5 pb-2">

                    <p class="px-3 text-[10px] uppercase tracking-wider text-white/40">
                        Sistem
                    </p>

                </div>

 


                {{-- Logout --}}
                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    class="pt-1"
                >
                    @csrf

                    <button
                        type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm
                               text-white/70 hover:bg-red-500/20 hover:text-red-200 transition"
                    >

                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.8"
                                d="M15 12H3m0 0l4-4m-4 4l4 4M13 5V4a1 1 0 011-1h6a1 1 0 011 1v16a1 1 0 01-1 1h-6a1 1 0 01-1-1v-1"
                            />
                        </svg>

                        <span>Keluar</span>

                    </button>

                </form>

            </nav>

        </aside>


        {{-- =========================
             MAIN AREA
        ========================== --}}
        <div class="lg:ml-64 min-h-screen">


            {{-- =========================
                 TOP NAVBAR
            ========================== --}}
            <header
                class="h-20 bg-white border-b border-[#E1DCC9] sticky top-0 z-30"
            >

                <div class="h-full px-4 sm:px-6 flex items-center justify-between">

                    {{-- Mobile menu --}}
                    <button
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-lg text-[#1B4332]
                               hover:bg-[#F7F5EF]"
                    >

                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>

                    </button>


                    {{-- Page title --}}
                    <div class="hidden sm:block">

                        <p class="text-xs text-[#5C6B62]">
                            Panel Administrasi
                        </p>

                        <h2 class="font-semibold text-[#1B4332]">
                            UPTD Pertanian Warungkondang
                        </h2>

                    </div>


                    {{-- User --}}
                    <div class="flex items-center gap-3 ml-auto">

                        <div class="text-right hidden sm:block">

                            <p class="text-sm font-medium text-[#26332B]">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="text-xs text-[#5C6B62]">
                                Administrator
                            </p>

                        </div>


                        <div
                            class="w-10 h-10 rounded-full bg-[#D8E7DE]
                                   flex items-center justify-center
                                   text-[#1B4332] font-semibold"
                        >
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>

                    </div>

                </div>

            </header>


            {{-- =========================
                 PAGE CONTENT
            ========================== --}}
            <main class="p-4 sm:p-6 lg:p-8">

                @if (session('success'))
                    <div
                        class="mb-6 rounded-lg border border-green-200 bg-green-50
                               px-4 py-3 text-sm text-green-800"
                    >
                        {{ session('success') }}
                    </div>
                @endif


                @if (session('error'))
                    <div
                        class="mb-6 rounded-lg border border-red-200 bg-red-50
                               px-4 py-3 text-sm text-red-800"
                    >
                        {{ session('error') }}
                    </div>
                @endif


                @yield('content')

            </main>


            {{-- =========================
                 FOOTER
            ========================== --}}
            <footer class="px-4 sm:px-6 lg:px-8 pb-6">

                <div
                    class="border-t border-[#E1DCC9] pt-4
                           flex flex-col sm:flex-row
                           items-center justify-between gap-2"
                >

                    <p class="text-xs text-[#5C6B62]">
                        © {{ date('Y') }} UPTD Pertanian Warungkondang
                    </p>

                    <p class="text-xs text-[#5C6B62]">
                        Sistem Informasi Pelayanan UPTD
                    </p>

                </div>

            </footer>

        </div>

    </div>

</body>

</html>
 
