{{-- Small, widely spaced uppercase label above a heading. --}}
@props(['tone' => 'muted'])

@php
    $tones = [
        'muted' => 'text-ink',
        'dark' => 'text-ash',
        'light' => 'text-white',
    ];
@endphp

<p {{ $attributes->class([
    'mb-4 font-display text-[.875rem] leading-[1.45] tracking-[.29em] uppercase max-sm:text-[.78rem] max-sm:tracking-[.2em]',
    $tones[$tone],
]) }}>{{ $slot }}</p>
