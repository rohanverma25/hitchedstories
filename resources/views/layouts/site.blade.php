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
    $underlineLink = 'relative text-[.8rem] font-medium tracking-[.26em] uppercase text-white after:absolute after:-bottom-1.5 after:left-0 after:h-px after:w-full after:origin-right after:scale-x-0 after:bg-white after:transition-transform after:duration-400 hover:after:origin-left hover:after:scale-x-100';
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Forum&display=swap">
    @vite('resources/css/app.css')
    @stack('styles')
    <script>document.documentElement.classList.add('js');</script>
    {!! $site['headScripts'] !!}
</head>
<body class="@yield('body-class')">
    <a class="absolute top-0 -left-[999px] z-100 bg-night px-4 py-[.6rem] text-white focus:left-0" href="#main">Skip to content</a>

    <header id="site-header" class="fixed inset-x-0 top-0 z-50 grid h-24 grid-cols-[1fr_auto] items-center gap-8 px-[clamp(1.25rem,4vw,3.5rem)] text-white transition-[background-color,color,height,box-shadow] duration-400 max-md:h-[78px] md:grid-cols-[1fr_auto_1fr] solid:h-18 solid:bg-black/96 solid:shadow-[0_1px_0_#000] solid:backdrop-blur-sm menu-open:bg-transparent menu-open:text-ink menu-open:shadow-none">
        <nav class="flex gap-[clamp(1.5rem,3vw,3.1rem)] max-md:hidden" aria-label="Primary">
            @foreach (array_slice($links, 0, 3) as $anchor => $label)
                <a class="{{ $underlineLink }}" href="{{ $home }}#{{ $anchor }}">{{ $label }}</a>
            @endforeach
        </nav>

        <a class="grid h-[84px] w-[100px] place-content-center justify-items-center gap-[.2rem] leading-none text-[#f4efe6] transition-[width,height,margin] duration-400 has-[img]:place-content-stretch max-md:mt-[.8rem] max-md:size-[60px] solid:mt-0 solid:h-[52px] solid:w-[90px]" href="{{ $home }}#top" aria-label="{{ $site['name'] }} home">
            @if ($site['logo'])
                <img class="size-full object-contain" src="{{ $site['logo'] }}" alt="{{ $site['name'] }}">
            @else
                <span class="font-display text-[2.5rem] transition-[font-size] duration-400 max-md:text-[1.8rem] solid:text-[1.7rem]">{{ mb_substr($site['name'], 0, 1) }}</span>
                <span class="pl-[.3em] text-[.5rem] font-semibold tracking-[.3em] uppercase text-gold max-md:text-[.4rem] solid:hidden">{{ $site['name'] }}</span>
            @endif
        </a>

        <nav class="flex justify-end gap-[clamp(1.5rem,3vw,3.1rem)] max-md:hidden" aria-label="Secondary">
            @foreach (array_slice($links, 3) as $anchor => $label)
                <a class="{{ $underlineLink }}" href="{{ $home }}#{{ $anchor }}">{{ $label }}</a>
            @endforeach
        </nav>

        <button class="menu-toggle group size-11 cursor-pointer justify-self-end md:hidden" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open menu">
            <span class="mx-auto my-[7px] block h-px w-[26px] bg-current transition-transform duration-350 group-aria-expanded:translate-y-1 group-aria-expanded:rotate-45"></span>
            <span class="mx-auto my-[7px] block h-px w-[26px] bg-current transition-transform duration-350 group-aria-expanded:-translate-y-1 group-aria-expanded:-rotate-45"></span>
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

    <footer class="grid justify-items-center gap-8 bg-night px-6 pt-[clamp(3.5rem,7vw,5.5rem)] pb-10 text-center text-mist">
        <a class="grid justify-items-center leading-none" href="{{ $home }}#top">
            @if ($site['logo'])
                <img class="max-h-20 max-w-40 object-contain" src="{{ $site['logo'] }}" alt="{{ $site['name'] }}">
            @else
                <span class="pl-[.22em] font-display text-[2.5rem] tracking-[.22em] uppercase">{{ $site['name'] }}</span>
                <span class="mt-[.4rem] text-[.62rem] font-semibold tracking-[.34em] uppercase opacity-85">{{ $site['tagline'] }}</span>
            @endif
        </a>

        <p class="text-[1.3rem] font-light whitespace-pre-line text-ash italic">{{ $site['footerLine'] }}</p>

        <nav class="flex flex-wrap justify-center gap-x-[2.6rem] gap-y-[1.2rem]" aria-label="Footer">
            @foreach ($links as $anchor => $label)
                <a class="{{ $underlineLink }}" href="{{ $home }}#{{ $anchor }}">{{ $label }}</a>
            @endforeach
        </nav>

        @if ($site['social'])
            <div class="flex gap-[.9rem]">
                @foreach ($site['social'] as $key => $url)
                    <a class="grid size-[46px] place-items-center rounded-full border border-white/14 transition-colors duration-350 hover:border-gold hover:bg-gold hover:text-white [&_svg]:size-5 [&_svg]:line-icon" href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabels[$key] }}">
                        @include('partials.social-icon', ['name' => $key])
                    </a>
                @endforeach
            </div>
        @endif

        <p class="w-[min(1240px,100%)] border-t border-white/14 pt-8 text-[.95rem] text-ash">{{ $site['footerText'] }}</p>
    </footer>

    <script src="{{ asset('js/site.js') }}" defer></script>
    {!! $site['bodyScripts'] !!}
</body>
</html>
