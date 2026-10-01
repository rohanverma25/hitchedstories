@php
    $circle = 'grid size-[50px] place-items-center rounded-full border font-display text-[1.15rem] transition-colors duration-350 max-sm:size-[42px] max-sm:text-base';
    $arrow = $circle.' border-line [&_svg]:size-[18px] [&_svg]:line-icon';
    $arrowLink = $arrow.' hover:border-ink hover:bg-ink hover:text-white';
@endphp

@if ($paginator->hasPages())
    <nav class="mt-[clamp(2.5rem,5vw,4rem)] flex items-center justify-center gap-[.6rem]" aria-label="Journal pages">
        @if ($paginator->onFirstPage())
            <span class="{{ $arrow }} opacity-35" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg>
            </span>
        @else
            <a class="{{ $arrowLink }}" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-muted" aria-hidden="true">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="{{ $circle }} border-transparent bg-ink text-white" aria-current="page">{{ str_pad($page, 2, '0', STR_PAD_LEFT) }}</span>
                    @else
                        <a class="{{ $circle }} border-transparent hover:border-gold hover:text-gold" href="{{ $url }}" aria-label="Page {{ $page }}">{{ str_pad($page, 2, '0', STR_PAD_LEFT) }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="{{ $arrowLink }}" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <span class="{{ $arrow }} opacity-35" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg>
            </span>
        @endif
    </nav>
@endif
