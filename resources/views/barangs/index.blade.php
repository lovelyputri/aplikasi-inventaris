@extends('layouts.app')

@section('title', 'Data Barang - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Data Barang Inventaris')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-[1.7rem] font-extrabold text-slate-900 dark:text-white tracking-tight">
                Data Inventaris Barang
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kelola seluruh aset komputer, pantau stok minimum (&le; {{ $threshold }} unit), dan cetak label QR Code.
            </p>
        </div>

        <a href="{{ route('barangs.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Barang Baru</span>
        </a>
    </div>

    {{-- Statistik Ringkas Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 px-4 py-3 shadow-2xs">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jenis Barang</p>
            <p class="text-xl font-extrabold text-slate-900 dark:text-white tabular-nums mt-0.5">{{ number_format($stats['total_jenis']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 px-4 py-3 shadow-2xs">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Unit</p>
            <p class="text-xl font-extrabold text-indigo-600 dark:text-indigo-400 tabular-nums mt-0.5">{{ number_format($stats['total_stok']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 px-4 py-3 shadow-2xs">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Baik</p>
            <p class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400 tabular-nums mt-0.5">{{ number_format($stats['baik']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 px-4 py-3 shadow-2xs">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rusak Ringan</p>
            <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400 tabular-nums mt-0.5">{{ number_format($stats['rusak_ringan']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 px-4 py-3 shadow-2xs">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rusak Berat</p>
            <p class="text-xl font-extrabold text-rose-600 dark:text-rose-400 tabular-nums mt-0.5">{{ number_format($stats['rusak_berat']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 px-4 py-3 shadow-2xs">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stok Menipis</p>
            <p class="text-xl font-extrabold text-amber-600 dark:text-amber-400 tabular-nums mt-0.5">{{ number_format($stats['perlu_perhatian']) }}</p>
        </div>
    </div>

    {{-- Unified Table Card --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
        {{-- Search & Filters --}}
        <div class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-navy-900/40">
            <form method="GET" action="{{ route('barangs.index') }}" class="flex flex-col lg:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari kode barang (misal: LAB-001) atau nama barang..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <select name="kondisi"
                            aria-label="Filter Kondisi"
                            class="sm:w-44 px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                        <option value="">Semua Kondisi</option>
                        @foreach ($kondisiOptions as $option)
                            <option value="{{ $option }}" @selected($kondisiFilter === $option)>{{ $option }}</option>
                        @endforeach
                    </select>

                    <select name="stok"
                            aria-label="Filter Status Stok"
                            class="sm:w-44 px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                        <option value="">Semua Stok</option>
                        <option value="perhatian" @selected($stokFilter === 'perhatian')>Menipis &amp; Habis</option>
                        <option value="menipis" @selected($stokFilter === 'menipis')>Stok Menipis</option>
                        <option value="habis" @selected($stokFilter === 'habis')>Stok Habis (0)</option>
                    </select>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 sm:flex-none inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-900 dark:bg-indigo-600 text-white hover:bg-slate-800 dark:hover:bg-indigo-700 transition">
                            <span>Cari</span>
                        </button>

                        @if ($search !== '' || $kondisiFilter !== '' || $stokFilter !== '')
                            <a href="{{ route('barangs.index') }}"
                               class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Table or Empty State --}}
        @if ($barangs->isEmpty())
            <div class="py-16 px-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-navy-900 text-slate-400 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>

                @if ($search !== '' || $kondisiFilter !== '' || $stokFilter !== '')
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Barang tidak ditemukan</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Tidak ada data barang yang sesuai dengan kata kunci atau filter yang dipilih.
                    </p>
                    <a href="{{ route('barangs.index') }}"
                       class="inline-flex items-center mt-5 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 dark:bg-navy-900 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        Tampilkan Semua Barang
                    </a>
                @else
                    <h2 class="text-base font-bold text-slate-900 dark:text-white">Belum ada data barang</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 max-w-md mx-auto">
                        Tambahkan barang pertama untuk mulai mencatat inventaris laboratorium.
                    </p>
                    <a href="{{ route('barangs.create') }}"
                       class="inline-flex items-center gap-1.5 mt-5 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        + Tambah Barang Pertama
                    </a>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50/90 dark:bg-navy-900/70">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-14">No</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Barang</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Barang</th>
                            <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stok</th>
                            <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kondisi</th>
                            <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/70 bg-white dark:bg-navy-800">
                        @foreach ($barangs as $index => $barang)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/35 transition-colors">
                                <td class="px-5 py-4 text-slate-500 dark:text-slate-400 font-medium tabular-nums whitespace-nowrap">
                                    {{ $barangs->firstItem() + $index }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-100 dark:bg-navy-900 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                        {{ $barang->kode_barang }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 min-w-[14rem]">
                                    <a href="{{ route('barangs.show', $barang) }}"
                                       class="font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition block">
                                        {{ $barang->nama_barang }}
                                    </a>
                                    <span class="text-xs text-slate-400 dark:text-slate-500">
                                        Diperbarui {{ $barang->formatWaktuRelatif($barang->updated_at) ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex flex-col items-center gap-1">
                                        <span class="inline-flex items-center justify-center min-w-[2.75rem] px-2.5 py-1 rounded-lg text-xs font-bold tabular-nums {{ $barang->isOutOfStock() ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : ($barang->isLowStock($threshold) ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300') }}">
                                            {{ number_format($barang->jumlah_stok) }} unit
                                        </span>
                                        @if ($barang->isOutOfStock())
                                            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase">Habis</span>
                                        @elseif ($barang->isLowStock($threshold))
                                            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400 uppercase">Menipis</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $barang->kondisiBadgeClass() }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $barang->kondisiDotClass() }}"></span>
                                        {{ $barang->kondisi }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        <a href="{{ route('barangs.qr', $barang) }}"
                                           title="Cetak QR Code"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-200/70 dark:border-indigo-500/30 hover:bg-indigo-100 dark:hover:bg-indigo-500/25 transition">
                                            QR
                                        </a>

                                        <a href="{{ route('barangs.show', $barang) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-navy-900 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-700 dark:hover:text-indigo-300 border border-slate-200 dark:border-slate-700 transition">
                                            Detail
                                        </a>

                                        <a href="{{ route('barangs.edit', $barang) }}"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-500/25 border border-amber-200 dark:border-amber-500/30 transition">
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('barangs.destroy', $barang) }}"
                                              class="js-delete-form inline"
                                              data-item-name="{{ $barang->nama_barang }}"
                                              data-item-code="{{ $barang->kode_barang }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-500/25 border border-rose-200 dark:border-rose-500/30 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination (Otomatis Tampil Hanya Jika Data > 10) --}}
            @if ($barangs->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-navy-900/40">
                    {{ $barangs->links('vendor.pagination.tailwind-id') }}
                </div>
            @endif
        @endif
    </div>
@endsection
