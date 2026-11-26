@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Pengaturan')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-[1.7rem] font-extrabold text-slate-900 dark:text-white tracking-tight">
                Pengaturan Identitas &amp; Aplikasi
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola profil pengelola laboratorium, identitas sekolah, logo resmi, serta batas notifikasi stok minimum.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Profil Pengelola Lab --}}
            <div class="lg:col-span-5 bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden self-start">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-navy-900/40 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Profil Pengelola Lab</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Nama yang tercatat pada riwayat aktivitas.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">
                        Operator Aktif
                    </span>
                </div>

                <form method="POST" action="{{ route('pengaturan.profile.update') }}" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                            Nama Pengelola
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               placeholder="Contoh: Kepala Lab Komputer"
                               class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                        @error('name')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                            Email Kontak
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               placeholder="lab@smkmuh6.sch.id"
                               class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                        @error('email')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        Simpan Profil Pengelola
                    </button>
                </form>
            </div>

            {{-- Pengaturan Identitas Sekolah & Aplikasi --}}
            <div class="lg:col-span-7 bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-navy-900/40 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Identitas Sekolah &amp; Aplikasi</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Pengaturan logo sekolah, nama sistem, dan ambang batas stok.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 dark:bg-navy-900 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                        v{{ $settings->versi_aplikasi }}
                    </span>
                </div>

                <form method="POST" action="{{ route('pengaturan.app.update') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col sm:flex-row sm:items-center gap-4 p-4 rounded-xl bg-slate-50 dark:bg-navy-900/60 border border-slate-200/80 dark:border-slate-700">
                        <div class="w-16 h-16 rounded-xl bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 p-1.5 flex items-center justify-center shrink-0">
                            <img src="{{ asset($settings->resolvedLogoPath()) }}"
                                 alt="Logo Sekolah Saat Ini"
                                 class="w-full h-full object-contain">
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="logo" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1">
                                Ganti Logo Sekolah (Opsional)
                            </label>
                            <input type="file" id="logo" name="logo" accept=".png,.jpg,.jpeg,.webp"
                                   class="block w-full text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 dark:file:bg-indigo-500/20 dark:file:text-indigo-300 hover:file:bg-indigo-100 cursor-pointer">
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Format PNG/JPG/WEBP maks. 2 MB. Logo saat ini: {{ $settings->resolvedLogoPath() }}</p>
                            @error('logo')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_aplikasi" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                                Nama Aplikasi
                            </label>
                            <input type="text" id="nama_aplikasi" name="nama_aplikasi"
                                   value="{{ old('nama_aplikasi', $settings->nama_aplikasi) }}" required
                                   class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                            @error('nama_aplikasi')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="versi_aplikasi" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                                Versi Aplikasi
                            </label>
                            <input type="text" id="versi_aplikasi" name="versi_aplikasi"
                                   value="{{ old('versi_aplikasi', $settings->versi_aplikasi) }}" required
                                   class="w-full px-3.5 py-2.5 text-sm font-mono bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                            @error('versi_aplikasi')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="tagline" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                            Subjudul / Tagline
                        </label>
                        <input type="text" id="tagline" name="tagline"
                               value="{{ old('tagline', $settings->tagline) }}" required
                               class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                        @error('tagline')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_sekolah" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                            Nama Sekolah
                        </label>
                        <input type="text" id="nama_sekolah" name="nama_sekolah"
                               value="{{ old('nama_sekolah', $settings->nama_sekolah) }}" required
                               class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                        @error('nama_sekolah')
                            <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-2">
                            <label for="alamat_sekolah" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                                Alamat / Lokasi Lab
                            </label>
                            <input type="text" id="alamat_sekolah" name="alamat_sekolah"
                                   value="{{ old('alamat_sekolah', $settings->alamat_sekolah) }}"
                                   class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                            @error('alamat_sekolah')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="batas_stok_minimum" class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                                Batas Stok Min.
                            </label>
                            <input type="number" id="batas_stok_minimum" name="batas_stok_minimum" min="0"
                                   value="{{ old('batas_stok_minimum', $settings->batas_stok_minimum) }}" required
                                   class="w-full px-3.5 py-2.5 text-sm tabular-nums bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/25 focus:border-indigo-500">
                            @error('batas_stok_minimum')
                                <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                            Simpan Pengaturan Aplikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
