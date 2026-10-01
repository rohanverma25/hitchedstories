{{-- Small spaced-out label above a heading, with a short gold rule on one or both sides. --}}
@props(['lines' => 'both', 'tone' => 'muted'])

@php
    $tones = [
        'muted' => 'text-muted [--rule:var(--color-gold)]',
        'dark' => 'text-ash [--rule:var(--color-gold)]',
        'light' => 'text-white/90 [--rule:rgb(255_255_255/.7)]',
    ];
    $rules = [
        'both' => 'before:h-px before:w-[34px] before:bg-(--rule) after:h-px after:w-[34px] after:bg-(--rule) max-sm:before:w-5 max-sm:after:w-5',
        'start' => 'before:h-px before:w-[34px] before:bg-(--rule) max-sm:before:w-5',
    ];
@endphp

<p {{ $attributes->class([
    'mb-[1.1rem] inline-flex items-center gap-[.9rem] text-[.8rem] font-semibold tracking-[.32em] uppercase max-sm:gap-[.6rem] max-sm:text-[.72rem] max-sm:tracking-[.22em]',
    $tones[$tone],
    $rules[$lines],
]) }}>{{ $slot }}</p>
