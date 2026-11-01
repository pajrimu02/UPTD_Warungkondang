<style>
    #konfirmasi-latar { opacity: 0; transition: opacity .2s ease; }
    #konfirmasi-kotak { opacity: 0; transform: translateY(24px) scale(.97); transition: opacity .2s ease, transform .2s ease; }
    #konfirmasi.terbuka #konfirmasi-latar { opacity: 1; }
    #konfirmasi.terbuka #konfirmasi-kotak { opacity: 1; transform: none; }
    @media (prefers-reduced-motion: reduce) {
        #konfirmasi-latar, #konfirmasi-kotak { transition: none; }
    }
</style>

<div id="konfirmasi" class="hidden fixed inset-0 z-[90] items-end sm:items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="konfirmasi-judul" aria-describedby="konfirmasi-isi">

    <div id="konfirmasi-latar" class="absolute inset-0 bg-[#0B1511]/60 backdrop-blur-sm"></div>

    <div id="konfirmasi-kotak" class="relative w-full max-w-sm bg-white rounded-3xl p-6 shadow-2xl">
        <span id="konfirmasi-ikon" class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl">
            <i class="bi"></i>
        </span>
        <h3 id="konfirmasi-judul" class="font-semibold text-lg text-[#14231C] mt-4"></h3>
        <p id="konfirmasi-isi" class="text-sm text-[#5C6B62] mt-1.5 leading-relaxed"></p>

        <div class="flex gap-3 mt-6">
            <button type="button" id="konfirmasi-batal"
                    class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold text-[#5C6B62] bg-[#F5F3EC] hover:bg-[#EAE6D6] transition">
                Batal
            </button>
            <button type="button" id="konfirmasi-ya"
                    class="flex-1 px-4 py-3 rounded-xl text-sm font-semibold text-white transition disabled:opacity-60">
                Ya
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        var modal = document.getElementById('konfirmasi');
        if (!modal) return;

        var latar = document.getElementById('konfirmasi-latar');
        var ikon = document.getElementById('konfirmasi-ikon');
        var judul = document.getElementById('konfirmasi-judul');
        var isi = document.getElementById('konfirmasi-isi');
        var batal = document.getElementById('konfirmasi-batal');
        var ya = document.getElementById('konfirmasi-ya');

        var formTertunda = null, pemicu = null, timer = null;

        var gaya = {
            normal: { ikon: 'bg-[#EAF2EC] text-[#1B4332]', ya: 'bg-[#1B4332] hover:bg-[#153629]', ico: 'bi-send' },
            bahaya: { ikon: 'bg-red-50 text-red-600', ya: 'bg-red-600 hover:bg-red-700', ico: 'bi-trash' }
        };

        function buka(form) {
            var g = gaya[form.dataset.tipe === 'bahaya' ? 'bahaya' : 'normal'];
            formTertunda = form;
            pemicu = document.activeElement;

            judul.textContent = form.dataset.judul || 'Konfirmasi';
            isi.textContent = form.dataset.konfirmasi || 'Lanjutkan tindakan ini?';
            ya.textContent = form.dataset.tombol || 'Ya, Lanjutkan';
            ya.disabled = false;
            ya.className = 'flex-1 px-4 py-3 rounded-xl text-sm font-semibold text-white transition disabled:opacity-60 ' + g.ya;
            ikon.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-xl ' + g.ikon;
            ikon.firstElementChild.className = 'bi ' + (form.dataset.ikon || g.ico);

            clearTimeout(timer);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    modal.classList.add('terbuka');
                    batal.focus();
                });
            });
        }

        function tutup() {
            modal.classList.remove('terbuka');
            document.body.style.overflow = '';
            timer = setTimeout(function () {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 200);
            if (pemicu && pemicu.focus) pemicu.focus();
            formTertunda = null;
        }

        // Semua form ber-atribut data-konfirmasi ditahan dulu
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-konfirmasi')) return;
            e.preventDefault();
            buka(form);
        });

        ya.addEventListener('click', function () {
            if (!formTertunda) return;
            ya.disabled = true;
            ya.textContent = 'Memproses...';
            formTertunda.submit();
        });

        batal.addEventListener('click', tutup);
        latar.addEventListener('click', tutup);

        document.addEventListener('keydown', function (e) {
            if (!modal.classList.contains('terbuka')) return;
            if (e.key === 'Escape') tutup();
            if (e.key === 'Tab') {
                var el = [batal, ya];
                var i = el.indexOf(document.activeElement);
                e.preventDefault();
                el[(i + (e.shiftKey ? -1 : 1) + 2) % 2].focus();
            }
        });

        // Tombol "kembali" browser: pastikan modal tidak tersangkut terbuka
        window.addEventListener('pageshow', function (e) {
            if (!e.persisted) return;
            modal.classList.remove('terbuka', 'flex');
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    })();
</script>