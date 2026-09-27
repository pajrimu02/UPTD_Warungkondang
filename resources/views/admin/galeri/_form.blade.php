<div>
    <label class="block text-sm mb-1">Judul</label>
    <input type="text" name="judul" value="{{ old('judul', $galeri->judul ?? '') }}" class="w-full rounded-lg border-[#E1DCC9]">
    @error('judul') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm mb-2">Jenis</label>
    <div class="grid grid-cols-2 gap-3">
        <label class="flex items-center gap-2 border border-[#E1DCC9] rounded-lg px-3 py-2.5 cursor-pointer">
            <input type="radio" name="jenis" value="foto" onchange="document.getElementById('field-foto').classList.remove('hidden'); document.getElementById('field-video').classList.add('hidden')" {{ old('jenis', $galeri->jenis ?? 'foto') === 'foto' ? 'checked' : '' }}>
            <span class="text-sm">Foto</span>
        </label>
        <label class="flex items-center gap-2 border border-[#E1DCC9] rounded-lg px-3 py-2.5 cursor-pointer">
            <input type="radio" name="jenis" value="video" onchange="document.getElementById('field-video').classList.remove('hidden'); document.getElementById('field-foto').classList.add('hidden')" {{ old('jenis', $galeri->jenis ?? '') === 'video' ? 'checked' : '' }}>
            <span class="text-sm">Video</span>
        </label>
    </div>
</div>

<div id="field-foto" class="{{ old('jenis', $galeri->jenis ?? 'foto') === 'video' ? 'hidden' : '' }}">
    <label class="block text-sm mb-1">File Foto @if(!isset($galeri)) * @endif</label>
    @if (isset($galeri) && $galeri->file_path)
        <img src="{{ asset('storage/'.$galeri->file_path) }}" class="w-32 h-20 object-cover rounded-lg mb-2">
    @endif
    <input type="file" name="file_path" accept="image/*" class="w-full text-sm">
    @error('file_path') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div id="field-video" class="{{ old('jenis', $galeri->jenis ?? 'foto') === 'foto' ? 'hidden' : '' }}">
    <label class="block text-sm mb-1">URL Video (YouTube, dsb) *</label>
    <input type="url" name="video_url" value="{{ old('video_url', $galeri->video_url ?? '') }}" placeholder="https://youtube.com/watch?v=..." class="w-full rounded-lg border-[#E1DCC9]">
    @error('video_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm mb-1">Tanggal <span class="text-[#5C6B62]">(opsional)</span></label>
    <input type="date" name="tanggal" value="{{ old('tanggal', isset($galeri->tanggal) ? $galeri->tanggal->format('Y-m-d') : '') }}" class="w-full rounded-lg border-[#E1DCC9]">
</div>