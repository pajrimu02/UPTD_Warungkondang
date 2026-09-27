<div>
    <label class="block text-sm mb-1">Jenis</label>
    <select name="jenis" class="w-full rounded-lg border-[#E1DCC9]">
        <option value="solar" {{ old('jenis', $stokKuota->jenis ?? '') === 'solar' ? 'selected' : '' }}>Solar</option>
        <option value="pupuk" {{ old('jenis', $stokKuota->jenis ?? '') === 'pupuk' ? 'selected' : '' }}>Pupuk</option>
    </select>
    @error('jenis') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm mb-1">Judul</label>
    <input type="text" name="judul" value="{{ old('judul', $stokKuota->judul ?? '') }}" class="w-full rounded-lg border-[#E1DCC9]">
    @error('judul') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm mb-1">Isi</label>
    <textarea name="isi" rows="5" class="w-full rounded-lg border-[#E1DCC9]">{{ old('isi', $stokKuota->isi ?? '') }}</textarea>
    @error('isi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm mb-1">Periode <span class="text-[#5C6B62]">(opsional, misal "Oktober 2026")</span></label>
    <input type="text" name="periode" value="{{ old('periode', $stokKuota->periode ?? '') }}" class="w-full rounded-lg border-[#E1DCC9]">
    @error('periode') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
</div>