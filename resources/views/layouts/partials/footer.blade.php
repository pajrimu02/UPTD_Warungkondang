<footer class="bg-[#14231C] text-[#CFE3D6]">
    <div class="max-w-6xl mx-auto px-6 py-14 grid md:grid-cols-4 gap-10">

        {{-- Brand + sosmed --}}
        <div>
            <p class="text-white font-semibold text-lg">UPTD Pertanian</p>
            <p class="text-sm text-[#9FB3A6]">BPP Kec. Warungkondang</p>
            <p class="mt-4 text-sm leading-relaxed max-w-xs">
                Melayani petani dan kelompok tani Kecamatan Warungkondang, Kabupaten Cianjur, dengan layanan yang cepat dan transparan.
            </p>
            <div class="flex gap-3 mt-5">
                <a href="#" aria-label="Facebook" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center transition">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" aria-label="Instagram" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center transition">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" aria-label="YouTube" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center transition">
                    <i class="bi bi-youtube"></i>
                </a>
                <a href="https://wa.me/62xxxxxxxxxx" aria-label="WhatsApp" class="w-9 h-9 rounded-full bg-white/10 hover:bg-[#C9A227] hover:text-[#14231C] flex items-center justify-center transition">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>

        {{-- Navigasi --}}
        <div>
            <p class="text-white font-semibold mb-4">Navigasi</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('home') }}" class="hover:text-white">Beranda</a></li>
                <li><a href="{{ route('tentang') }}" class="hover:text-white">Tentang Kami</a></li>
                <li><a href="{{ route('faq') }}" class="hover:text-white">FAQ</a></li>
                <li><a href="{{ route('hubungi') }}" class="hover:text-white">Hubungi Kami</a></li>
            </ul>
        </div>

        {{-- Informasi Publik --}}
        <div>
            <p class="text-white font-semibold mb-4">Informasi Publik</p>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('infopublik.pelayanan') }}" class="hover:text-white">Pelayanan</a></li>
                <li><a href="{{ route('infopublik.stokkuota') }}" class="hover:text-white">Stok & Kuota</a></li>
                <li><a href="{{ route('infopublik.informasi') }}" class="hover:text-white">Informasi</a></li>
                <li><a href="{{ route('infopublik.edukasi') }}" class="hover:text-white">Edukasi</a></li>
                <li><a href="{{ route('galeri.foto') }}" class="hover:text-white">Galeri Foto</a></li>
                <li><a href="{{ route('galeri.video') }}" class="hover:text-white">Galeri Video</a></li>
            </ul>
        </div>

        {{-- Kontak --}}
        <div>
            <p class="text-white font-semibold mb-4">Kontak</p>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start gap-3">
                    <i class="bi bi-geo-alt-fill mt-0.5 text-[#C9A227]"></i>
                    <span>Kecamatan Warungkondang,<br>Kabupaten Cianjur, Jawa Barat</span>
                </li>
                <li class="flex items-center gap-3">
                    <i class="bi bi-telephone-fill text-[#C9A227]"></i>
                    <span>(0263) xxx-xxxx</span>
                </li>
                <li class="flex items-center gap-3">
                    <i class="bi bi-envelope-fill text-[#C9A227]"></i>
                    <span>bpp.warungkondang@cianjurkab.go.id</span>
                </li>
                <li class="flex items-center gap-3">
                    <i class="bi bi-clock-fill text-[#C9A227]"></i>
                    <span>Senin–Jumat, 08.00–15.00 WIB</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-6xl mx-auto px-6 py-5 flex flex-col md:flex-row items-center justify-between gap-2 text-xs text-[#9FB3A6]">
            <p>&copy; {{ date('Y') }} UPTD Pertanian / BPP Kecamatan Warungkondang. Hak cipta dilindungi.</p>
            <p>Dibangun untuk pelayanan publik yang lebih baik.</p>
        </div>
    </div>
</footer>