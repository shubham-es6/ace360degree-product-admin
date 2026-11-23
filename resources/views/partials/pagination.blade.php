{{--
    Custom pagination links styled like Figma: Previous · 1 2 3 … 6 · Next
    Laravel passes in $paginator (the page data) and $elements
    (page numbers, with "..." strings where pages are skipped).
--}}
@if ($paginator->hasPages())
    @php
        $base = 'flex h-8 min-w-8 items-center justify-center rounded-lg border px-2.5 text-[13px] leading-[18px] font-medium';
        $normal = $base . ' border-line bg-white text-ink hover:bg-page';
        $disabled = $base . ' cursor-not-allowed border-line/50 text-subtle/60';
    @endphp

    <nav class="flex flex-wrap items-center gap-1" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="{{ $disabled }}" aria-disabled="true">Previous</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $normal }}">Previous</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-[13px] text-muted" aria-hidden="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="{{ $base }} border-primary bg-primary text-white" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="{{ $normal }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $normal }}">Next</a>
        @else
            <span class="{{ $disabled }}" aria-disabled="true">Next</span>
        @endif
    </nav>
@endif
