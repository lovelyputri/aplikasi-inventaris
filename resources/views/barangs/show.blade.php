@extends('layouts.app')

@section('title', $barang->nama_barang . ' - Detail Barang')
@section('page_heading', 'Detail Barang')

@section('content')
    <div class="max-w-5xl mx-auto">
        {{-- Breadcrumb & Header --}}
        <div class="mb-6">
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 mb-3">
                <a href="{{ route('barangs.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">Data Barang</a>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-800 dark:text-slate-200 font-semibold font-mono">{{ $barang->kode_barang }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $barang->nama_barang }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Informasi lengkap perangkat, status stok, QR Code aset, dan riwayat waktu pencatatan.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('barangs.index') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>Kembali</span>
                    </a>

                    <a href="{{ route('barangs.edit', $barang) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Barang</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Left Column: Primary Info & Timestamps (8 cols) --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3 bg-slate-50/50 dark:bg-navy-900/40">
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-500/30">
                                {{ $barang->kode_barang }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $barang->kondisiBadgeClass() }}">
                                <span class="w-2 h-2 rounded-full {{ $barang->kondisiDotClass() }}"></span>
                                {{ $barang->kondisi }}
                            </span>
                        </div>

                        @if ($barang->isOutOfStock())
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">
                                Stok Habis
                            </span>
                        @elseif ($barang->isLowStock($threshold))
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-300">
                                Peringatan Stok Menipis (&le; {{ $threshold }})
                            </span>
                        @endif
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="rounded-xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/60 dark:bg-navy-900/60 p-4">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kode Barang</p>
                                <p class="mt-1.5 text-lg font-mono font-bold text-slate-900 dark:text-white">{{ $barang->kode_barang }}</p>
                            </div>

                            <div class="rounded-xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/60 dark:bg-navy-900/60 p-4">
                                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Jumlah Stok Tersedia</p>
                                <p class="mt-1.5 text-lg font-extrabold tabular-nums {{ $barang->isOutOfStock() ? 'text-rose-600 dark:text-rose-400' : ($barang->isLowStock($threshold) ? 'text-amber-600 dark:text-amber-400' : 'text-indigo-600 dark:text-indigo-400') }}">
                                    {{ number_format($barang->jumlah_stok) }} unit
                                </p>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/60 dark:bg-navy-900/60 p-4">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi Barang</p>
                            <p class="mt-2 text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                                {{ $barang->deskripsi ?: 'Belum ada deskripsi khusus. ' . $barang->kondisiKeterangan() }}
                            </p>
                        </div>

                        {{-- Informasi Waktu Indonesia --}}
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-4 py-3 bg-slate-50 dark:bg-navy-900/70 border-b border-slate-200 dark:border-slate-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h2 class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider">
                                    Riwayat Waktu Pencatatan
                                </h2>
                            </div>

                            <dl class="divide-y divide-slate-100 dark:divide-slate-700/70">
                                <div class="px-4 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Tanggal Ditambahkan</dt>
                                    <dd class="text-sm font-semibold text-slate-900 dark:text-white sm:text-right">
                                        {{ $barang->formatWaktuIndonesia($barang->created_at) }}
                                        @if ($barang->created_at)
                                            <span class="block text-xs font-normal text-slate-400 dark:text-slate-500">
                                                ({{ $barang->formatWaktuRelatif($barang->created_at) }})
                                            </span>
                                        @endif
                                    </dd>
                                </div>

                                <div class="px-4 py-3.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                    <dt class="text-sm font-medium text-slate-500 dark:text-slate-400">Terakhir Diperbarui</dt>
                                    <dd class="text-sm font-semibold text-slate-900 dark:text-white sm:text-right">
                                        {{ $barang->formatWaktuIndonesia($barang->updated_at) }}
                                        @if ($barang->updated_at)
                                            <span class="block text-xs font-normal text-slate-400 dark:text-slate-500">
                                                ({{ $barang->formatWaktuRelatif($barang->updated_at) }})
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/70 dark:bg-navy-900/50 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-3">
                        <a href="{{ route('barangs.index') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            &larr; Kembali ke Daftar
                        </a>

                        <form method="POST"
                              action="{{ route('barangs.destroy', $barang) }}"
                              class="js-delete-form"
                              data-item-name="{{ $barang->nama_barang }}"
                              data-item-code="{{ $barang->kode_barang }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30 hover:bg-rose-100 dark:hover:bg-rose-500/25 transition">
                                Hapus Barang
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Riwayat Aktivitas Barang Ini --}}
                <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Aktivitas Barang</h3>
                        <a href="{{ route('riwayat.index', ['search' => $barang->kode_barang]) }}"
                           class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                            Semua Log
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-700/70">
                        @forelse ($logs as $log)
                            <div class="px-6 py-3.5 flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold {{ $log->aktivitasBadgeClass() }}">
                                            {{ $log->aktivitas }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $log->user_name }}</span>
                                    </div>
                                    @if ($log->keterangan)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $log->keterangan }}</p>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 whitespace-nowrap">
                                    {{ $log->formatWaktuIndonesia() }}
                                </span>
                            </div>
                        @empty
                            <div class="px-6 py-6 text-center text-xs text-slate-500 dark:text-slate-400">
                                Belum ada log aktivitas tercatat untuk barang ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Right Column: QR Code Card (4 cols) --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-navy-800 rounded-2xl border border-slate-200/90 dark:border-slate-700/80 shadow-xs p-6 text-center sticky top-24">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-300">
                        Identitas QR Code Aset
                    </span>

                    <h2 class="text-base font-bold text-slate-900 dark:text-white mt-3">
                        {{ $barang->kode_barang }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Pindai untuk membuka halaman detail barang ini secara langsung.
                    </p>

                    <div class="my-5 inline-flex items-center justify-center p-4 rounded-2xl bg-white border border-slate-200 shadow-xs">
                        <div id="barang-qr-container" class="w-44 h-44 flex items-center justify-center"></div>
                    </div>

                    <p class="text-[11px] font-mono text-slate-400 dark:text-slate-500 break-all mb-5">
                        {{ $qrUrl }}
                    </p>

                    <div class="space-y-2.5">
                        <a href="{{ route('barangs.qr', $barang) }}"
                           class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Label QR Code</span>
                        </a>

                        <button type="button"
                                onclick="downloadQrPng()"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold border border-slate-300 dark:border-slate-600 bg-white dark:bg-navy-900 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Unduh QR (PNG)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <script>
        (function () {
            const target = document.getElementById('barang-qr-container');
            const qrUrl = @json($qrUrl);

            if (typeof qrcode === 'function' && target) {
                const qr = qrcode(0, 'M');
                qr.addData(qrUrl);
                qr.make();
                target.innerHTML = qr.createSvgTag({ cellSize: 4, margin: 0, scalable: true });
                const svg = target.querySelector('svg');
                if (svg) {
                    svg.classList.add('w-full', 'h-full');
                }
            } else if (target) {
                target.innerHTML = '<span class="text-xs text-slate-400">QR Code membutuhkan koneksi CDN</span>';
            }
        })();

        function downloadQrPng() {
            const svg = document.querySelector('#barang-qr-container svg');
            if (!svg) return;

            const serializer = new XMLSerializer();
            const svgStr = serializer.serializeToString(svg);
            const canvas = document.createElement('canvas');
            canvas.width = 512;
            canvas.height = 512;
            const ctx = canvas.getContext('2d');
            const img = new Image();
            img.onload = function () {
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 32, 32, 448, 448);
                const a = document.createElement('a');
                a.download = 'qr-{{ \Illuminate\Support\Str::slug($barang->kode_barang) }}.png';
                a.href = canvas.toDataURL('image/png');
                a.click();
            };
            img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgStr)));
        }
    </script>
@endpush
