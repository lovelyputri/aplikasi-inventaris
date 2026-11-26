@extends('layouts.app')

@section('title', 'Dashboard - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Dashboard Inventaris')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-7">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Ringkasan Inventaris Laboratorium
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Pantau kesehatan aset, peringatan stok minimum, dan aktivitas terbaru di {{ $appSettings->nama_sekolah }}.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('laporan.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-700 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Laporan</span>
            </a>

            <a href="{{ route('barangs.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Barang</span>
            </a>
        </div>
    </div>

    {{-- Row 1: Asymmetric Bento Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
        {{-- Featured Asset Overview Card (7 cols) --}}
        <div class="lg:col-span-7 bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400"></span>
                            Ringkasan Aset Lab
                        </span>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mt-3">
                            Total Kapasitas &amp; Ketersediaan
                        </h2>
                    </div>

                    <a href="{{ route('barangs.index') }}"
                       class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline shrink-0">
                        Kelola Data &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-5">
                    <div class="rounded-2xl bg-slate-50 dark:bg-navy-900/80 border border-slate-200/80 dark:border-slate-700/80 p-4">
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Total Jenis Barang</p>
                        <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tabular-nums mt-1.5">
                            {{ number_format($stats['total_jenis']) }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Kategori perangkat tercatat</p>
                    </div>

                    <div class="rounded-2xl bg-indigo-50/70 dark:bg-indigo-500/10 border border-indigo-200/70 dark:border-indigo-500/25 p-4">
                        <p class="text-xs font-medium text-indigo-700 dark:text-indigo-300">Total Unit Stok</p>
                        <p class="text-3xl sm:text-4xl font-extrabold text-indigo-700 dark:text-indigo-300 tabular-nums mt-1.5">
                            {{ number_format($stats['total_stok']) }}
                        </p>
                        <p class="text-xs text-indigo-600/80 dark:text-indigo-300/80 mt-1">Akumulasi unit di laboratorium</p>
                    </div>
                </div>
            </div>

            {{-- Bottom Mini Status Bar --}}
            <div class="grid grid-cols-3 gap-3 mt-5 pt-5 border-t border-slate-100 dark:border-slate-700/80">
                <a href="{{ route('barangs.index', ['kondisi' => 'Baik']) }}"
                   class="group rounded-xl p-3 bg-emerald-50/60 dark:bg-emerald-500/10 border border-emerald-200/70 dark:border-emerald-500/20 hover:border-emerald-300 dark:hover:border-emerald-500/40 transition">
                    <p class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-300">Kondisi Baik</p>
                    <p class="text-xl font-extrabold text-emerald-700 dark:text-emerald-300 tabular-nums mt-0.5">
                        {{ number_format($stats['baik']) }}
                    </p>
                </a>

                <a href="{{ route('barangs.index', ['kondisi' => 'Rusak Ringan']) }}"
                   class="group rounded-xl p-3 bg-amber-50/60 dark:bg-amber-500/10 border border-amber-200/70 dark:border-amber-500/20 hover:border-amber-300 dark:hover:border-amber-500/40 transition">
                    <p class="text-[11px] font-semibold text-amber-700 dark:text-amber-300">Rusak Ringan</p>
                    <p class="text-xl font-extrabold text-amber-700 dark:text-amber-300 tabular-nums mt-0.5">
                        {{ number_format($stats['rusak_ringan']) }}
                    </p>
                </a>

                <a href="{{ route('barangs.index', ['kondisi' => 'Rusak Berat']) }}"
                   class="group rounded-xl p-3 bg-rose-50/60 dark:bg-rose-500/10 border border-rose-200/70 dark:border-rose-500/20 hover:border-rose-300 dark:hover:border-rose-500/40 transition">
                    <p class="text-[11px] font-semibold text-rose-700 dark:text-rose-300">Rusak Berat</p>
                    <p class="text-xl font-extrabold text-rose-700 dark:text-rose-300 tabular-nums mt-0.5">
                        {{ number_format($stats['rusak_berat']) }}
                    </p>
                </a>
            </div>
        </div>

        {{-- Condition Distribution & Health Card (5 cols) --}}
        <div class="lg:col-span-5 bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white">Ringkasan Kondisi Inventaris</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Proporsi kelayakan seluruh perangkat laboratorium.
                </p>

                {{-- Stacked Progress Bar --}}
                <div class="mt-5 h-3 w-full rounded-full bg-slate-100 dark:bg-navy-900 overflow-hidden flex">
                    @if ($stats['total_jenis'] > 0)
                        <div class="bg-emerald-500 h-full transition-all" style="width: {{ $stats['baik_pct'] }}%"></div>
                        <div class="bg-amber-500 h-full transition-all" style="width: {{ $stats['ringan_pct'] }}%"></div>
                        <div class="bg-rose-500 h-full transition-all" style="width: {{ $stats['berat_pct'] }}%"></div>
                    @endif
                </div>

                {{-- Breakdown List --}}
                <div class="mt-5 space-y-3.5">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="font-medium text-slate-700 dark:text-slate-200">Kondisi Baik</span>
                        </div>
                        <div class="font-semibold text-slate-900 dark:text-white tabular-nums">
                            {{ number_format($stats['baik']) }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ $stats['baik_pct'] }}%)</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="font-medium text-slate-700 dark:text-slate-200">Rusak Ringan</span>
                        </div>
                        <div class="font-semibold text-slate-900 dark:text-white tabular-nums">
                            {{ number_format($stats['rusak_ringan']) }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ $stats['ringan_pct'] }}%)</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                            <span class="font-medium text-slate-700 dark:text-slate-200">Rusak Berat</span>
                        </div>
                        <div class="font-semibold text-slate-900 dark:text-white tabular-nums">
                            {{ number_format($stats['rusak_berat']) }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">({{ $stats['berat_pct'] }}%)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Threshold Summary Box --}}
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700/80 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">Status Batas Stok Minimum</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Ambang batas: &le; {{ $threshold }} unit</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                        {{ $stats['stok_menipis'] }} Menipis
                    </span>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">
                        {{ $stats['stok_habis'] }} Habis
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: Low Stock Alerts & Recent Activity Timeline --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
        {{-- Peringatan Stok Menipis & Habis --}}
        <div class="lg:col-span-6 bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.65 18.5h16.7a1 1 0 00.86-1.64l-7.5-13a1 1 0 00-1.42 0z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Perhatian Stok Menipis</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Barang dengan stok &le; {{ $threshold }} unit</p>
                    </div>
                </div>
                <a href="{{ route('barangs.index', ['stok' => 'perhatian']) }}"
                   class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Lihat Semua
                </a>
            </div>

            <div class="flex-1 divide-y divide-slate-100 dark:divide-slate-700/70">
                @forelse ($lowStockBarangs as $item)
                    <div class="px-6 py-3.5 flex items-center justify-between gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                        <div class="min-w-0">
                            <a href="{{ route('barangs.show', $item) }}"
                               class="text-sm font-semibold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 truncate block">
                                {{ $item->nama_barang }}
                            </a>
                            <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $item->kode_barang }}</span>
                        </div>

                        <div class="flex items-center gap-2.5 shrink-0">
                            @if ($item->isOutOfStock())
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">
                                    Stok Habis (0)
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                    Sisa {{ $item->jumlah_stok }} unit
                                </span>
                            @endif

                            <a href="{{ route('barangs.edit', $item) }}"
                               class="px-2.5 py-1 rounded-lg text-xs font-semibold border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                Update
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 px-6 text-center">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 dark:bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Semua stok aman</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tidak ada barang di bawah batas stok minimum.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Aktivitas Barang Terbaru --}}
        <div class="lg:col-span-6 bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden flex flex-col">
            <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-indigo-50 dark:bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Aktivitas Terbaru</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Riwayat perubahan data inventaris</p>
                    </div>
                </div>
                <a href="{{ route('riwayat.index') }}"
                   class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Semua Riwayat
                </a>
            </div>

            <div class="flex-1 divide-y divide-slate-100 dark:divide-slate-700/70">
                @forelse ($recentActivities as $log)
                    <div class="px-6 py-3.5 flex items-start justify-between gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $log->aktivitasBadgeClass() }}">
                                    {{ $log->aktivitas }}
                                </span>
                                <span class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                                    {{ $log->nama_barang }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Oleh <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $log->user_name }}</span>
                                @if ($log->keterangan)
                                    &bull; {{ $log->keterangan }}
                                @endif
                            </p>
                        </div>

                        <span class="text-[11px] text-slate-400 dark:text-slate-500 whitespace-nowrap shrink-0 mt-0.5">
                            {{ $log->formatWaktuRelatif() }}
                        </span>
                    </div>
                @empty
                    <div class="py-12 px-6 text-center">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Belum ada aktivitas tercatat</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Aktivitas tambah, ubah, dan hapus barang akan muncul di sini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Row 3: Barang Terbaru --}}
    <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Barang Terbaru Ditambahkan / Diperbarui</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">5 entri inventaris paling mutakhir</p>
            </div>
            <a href="{{ route('barangs.index') }}"
               class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Tabel Lengkap &rarr;
            </a>
        </div>

        @if ($recentBarangs->isEmpty())
            <div class="py-10 px-6 text-center text-sm text-slate-500 dark:text-slate-400">
                Belum ada barang di laboratorium. <a href="{{ route('barangs.create') }}" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">Tambah barang pertama</a>.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50/80 dark:bg-navy-900/60">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Barang</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kondisi</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/70">
                        @foreach ($recentBarangs as $barang)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition">
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-mono font-semibold bg-slate-100 dark:bg-navy-900 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                        {{ $barang->kode_barang }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-900 dark:text-white">
                                    {{ $barang->nama_barang }}
                                </td>
                                <td class="px-6 py-3.5 text-center whitespace-nowrap font-bold tabular-nums text-indigo-700 dark:text-indigo-300">
                                    {{ number_format($barang->jumlah_stok) }} unit
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $barang->kondisiBadgeClass() }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $barang->kondisiDotClass() }}"></span>
                                        {{ $barang->kondisi }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <a href="{{ route('barangs.show', $barang) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-navy-900 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-700 dark:hover:text-indigo-300 transition">
                                        Detail &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
