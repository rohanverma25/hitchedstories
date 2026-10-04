@php
    $home = route('home');
    $links = [
        'portfolio' => 'Portfolio',
        'about' => 'About',
        'services' => 'Services',
        'gallery' => 'Gallery',
        'journal' => 'Journal',
        'contact' => 'Contact',
    ];
    $socialLabels = \App\Http\Requests\Admin\SettingsRequest::SOCIAL;
    $underlineLink = 'relative font-display text-[.8rem] tracking-[.24em] uppercase after:absolute after:-bottom-1 after:left-0 after:h-px after:w-full after:origin-right after:scale-x-0 after:bg-current after:transition-transform after:duration-400 hover:after:origin-left hover:after:scale-x-100';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site['name'].' · '.$site['tagline'])</title>
    <meta name="description" content="@yield('description', $site['footerLine'])">
    @stack('meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,700;1,400&family=Forum&display=swap">
    @vite('resources/css/app.css')
    @stack('styles')
    <script>document.documentElement.classList.add('js');</script>
    {!! $site['headScripts'] !!}
</head>
<body class="@yield('body-class')">
    <a class="absolute top-0 -left-[999px] z-100 bg-night px-4 py-[.6rem] text-white focus:left-0" href="#main">Skip to content</a>

    <header id="site-header" class="fixed inset-x-0 top-0 z-50 grid h-[118px] grid-cols-[1fr_auto] items-center gap-8 px-[clamp(1rem,2.4vw,2.5rem)] text-ink transition-[background-color,color,height,box-shadow] duration-400 max-md:h-[88px] md:grid-cols-[1fr_auto_1fr] solid:h-[72px] solid:bg-black solid:text-white solid:shadow-[0_1px_0_rgb(255_255_255/.08)] menu-open:bg-transparent menu-open:text-ink menu-open:shadow-none">
        <nav class="flex gap-[clamp(1.5rem,3vw,3.1rem)] max-md:hidden" aria-label="Primary">
            @foreach (array_slice($links, 0, 3) as $anchor => $label)
                <a class="{{ $underlineLink }}" href="{{ $home }}#{{ $anchor }}" style="color:#fff">{{ $label }}</a>
            @endforeach
        </nav>

        {{-- The logo sits on a small square wine-coloured tile. --}}
        <a class="grid size-[84px] place-items-center leading-none text-white transition-[width,height] duration-400 max-md:size-[60px] max-md:p-[6px] solid:size-[52px] solid:p-[5px]" href="{{ $home }}#top" aria-label="{{ $site['name'] }} home" style="width: 90px;">
            @if ($site['logo'])
                <img class="size-full object-contain" src="{{ $site['logo'] }}" alt="{{ $site['name'] }}">
            @else
                <span class="font-display text-[2.4rem] transition-[font-size] duration-400 max-md:text-[1.7rem] solid:text-[1.5rem]">{{ mb_substr($site['name'], 0, 1) }}</span>
            @endif
        </a>

        <nav class="flex justify-end gap-[clamp(1.5rem,3vw,3.1rem)] max-md:hidden" aria-label="Secondary">
            @foreach (array_slice($links, 3) as $anchor => $label)
                <a class="{{ $underlineLink }}" href="{{ $home }}#{{ $anchor }}" style="color:#fff">{{ $label }}</a>
            @endforeach
        </nav>

        <button class="menu-toggle group size-11 cursor-pointer justify-self-end md:hidden" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open menu">
            <span class="mx-auto my-[7px] block h-px w-[26px] bg-white transition-transform duration-350 group-aria-expanded:translate-y-1 group-aria-expanded:rotate-45"></span>
            <span class="mx-auto my-[7px] block h-px w-[26px] bg-white transition-transform duration-350 group-aria-expanded:-translate-y-1 group-aria-expanded:-rotate-45"></span>
        </button>
    </header>

    <div class="fixed inset-0 z-40 grid place-items-center bg-paper" id="mobile-menu" hidden>
        <nav class="grid gap-[1.9rem] text-center" aria-label="Mobile">
            @foreach ($links as $anchor => $label)
                <a class="font-display text-[1.7rem] tracking-[.12em] uppercase text-ink" href="{{ $home }}#{{ $anchor }}">{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    <main id="main">
        @yield('content')
    </main>

    <footer class="page-width grid grid-cols-1 items-center justify-items-center gap-5 pt-[clamp(2.5rem,5vw,4.5rem)] pb-10 text-center md:grid-cols-[1fr_auto_1fr] md:justify-items-stretch md:text-left">
        <p class="text-[1.125rem] text-ink">{{ $site['footerText'] }}</p>

        <div class="flex gap-4">
            @foreach ($site['social'] as $key => $url)
                <a class="text-stone transition-colors duration-300 hover:text-ink [&_svg]:size-[18px] [&_svg]:line-icon" href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabels[$key] }}">
                    @include('partials.social-icon', ['name' => $key])
                </a>
            @endforeach
        </div>

        <a class="justify-self-center text-ink transition-transform duration-400 hover:-translate-y-1 md:justify-self-end [&_svg]:size-6 [&_svg]:line-icon" href="{{ $home }}#top" aria-label="Back to top">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21V3"/><path d="M5 10l7-7 7 7"/></svg>
        </a>

        <p class="text-[.95rem] text-muted md:col-span-3">
            Designed and developed by <a class="text-ink underline decoration-line underline-offset-4 transition-colors duration-300 hover:decoration-ink" href="https://codebasecoders.com" target="_blank" rel="noopener">CodeBase Coders</a>
        </p>
    </footer>

    <script src="{{ asset('js/site.js') }}" defer></script>
    {!! $site['bodyScripts'] !!}
</body>
</html>
