@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Mobile Pagination --}}
        <div class="flex items-center justify-between sm:hidden gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed">
                    &larr; Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    &larr; Sebelumnya
                </a>
            @endif

            <span class="text-xs font-medium text-slate-600 dark:text-slate-300">
                Hal. {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    Berikutnya &rarr;
                </a>
            @else
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-navy-800 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed">
                    Berikutnya &rarr;
                </span>
            @endif
        </div>

        {{-- Info Jumlah Data --}}
        <div class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 text-center sm:text-left">
            Menampilkan
            <span class="font-semibold text-slate-900 dark:text-white">{{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-semibold text-slate-900 dark:text-white">{{ number_format($paginator->total()) }}</span>
            data
        </div>

        {{-- Desktop Pagination Links --}}
        <div class="hidden sm:flex items-center gap-1.5">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-navy-900 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed select-none">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Sebelumnya</span>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2 text-xs font-medium text-slate-400 dark:text-slate-500 select-none">
                        {{ $element }}
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2.5 text-xs font-bold text-white bg-indigo-600 border border-indigo-600 rounded-xl shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center min-w-[2rem] h-8 px-2.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-500/20 hover:text-indigo-700 dark:hover:text-indigo-300 hover:border-indigo-300 dark:hover:border-indigo-500/40 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200 bg-white dark:bg-navy-800 border border-slate-300 dark:border-slate-600 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    <span>Berikutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            @else
                <span aria-disabled="true"
                      class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-navy-900 border border-slate-200 dark:border-slate-700 rounded-xl cursor-not-allowed select-none">
                    <span>Berikutnya</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
