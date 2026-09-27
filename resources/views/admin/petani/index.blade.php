@extends('layouts.admin')

@section('title', 'Kelola Akun Petani')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-[#1B4332]">Akun Petani</h1>
            <p class="text-sm text-[#5C6B62] mt-1">Daftar warga yang terdaftar sebagai pengguna layanan.</p>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="relative max-w-xs">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-[#5C6B62]"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau NIK" class="w-full pl-9 rounded-lg border-[#E1DCC9] text-sm">
        </div>
    </form>

    @if ($petanis->isEmpty())
        <div class="bg-white border border-[#E1DCC9] rounded-2xl px-6 py-14 text-center">
            <i class="bi bi-people text-3xl text-[#5C6B62]"></i>
            <p class="text-sm text-[#5C6B62] mt-3">Belum ada akun petani terdaftar.</p>
        </div>
    @else
        <div class="bg-white border border-[#E1DCC9] rounded-2xl divide-y divide-[#E1DCC9] overflow-hidden">
            @foreach ($petanis as $petani)
                <div class="flex items-center gap-4 px-5 py-4">
                    <span class="w-10 h-10 rounded-full bg-[#D8E7DE] text-[#1B4332] flex items-center justify-center font-semibold shrink-0">
                        {{ strtoupper(substr($petani->name, 0, 1)) }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-[#14231C]">{{ $petani->name }}</p>
                        <p class="text-sm text-[#5C6B62]">{{ $petani->email }} @if($petani->no_hp) · {{ $petani->no_hp }} @endif</p>
                    </div>
                    <div class="flex items-center gap-3 shrink-0 text-sm">
                        <a href="{{ route('admin.petani.edit', $petani) }}" class="text-[#1B4332] underline">Edit</a>

                        <form method="POST" action="{{ route('admin.petani.reset-password', $petani) }}" onsubmit="return confirm('Reset password {{ $petani->name }}?')">
                            @csrf
                            <button type="submit" class="text-[#1B4332] underline">Reset Password</button>
                        </form>

                        <form method="POST" action="{{ route('admin.petani.destroy', $petani) }}" onsubmit="return confirm('Hapus akun {{ $petani->name }}? Data pengajuan & kritik-saran terkait juga akan terhapus.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 underline">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $petanis->links() }}</div>
    @endif

@endsection