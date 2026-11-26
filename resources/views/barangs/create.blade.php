@extends('layouts.app')

@section('title', 'Tambah Barang Baru - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Tambah Barang Baru')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 mb-3">
                <a href="{{ route('barangs.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Data Barang</a>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 dark:text-slate-200 font-semibold">Tambah Barang</span>
            </nav>

            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Tambah Barang Baru</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Lengkapi informasi di bawah ini untuk mencatat barang baru ke dalam inventaris laboratorium.
            </p>
        </div>

        <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-navy-900/40 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Formulir Data Barang</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kolom bertanda <span class="text-rose-500 font-semibold">*</span> wajib diisi.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('barangs.store') }}">
                @csrf

                <div class="p-6">
                    @include('barangs._form')
                </div>

                <div class="px-6 py-4 bg-slate-50/70 dark:bg-navy-900/50 border-t border-slate-200 dark:border-slate-700 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2.5">
                    <a href="{{ route('barangs.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Barang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
