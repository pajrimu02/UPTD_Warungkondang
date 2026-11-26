@props([
    'href',
    'title' => 'Kembali',
])

<div class="mb-4">
    <a href="{{ $href }}"
       title="{{ $title }}"
       aria-label="{{ $title }}"
       class="group inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-[#E1DCC9] bg-white text-[#1B4332] shadow-2xs hover:border-[#1B4332] hover:bg-[#1B4332] hover:text-white active:scale-95 transition-all duration-150">
        <i class="bi bi-arrow-left text-lg transition-transform duration-150 group-hover:-translate-x-0.5"></i>
    </a>
</div>