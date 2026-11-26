@extends('layouts.app')

@section('title', 'Laporan Inventaris - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Laporan Inventaris')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-[1.7rem] font-extrabold text-slate-900 dark:text-white tracking-tight">
                Laporan Data Inventaris
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Filter laporan berdasarkan kondisi maupun periode tanggal, lalu export ke PDF atau Excel (.xlsx).
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('laporan.export.pdf', array_filter($filters)) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Export PDF</span>
            </a>

            <a href="{{ route('laporan.export.excel', array_filter($filters)) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Export Excel</span>
            </a>
        </div>
    </div>

    {{-- Filter Form Card --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs p-5 mb-6">
        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="kondisi" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Kondisi Barang
                </label>
                <select id="kondisi" name="kondisi"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
                    <option value="">Semua Kondisi</option>
                    @foreach ($kondisiOptions as $option)
                        <option value="{{ $option }}" @selected($filters['kondisi'] === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="tanggal_mulai" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Dari Tanggal
                </label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] }}"
                       class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
            </div>

            <div>
                <label for="tanggal_selesai" class="block text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Sampai Tanggal
                </label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] }}"
                       class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs transition">
                    Tampilkan
                </button>
                @if (array_filter($filters) !== [])
                    <a href="{{ route('laporan.index') }}"
                       class="px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-900 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Ringkasan Laporan Terfilter --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 p-4 shadow-2xs">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Jenis Barang</p>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-white tabular-nums mt-1">{{ number_format($summary['total_jenis']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 p-4 shadow-2xs">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Unit</p>
            <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 tabular-nums mt-1">{{ number_format($summary['total_stok']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 p-4 shadow-2xs">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Baik</p>
            <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 tabular-nums mt-1">{{ number_format($summary['baik']) }}</p>
        </div>
        <div class="bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 p-4 shadow-2xs">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Rusak Ringan</p>
            <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 tabular-nums mt-1">{{ number_format($summary['rusak_ringan']) }}</p>
        </div>
        <div class="col-span-2 sm:col-span-1 bg-white dark:bg-navy-800 rounded-xl border border-slate-200/90 dark:border-slate-700/80 p-4 shadow-2xs">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Rusak Berat</p>
            <p class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 tabular-nums mt-1">{{ number_format($summary['rusak_berat']) }}</p>
        </div>
    </div>

    {{-- Pratinjau Tabel Laporan --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pratinjau Data Laporan</h2>
            <span class="text-xs text-slate-500 dark:text-slate-400">{{ number_format($barangs->total()) }} baris cocok</span>
        </div>

        @if ($barangs->isEmpty())
            <div class="py-14 px-6 text-center text-sm text-slate-500 dark:text-slate-400">
                Tidak ada data inventaris yang sesuai dengan filter laporan.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50/90 dark:bg-navy-900/70">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-14">No</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stok</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kondisi</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal Dicatat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/70 bg-white dark:bg-navy-800">
                        @foreach ($barangs as $index => $barang)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/35 transition">
                                <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400 tabular-nums">{{ $barangs->firstItem() + $index }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $barang->kode_barang }}</td>
                                <td class="px-5 py-3.5 font-semibold text-slate-900 dark:text-white">{{ $barang->nama_barang }}</td>
                                <td class="px-5 py-3.5 text-center font-bold tabular-nums text-indigo-700 dark:text-indigo-300">{{ number_format($barang->jumlah_stok) }} unit</td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $barang->kondisiBadgeClass() }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $barang->kondisiDotClass() }}"></span>
                                        {{ $barang->kondisi }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                                    {{ $barang->formatWaktuIndonesia($barang->created_at) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($barangs->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-navy-900/40">
                    {{ $barangs->links('vendor.pagination.tailwind-id') }}
                </div>
            @endif
        @endif
    </div>
@endsection
