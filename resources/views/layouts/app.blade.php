<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($appSettings->nama_aplikasi ?? 'Inventaris Lab') . ' - ' . ($appSettings->nama_sekolah ?? 'SMK Muhammadiyah 6 Rogojampi'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        (function () {
            try {
                const saved = localStorage.getItem('inventaris_theme');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'dark' || (!saved && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {}
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    },
                    colors: {
                        navy: {
                            950: '#0b1120',
                            900: '#111827',
                            800: '#1a2335',
                            700: '#273449',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-navy-950 text-slate-800 dark:text-slate-100 min-h-screen font-sans antialiased transition-colors duration-200 selection:bg-indigo-100 selection:text-indigo-900 dark:selection:bg-indigo-500/30 dark:selection:text-indigo-200">

    {{-- Mobile Sidebar Backdrop --}}
    <div id="sidebar-backdrop"
         class="fixed inset-0 z-40 bg-slate-900/50 dark:bg-black/60 backdrop-blur-xs hidden lg:hidden transition-opacity"
         onclick="toggleMobileSidebar(false)"></div>

    <div class="min-h-screen flex">
        {{-- Sidebar Navigation --}}
        <aside id="app-sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-navy-900 border-r border-slate-200/90 dark:border-slate-800 flex flex-col transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">

            {{-- Brand & Official School Logo --}}
            <div class="px-5 py-4 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0 group">
                    <div class="w-12 h-12 rounded-xl bg-white dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-700 p-1 flex items-center justify-center shrink-0 shadow-xs">
                        <img src="{{ asset($appSettings->resolvedLogoPath()) }}"
                             alt="Logo {{ $appSettings->nama_sekolah }}"
                             class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0">
                        <span class="block text-base font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $appSettings->nama_aplikasi }}
                        </span>
                        <span class="block text-[11px] font-medium text-slate-500 dark:text-slate-400 leading-snug truncate mt-0.5">
                            {{ $appSettings->tagline }}
                        </span>
                    </div>
                </a>

                <button type="button"
                        onclick="toggleMobileSidebar(false)"
                        aria-label="Tutup Menu"
                        class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Primary Navigation Links --}}
            <nav aria-label="Menu Utama" class="flex-1 px-3.5 py-5 space-y-1.5 overflow-y-auto">
                <p class="px-3 mb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    Menu Utama
                </p>

                @php
                    $navItems = [
                        [
                            'label' => 'Dashboard',
                            'route' => 'dashboard',
                            'active' => request()->routeIs('dashboard', 'home'),
                            'badge' => null,
                            'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        ],
                        [
                            'label' => 'Data Barang',
                            'route' => 'barangs.index',
                            'active' => request()->routeIs('barangs.*'),
                            'badge' => $lowStockCount > 0 ? $lowStockCount : null,
                            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                        ],
                        [
                            'label' => 'Laporan',
                            'route' => 'laporan.index',
                            'active' => request()->routeIs('laporan.*'),
                            'badge' => null,
                            'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        ],
                        [
                            'label' => 'Riwayat Aktivitas',
                            'route' => 'riwayat.index',
                            'active' => request()->routeIs('riwayat.*'),
                            'badge' => null,
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        [
                            'label' => 'Pengaturan',
                            'route' => 'pengaturan.index',
                            'active' => request()->routeIs('pengaturan.*'),
                            'badge' => null,
                            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        ],
                    ];
                @endphp

                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="relative flex items-center justify-between gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group {{ $item['active'] ? 'bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 shadow-2xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 hover:text-slate-900 dark:hover:text-white' }}">
                        @if ($item['active'])
                            <span class="absolute left-0 top-2 bottom-2 w-1 rounded-r-full bg-indigo-600 dark:bg-indigo-400"></span>
                        @endif

                        <span class="flex items-center gap-3 min-w-0">
                            <svg class="w-[1.15rem] h-[1.15rem] shrink-0 {{ $item['active'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500 group-hover:text-slate-600 dark:group-hover:text-slate-300' }}"
                                 fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                            <span class="truncate">{{ $item['label'] }}</span>
                        </span>

                        @if ($item['badge'])
                            <span title="{{ $item['badge'] }} barang stok menipis/habis"
                                  class="inline-flex items-center justify-center min-w-[1.35rem] h-5 px-1.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach

                <div class="pt-4 mt-4 border-t border-slate-200/70 dark:border-slate-800">
                    <a href="{{ route('barangs.create') }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Barang Baru</span>
                    </a>
                </div>
            </nav>

            {{-- Bottom School Identity & User Card --}}
            <div class="p-3.5 border-t border-slate-200/80 dark:border-slate-800 space-y-3 bg-slate-50/60 dark:bg-navy-950/50">
                <div class="rounded-xl bg-white dark:bg-navy-800 border border-slate-200/80 dark:border-slate-700/80 p-3">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset($appSettings->resolvedLogoPath()) }}"
                             alt="Logo {{ $appSettings->nama_sekolah }}"
                             class="w-8 h-8 object-contain shrink-0">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                {{ $appSettings->nama_sekolah }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                {{ $appSettings->alamat_sekolah ?: 'Laboratorium Komputer' }}
                            </p>
                        </div>
                    </div>
                </div>

                <a href="{{ route('pengaturan.index') }}"
                   class="flex items-center justify-between gap-2 px-2 py-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-navy-800 transition group">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-xs font-bold shrink-0">
                            {{ strtoupper(mb_substr($operatorName ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                {{ $operatorName ?? 'Administrator Lab' }}
                            </p>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                Pengelola Laboratorium
                            </p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                        Atur
                    </span>
                </a>
            </div>
        </aside>

        {{-- Main Area --}}
        <div class="flex-1 lg:pl-72 flex flex-col min-w-0">
            {{-- Top Navbar --}}
            <header class="sticky top-0 z-20 h-16 bg-white/90 dark:bg-navy-900/90 backdrop-blur-md border-b border-slate-200/90 dark:border-slate-800 transition-colors">
                <div class="h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3">
                    {{-- Left: Mobile Drawer Trigger + Page Context --}}
                    <div class="flex items-center gap-3 min-w-0">
                        <button type="button"
                                onclick="toggleMobileSidebar(true)"
                                aria-label="Buka Menu"
                                class="lg:hidden p-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="flex items-center gap-2.5 lg:hidden min-w-0">
                            <img src="{{ asset($appSettings->resolvedLogoPath()) }}"
                                 alt="Logo Sekolah"
                                 class="w-8 h-8 object-contain shrink-0">
                            <span class="text-sm font-bold text-slate-900 dark:text-white truncate">
                                {{ $appSettings->nama_aplikasi }}
                            </span>
                        </div>

                        <div class="hidden lg:flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                            <span class="font-semibold text-slate-800 dark:text-slate-100">@yield('page_heading', 'Panel Kontrol')</span>
                            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                            <span class="text-xs truncate">{{ $appSettings->nama_sekolah }}</span>
                        </div>
                    </div>

                    {{-- Right: Real-time Digital Clock (WIB), Low Stock Bell, Theme Toggle --}}
                    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                        {{-- Real-Time Digital Clock (Asia/Jakarta WIB) --}}
                        <div id="wib-clock-box"
                             class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-100/90 dark:bg-navy-800 border border-slate-200/80 dark:border-slate-700/80 text-xs transition-colors">
                            <span class="relative flex h-2 w-2 shrink-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <div class="leading-tight">
                                <span id="wib-clock-time" class="font-mono font-bold text-slate-900 dark:text-white tracking-tight tabular-nums">--:--:--</span>
                                <span class="font-mono text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 ml-0.5">WIB</span>
                                <span id="wib-clock-date" class="hidden md:inline text-[11px] text-slate-500 dark:text-slate-400 ml-2 pl-2 border-l border-slate-300 dark:border-slate-600">
                                    Memuat tanggal...
                                </span>
                            </div>
                        </div>

                        {{-- Low-Stock Alert Bell Dropdown --}}
                        <div class="relative" id="stock-notif-wrapper">
                            <button type="button"
                                    onclick="toggleStockNotif()"
                                    aria-label="Notifikasi Stok Menipis"
                                    class="relative p-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-navy-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <svg class="w-4 h-4 sm:w-[1.15rem] sm:h-[1.15rem]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if ($lowStockCount > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-rose-600 text-white text-[10px] font-bold flex items-center justify-center shadow-xs">
                                        {{ $lowStockCount > 99 ? '99+' : $lowStockCount }}
                                    </span>
                                @endif
                            </button>

                            <div id="stock-notif-panel"
                                 class="hidden absolute right-0 mt-2 w-80 rounded-2xl bg-white dark:bg-navy-800 border border-slate-200 dark:border-slate-700 shadow-xl z-40 overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                                        Peringatan Stok (&le; {{ $appSettings->batas_stok_minimum }})
                                    </span>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $lowStockCount > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' }}">
                                        {{ $lowStockCount }} item
                                    </span>
                                </div>

                                @if ($lowStockItems->isEmpty())
                                    <div class="p-5 text-center text-xs text-slate-500 dark:text-slate-400">
                                        Semua stok barang saat ini berada di atas batas minimum.
                                    </div>
                                @else
                                    <div class="divide-y divide-slate-100 dark:divide-slate-700/70 max-h-64 overflow-y-auto">
                                        @foreach ($lowStockItems as $item)
                                            <a href="{{ route('barangs.show', $item) }}"
                                               class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">{{ $item->nama_barang }}</p>
                                                    <p class="text-[11px] font-mono text-slate-500 dark:text-slate-400">{{ $item->kode_barang }}</p>
                                                </div>
                                                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] font-bold {{ $item->jumlah_stok <= 0 ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300' }}">
                                                    {{ $item->jumlah_stok <= 0 ? 'Habis' : $item->jumlah_stok . ' unit' }}
                                                </span>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="px-4 py-2.5 bg-slate-50 dark:bg-navy-900/60 border-t border-slate-200 dark:border-slate-700 text-center">
                                        <a href="{{ route('barangs.index', ['stok' => 'perhatian']) }}"
                                           class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                            Lihat Semua Stok Menipis &rarr;
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Light / Dark Mode Toggle --}}
                        <button type="button"
                                id="theme-toggle-btn"
                                onclick="toggleAppTheme()"
                                aria-label="Ganti Tema Siang / Malam"
                                title="Ganti Tema Light / Dark Mode"
                                class="inline-flex items-center gap-1.5 px-2.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-navy-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            {{-- Sun Icon (shown in Dark Mode to switch to Light) --}}
                            <svg id="theme-icon-sun" class="w-4 h-4 sm:w-[1.15rem] sm:h-[1.15rem] text-amber-400 hidden dark:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            {{-- Moon Icon (shown in Light Mode to switch to Dark) --}}
                            <svg id="theme-icon-moon" class="w-4 h-4 sm:w-[1.15rem] sm:h-[1.15rem] text-indigo-600 block dark:hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <span class="hidden md:inline text-xs font-semibold">
                                <span class="dark:hidden">Gelap</span>
                                <span class="hidden dark:inline">Terang</span>
                            </span>
                        </button>
                    </div>
                </div>

                {{-- Mobile Compact Clock Strip --}}
                <div class="sm:hidden px-4 py-1.5 bg-slate-50 dark:bg-navy-950/70 border-t border-slate-200/70 dark:border-slate-800 flex items-center justify-between text-[11px]">
                    <span id="wib-clock-date-mobile" class="text-slate-500 dark:text-slate-400 font-medium">Memuat tanggal...</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200 tabular-nums">
                        <span id="wib-clock-time-mobile">--:--:--</span> <span class="text-indigo-600 dark:text-indigo-400">WIB</span>
                    </span>
                </div>
            </header>

            {{-- Page Body --}}
            <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                @if (session('success'))
                    <div role="alert"
                         class="mb-6 rounded-2xl border border-emerald-200 dark:border-emerald-500/30 bg-emerald-50/90 dark:bg-emerald-500/10 px-4 py-3.5 text-emerald-900 dark:text-emerald-200 flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3 text-sm font-medium">
                            <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button"
                                onclick="this.closest('[role=alert]').remove()"
                                class="text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 text-xs font-semibold px-2.5 py-1 rounded-lg transition shrink-0">
                            Tutup
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div role="alert"
                         class="mb-6 rounded-2xl border border-rose-200 dark:border-rose-500/30 bg-rose-50/90 dark:bg-rose-500/10 px-4 py-3.5 text-rose-900 dark:text-rose-200 flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3 text-sm font-medium">
                            <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.65 18.5h16.7a1 1 0 00.86-1.64l-7.5-13a1 1 0 00-1.42 0z"/>
                                </svg>
                            </span>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button"
                                onclick="this.closest('[role=alert]').remove()"
                                class="text-rose-700 dark:text-rose-300 hover:bg-rose-100 dark:hover:bg-rose-500/20 text-xs font-semibold px-2.5 py-1 rounded-lg transition shrink-0">
                            Tutup
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="border-t border-slate-200/80 dark:border-slate-800 bg-white dark:bg-navy-900 mt-auto transition-colors">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-xs text-slate-500 dark:text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
                    <span class="font-medium text-slate-600 dark:text-slate-300">
                        {{ $appSettings->nama_aplikasi }} &bull; {{ $appSettings->nama_sekolah }}
                    </span>
                    <span>Versi {{ $appSettings->versi_aplikasi }} &bull; Laravel &amp; SQLite</span>
                </div>
            </footer>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="delete-confirm-modal"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/55 dark:bg-black/70 backdrop-blur-xs"
         role="dialog"
         aria-modal="true"
         aria-labelledby="delete-modal-title">
        <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-11 h-11 rounded-xl bg-rose-50 dark:bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/25 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div>
                        <h3 id="delete-modal-title" class="text-base font-bold text-slate-900 dark:text-white">
                            Konfirmasi Hapus Barang
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1.5 leading-relaxed">
                            Apakah Anda yakin ingin menghapus barang <span id="delete-modal-item" class="font-semibold text-slate-900 dark:text-white"></span>? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-slate-50 dark:bg-navy-900/60 border-t border-slate-200 dark:border-slate-700 flex items-center justify-end gap-2.5">
                <button type="button"
                        id="delete-modal-cancel"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-800 border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Batal
                </button>
                <button type="button"
                        id="delete-modal-confirm"
                        class="px-4 py-2 rounded-xl text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 shadow-xs transition">
                    Ya, Hapus Barang
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileSidebar(open) {
            const sidebar = document.getElementById('app-sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (open) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        function toggleAppTheme() {
            const root = document.documentElement;
            const isDark = root.classList.toggle('dark');
            try {
                localStorage.setItem('inventaris_theme', isDark ? 'dark' : 'light');
            } catch (e) {}
        }

        function toggleStockNotif() {
            const panel = document.getElementById('stock-notif-panel');
            panel.classList.toggle('hidden');
        }

        document.addEventListener('click', function (e) {
            const wrapper = document.getElementById('stock-notif-wrapper');
            const panel = document.getElementById('stock-notif-panel');
            if (wrapper && panel && !wrapper.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });

        // Real-time Digital Clock (Asia/Jakarta WIB)
        (function () {
            const timeEl = document.getElementById('wib-clock-time');
            const dateEl = document.getElementById('wib-clock-date');
            const timeMobileEl = document.getElementById('wib-clock-time-mobile');
            const dateMobileEl = document.getElementById('wib-clock-date-mobile');

            const timeFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            const dateFormatter = new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            function updateClock() {
                const now = new Date();
                const timeStr = timeFormatter.format(now).replace(/\./g, ':');
                const dateStr = dateFormatter.format(now);

                if (timeEl) timeEl.textContent = timeStr;
                if (dateEl) dateEl.textContent = dateStr;
                if (timeMobileEl) timeMobileEl.textContent = timeStr;
                if (dateMobileEl) dateMobileEl.textContent = dateStr;
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();

        // Delete confirmation modal
        (function () {
            const modal = document.getElementById('delete-confirm-modal');
            const itemLabel = document.getElementById('delete-modal-item');
            const cancelBtn = document.getElementById('delete-modal-cancel');
            const confirmBtn = document.getElementById('delete-modal-confirm');
            let pendingForm = null;

            function closeModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                pendingForm = null;
            }

            document.addEventListener('submit', function (event) {
                const form = event.target;
                if (!form.classList.contains('js-delete-form')) return;
                if (form.dataset.confirmed === 'true') return;

                event.preventDefault();
                pendingForm = form;
                const name = form.dataset.itemName || 'ini';
                const code = form.dataset.itemCode ? ` (${form.dataset.itemCode})` : '';
                itemLabel.textContent = `"${name}"${code}`;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                confirmBtn.focus();
            });

            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (event) {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
            confirmBtn.addEventListener('click', function () {
                if (!pendingForm) return;
                pendingForm.dataset.confirmed = 'true';
                pendingForm.submit();
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
