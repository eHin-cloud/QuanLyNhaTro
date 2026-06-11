@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between w-full">
        <!-- Mobile Simple View -->
        <div class="flex justify-between flex-1 sm:hidden gap-3">
            @if ($paginator->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2.5 text-xs font-bold text-slate-500 bg-slate-900/20 border border-slate-800/80 rounded-xl cursor-default select-none">
                    Trước
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 text-xs font-bold text-slate-300 bg-slate-900/40 border border-slate-800 hover:border-indigo-500/40 hover:text-white transition-all active:scale-95">
                    Trước
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2.5 text-xs font-bold text-slate-300 bg-slate-900/40 border border-slate-800 hover:border-indigo-500/40 hover:text-white transition-all active:scale-95">
                    Sau
                </a>
            @else
                <span class="relative inline-flex items-center px-4 py-2.5 text-xs font-bold text-slate-500 bg-slate-900/20 border border-slate-800/80 rounded-xl cursor-default select-none">
                    Sau
                </span>
            @endif
        </div>

        <!-- Desktop Advanced View -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs text-slate-450">
                    Hiển thị từ
                    <span class="font-extrabold text-slate-200">{{ $paginator->firstItem() }}</span>
                    đến
                    <span class="font-extrabold text-slate-200">{{ $paginator->lastItem() }}</span>
                    trong tổng số
                    <span class="font-extrabold text-slate-200">{{ $paginator->total() }}</span>
                    kết quả
                </p>
            </div>

            <div>
                <span class="relative z-0 inline-flex shadow-sm rounded-xl gap-1.5">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="relative inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-800/60 bg-slate-900/20 text-xs font-bold text-slate-600 cursor-default select-none" aria-hidden="true">
                                <i class="fa-solid fa-chevron-left text-[10px]"></i>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="relative inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-800 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-white hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/5 transition-all active:scale-95" aria-label="@lang('pagination.previous')">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="relative inline-flex items-center px-3.5 py-2.5 text-xs font-bold text-slate-650 select-none">...</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="relative inline-flex items-center px-3.5 py-2.5 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white text-xs font-extrabold border border-indigo-500/25 shadow-lg shadow-indigo-600/10 scale-105 select-none">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-800/80 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-slate-200 hover:border-indigo-500/40 transition-all active:scale-95" aria-label="Trang {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="relative inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-800 bg-slate-900/40 text-xs font-bold text-slate-400 hover:text-white hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/5 transition-all active:scale-95" aria-label="@lang('pagination.next')">
                            <i class="fa-solid fa-chevron-right text-[10px]"></i>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="relative inline-flex items-center px-3.5 py-2.5 rounded-xl border border-slate-800/60 bg-slate-900/20 text-xs font-bold text-slate-600 cursor-default select-none" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                            </span>
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
