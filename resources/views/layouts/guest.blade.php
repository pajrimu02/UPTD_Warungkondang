<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'UPTD Pertanian Warungkondang')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Fraunces', serif; }
    </style>
</head>
<body class="bg-[#F5F3EC]">

    <div class="min-h-screen grid lg:grid-cols-2">

        {{-- Kiri: foto UPTD --}}
        <div class="hidden lg:block relative">
            <img src="{{ asset('storage/home/uptd.png') }}" alt="UPTD Pertanian Warungkondang" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-[#14231C]/80 via-[#1B4332]/30 to-transparent"></div>

            <div class="relative h-full flex flex-col justify-start p-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-white/90 text-sm font-medium hover:text-white w-fit">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>

                <div class="text-white max-w-md mt-10">
                    <h1 class="font-display text-3xl leading-tight">
                        Selamat datang di UPTD Pertanian Warungkondang
                    </h1>
                    <p class="mt-3 text-white/80 text-sm">
                         Kelola kebutuhan layanan pertanian Anda secara digital bersama UPTD Pertanian Warungkondang.
                    </p>
                </div>
            </div>
        </div>

        {{-- Kanan: form --}}
        <div class="flex flex-col justify-center px-6 sm:px-10 py-10 relative">

            {{-- Tombol kembali — muncul di mobile aja, karena versi desktop udah ada di panel foto --}}
            <a href="{{ route('home') }}" class="lg:hidden inline-flex items-center gap-2 text-[#1B4332] text-sm font-medium mb-8 w-fit">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>

            <div class="w-full max-w-sm mx-auto">
                <div class="flex items-center gap-2 mb-8">
                    <span class="w-9 h-9 rounded-full bg-[#1B4332] flex items-center justify-center">
                        <i class="bi bi-flower1 text-[#C9A227]"></i>
                    </span>
                    <span class="font-display font-semibold text-[#1B4332]">UPTD Pertanian Warungkondang</span>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>

</body>
</html>