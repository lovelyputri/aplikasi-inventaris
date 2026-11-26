@extends('layouts.app')

@section('title', 'Edit Barang - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Edit Data Barang')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 mb-3">
                <a href="{{ route('barangs.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Data Barang</a>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('barangs.show', $barang) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition font-mono">{{ $barang->kode_barang }}</a>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 dark:text-slate-200 font-semibold">Edit</span>
            </nav>

            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Edit Data Barang</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Perbarui informasi stok, kondisi, atau detail barang <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $barang->nama_barang }}</span>.
            </p>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-navy-900/40 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Perbarui Data Inventaris</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Terakhir diubah {{ $barang->formatWaktuRelatif($barang->updated_at) ?? '-' }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-100 dark:bg-navy-900 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                    {{ $barang->kode_barang }}
                </span>
            </div>

            <form method="POST" action="{{ route('barangs.update', $barang) }}">
                @csrf
                @method('PUT')

                <div class="p-6">
                    @include('barangs._form')
                </div>

                <div class="px-6 py-4 bg-slate-50/70 dark:bg-navy-900/50 border-t border-slate-200 dark:border-slate-700 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('barangs.show', $barang) }}"
                       class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition text-center sm:text-left">
                        Lihat Halaman Detail &rarr;
                    </a>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2.5">
                        <a href="{{ route('barangs.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            Batal
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
