@extends('layouts.app')

@section('title', 'Riwayat Aktivitas - ' . $appSettings->nama_aplikasi)
@section('page_heading', 'Riwayat Aktivitas')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-[1.7rem] font-extrabold text-slate-900 dark:text-white tracking-tight">
                Riwayat Aktivitas Inventaris
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Log lengkap penambahan, perubahan, penghapusan barang, dan pengaturan sistem.
            </p>
        </div>
    </div>

    <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
        {{-- Filter Bar --}}
        <div class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-700/80 bg-slate-50/50 dark:bg-navy-900/40">
            <form method="GET" action="{{ route('riwayat.index') }}" class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </span>
                    <input type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama barang, kode, pengguna, atau keterangan..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <select name="aktivitas"
                            aria-label="Filter Jenis Aktivitas"
                            class="sm:w-48 px-3.5 py-2.5 text-sm bg-white dark:bg-navy-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition">
                        <option value="">Semua Aktivitas</option>
                        @foreach ($aktivitasTypes as $type)
                            <option value="{{ $type }}" @selected($aktivitasFilter === $type)>{{ $type }}</option>
                        @endforeach
                    </select>

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-900 dark:bg-indigo-600 text-white hover:bg-slate-800 dark:hover:bg-indigo-700 transition">
                            Filter
                        </button>
                        @if ($search !== '' || $aktivitasFilter !== '')
                            <a href="{{ route('riwayat.index') }}"
                               class="px-3.5 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if ($logs->isEmpty())
            <div class="py-16 px-6 text-center">
                <p class="text-base font-bold text-slate-900 dark:text-white">Belum ada riwayat aktivitas</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Aktivitas pengelolaan barang akan otomatis tercatat di halaman ini.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-sm">
                    <thead class="bg-slate-50/90 dark:bg-navy-900/70">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aktivitas</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Barang / Objek</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengguna</th>
                            <th class="px-5 py-3.5 text-right text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tanggal &amp; Waktu (WIB)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/70 bg-white dark:bg-navy-800">
                        @foreach ($logs as $log)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/35 transition">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $log->aktivitasBadgeClass() }}">
                                        {{ $log->aktivitas }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $log->nama_barang }}</p>
                                    @if ($log->kode_barang)
                                        <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ $log->kode_barang }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-600 dark:text-slate-300 text-xs sm:text-sm">
                                    {{ $log->keterangan ?: '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $log->user_name }}</span>
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <span class="block text-xs font-semibold text-slate-800 dark:text-slate-200">
                                        {{ $log->formatWaktuIndonesia() }}
                                    </span>
                                    <span class="block text-[11px] text-slate-400 dark:text-slate-500">
                                        {{ $log->formatWaktuRelatif() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-navy-900/40">
                    {{ $logs->links('vendor.pagination.tailwind-id') }}
                </div>
            @endif
        @endif
    </div>
@endsection
