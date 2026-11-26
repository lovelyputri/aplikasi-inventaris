<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
            <label for="kode_barang" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Kode Barang <span class="text-rose-500">*</span>
            </label>
            <input type="text"
                   id="kode_barang"
                   name="kode_barang"
                   value="{{ old('kode_barang', $barang->kode_barang ?? '') }}"
                   required
                   placeholder="Contoh: LAB-001"
                   class="w-full px-3.5 py-2.5 text-sm font-mono bg-white dark:bg-navy-900 border rounded-xl text-slate-900 dark:text-white placeholder:font-sans placeholder:text-slate-400 focus:outline-none focus:ring-2 transition {{ $errors->has('kode_barang') ? 'border-rose-400 focus:ring-rose-500/25 focus:border-rose-500' : 'border-slate-300 dark:border-slate-600 focus:ring-indigo-500/25 focus:border-indigo-500' }}">
            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Kode unik identifikasi aset lab.</p>
            @error('kode_barang')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div class="sm:col-span-2">
            <label for="nama_barang" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Nama Barang <span class="text-rose-500">*</span>
            </label>
            <input type="text"
                   id="nama_barang"
                   name="nama_barang"
                   value="{{ old('nama_barang', $barang->nama_barang ?? '') }}"
                   required
                   placeholder="Contoh: Monitor LG 24 Inch IPS"
                   class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 transition {{ $errors->has('nama_barang') ? 'border-rose-400 focus:ring-rose-500/25 focus:border-rose-500' : 'border-slate-300 dark:border-slate-600 focus:ring-indigo-500/25 focus:border-indigo-500' }}">
            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Nama lengkap beserta merek/tipe perangkat.</p>
            @error('nama_barang')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-2 border-t border-slate-100 dark:border-slate-700/70">
        <div>
            <label for="jumlah_stok" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Jumlah Stok <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
                <input type="number"
                       id="jumlah_stok"
                       name="jumlah_stok"
                       value="{{ old('jumlah_stok', $barang->jumlah_stok ?? 0) }}"
                       min="0"
                       step="1"
                       required
                       placeholder="0"
                       class="w-full pl-3.5 pr-14 py-2.5 text-sm bg-white dark:bg-navy-900 border rounded-xl text-slate-900 dark:text-white tabular-nums focus:outline-none focus:ring-2 transition {{ $errors->has('jumlah_stok') ? 'border-rose-400 focus:ring-rose-500/25 focus:border-rose-500' : 'border-slate-300 dark:border-slate-600 focus:ring-indigo-500/25 focus:border-indigo-500' }}">
                <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs font-medium text-slate-400 pointer-events-none">
                    unit
                </span>
            </div>
            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Masukkan jumlah unit tersedia (minimal 0).</p>
            @error('jumlah_stok')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="kondisi" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                Kondisi Barang <span class="text-rose-500">*</span>
            </label>
            <select id="kondisi"
                    name="kondisi"
                    required
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 transition {{ $errors->has('kondisi') ? 'border-rose-400 focus:ring-rose-500/25 focus:border-rose-500' : 'border-slate-300 dark:border-slate-600 focus:ring-indigo-500/25 focus:border-indigo-500' }}">
                <option value="" disabled @selected(old('kondisi', $barang->kondisi ?? '') === '')>
                    -- Pilih Kondisi Barang --
                </option>
                @foreach ($kondisiOptions as $option)
                    <option value="{{ $option }}" @selected(old('kondisi', $barang->kondisi ?? '') === $option)>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
            <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Pilih status kelayakan perangkat saat ini.</p>
            @error('kondisi')
                <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="pt-2 border-t border-slate-100 dark:border-slate-700/70">
        <label for="deskripsi" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
            Deskripsi / Spesifikasi Barang
        </label>
        <textarea id="deskripsi"
                  name="deskripsi"
                  rows="3"
                  placeholder="Contoh: Monitor Full HD untuk meja praktikum Lab RPL 1, lengkap dengan kabel HDMI & adaptor."
                  class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 transition {{ $errors->has('deskripsi') ? 'border-rose-400 focus:ring-rose-500/25 focus:border-rose-500' : 'border-slate-300 dark:border-slate-600 focus:ring-indigo-500/25 focus:border-indigo-500' }}">{{ old('deskripsi', $barang->deskripsi ?? '') }}</textarea>
        <p class="mt-1.5 text-xs text-slate-500 dark:text-slate-400">Opsional: catatan lokasi ruang lab, spesifikasi, atau keterangan tambahan.</p>
        @error('deskripsi')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400 font-semibold">{{ $message }}</p>
        @enderror
    </div>
</div>
