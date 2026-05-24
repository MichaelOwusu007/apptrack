@if ($paginator->hasPages())
<nav class="flex items-center justify-between" aria-label="Pagination">
    <p class="text-xs text-slate-500">
        Showing <span class="font-medium text-slate-300">{{ $paginator->firstItem() }}</span>–<span class="font-medium text-slate-300">{{ $paginator->lastItem() }}</span>
        of <span class="font-medium text-slate-300">{{ $paginator->total() }}</span> results
    </p>
    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-1.5 rounded-lg text-xs text-slate-700 cursor-not-allowed">← Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition-colors">← Prev</a>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1.5 text-xs text-slate-600">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 rounded-lg text-xs bg-indigo-500/20 text-indigo-400 font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-xs text-slate-400 hover:text-white hover:bg-white/5 transition-colors">Next →</a>
        @else
            <span class="px-3 py-1.5 rounded-lg text-xs text-slate-700 cursor-not-allowed">Next →</span>
        @endif
    </div>
</nav>
@endif
