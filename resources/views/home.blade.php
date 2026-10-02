@extends('layouts.site')

@php
    $c = $site['content'];
    $img = $site['images'];
    $em = fn (string $text) => \App\Support\HomeContent::emphasise($text);
    $portfolio = collect([1, 2, 3])->map(fn ($n) => [$c["portfolio_{$n}_title"], $c["portfolio_{$n}_caption"], $img["portfolio_{$n}_image"]]);
    $team = [
        [$c['team_1_name'], $c['team_1_role'], $img['about_image']],
        [$c['team_2_name'], $c['team_2_role'], $img['about_image_small']],
    ];

    // Cards in a row fade in one after another.
    $stagger = ['', 'delay-120', 'delay-240', 'delay-360'];

    $btn = 'inline-block cursor-pointer border px-[30px] py-5 font-display text-[.88rem] leading-normal tracking-[.21em] uppercase transition-colors duration-400 max-sm:px-6 max-sm:py-4';
    $btnDark = $btn.' border-ink bg-ink text-white hover:bg-transparent hover:text-ink';
    $arrowBtn = 'grid size-[54px] shrink-0 cursor-pointer place-items-center rounded-full border border-line text-ink transition-colors duration-350 hover:border-ink hover:bg-ink hover:text-white max-sm:size-[46px] [&_svg]:size-5 [&_svg]:line-icon';
    $slideArrow = 'absolute top-1/2 z-1 grid size-12 -translate-y-1/2 cursor-pointer place-items-center text-white drop-shadow-[0_1px_6px_rgb(0_0_0/.45)] transition-transform duration-300 hover:scale-110 [&_svg]:size-8 [&_svg]:line-icon';
    $cardTitle = 'font-display text-[clamp(1.6rem,2.4vw,2.125rem)] leading-[1.4] tracking-normal normal-case';
    $label = 'block font-display text-[.78rem] tracking-[.2em] uppercase text-muted';
    $input = fn (string $name) => 'w-full rounded-none border-b bg-transparent py-[.55rem] text-ink transition-colors duration-300 placeholder:text-[#b9b5ad] placeholder:italic focus:border-ink focus:outline-none '
        .($errors->has($name) ? 'border-error' : 'border-line');
    $error = 'mt-[.35rem] text-[.95rem] text-error';
    $detailLink = 'border-b border-transparent transition-colors duration-300 hover:border-current';
@endphp

@section('content')
    {{-- Hero: the heading sits in the open sky at the top of a tall, full-bleed photo. --}}
    <section class="relative isolate flex min-h-svh flex-col items-center overflow-hidden bg-sand text-center md:min-h-[min(87.5vw,1400px)]" id="top">
        <img class="absolute inset-0 -z-1 size-full origin-bottom animate-hero-zoom object-cover object-bottom" src="{{ $img['hero_image'] }}" alt="A couple walking hand in hand across open hills beneath a wide pale sky" fetchpriority="high">
        {{-- Pale wash at the top keeps the dark heading readable on any photo. --}}
        <span class="absolute inset-0 -z-1 bg-[linear-gradient(180deg,var(--color-paper)_0%,rgb(249_248_246/.88)_24%,rgb(249_248_246/0)_52%)]" aria-hidden="true"></span>
        <div class="animate-fade-up px-6 pt-[clamp(10rem,15vw,13.4rem)]">
            <h1 class="mx-auto max-w-[12.5em] text-white">{{ $c['hero_title'] }}</h1>
            <p class="mt-5 font-display text-[.875rem] leading-[1.45] tracking-[.29em] text-white uppercase max-sm:text-[.78rem] max-sm:tracking-[.2em]">{{ $c['hero_tagline_1'] }} <span class="block">{{ $c['hero_tagline_2'] }}</span></p>
        </div>
    </section>

    <section class="section-y" id="intro">
        <div class="reveal page-width max-w-[900px] text-center">
            <x-eyebrow>{{ $c['intro_eyebrow'] }}</x-eyebrow>
            <h2>{{ $em($c['intro_heading']) }}</h2>
            <p class="mx-auto mt-4 max-w-[640px] text-muted">{{ $c['intro_text'] }}</p>
        </div>

        <div class="page-width mt-[clamp(3rem,6.5vw,6rem)] grid grid-cols-1 gap-[14px] sm:grid-cols-3" id="portfolio">
            @foreach ($portfolio as $index => [$title, $caption, $file])
                <a @class(['reveal group relative block overflow-hidden bg-sand text-center text-white', $stagger[$index]]) href="#gallery">
                    <img class="aspect-[443/651] w-full object-cover transition-[scale] duration-1400 group-hover:scale-[1.05] max-sm:aspect-[4/5]" src="{{ $file }}" alt="{{ $title }} portfolio" loading="lazy">
                    <span class="absolute inset-0 bg-[linear-gradient(180deg,rgb(0_0_0/0)_50%,rgb(0_0_0/.38)_100%)] transition-colors duration-700 group-hover:bg-black/15" aria-hidden="true"></span>
                    <div class="absolute inset-x-6 bottom-[9%]">
                        <h3 class="{{ $cardTitle }}">{{ $title }}</h3>
                        <p class="mt-3 font-display text-[.88rem] tracking-[.03em]">{{ $caption }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="pb-[clamp(3rem,5.4vw,5rem)]" id="about">
        <div class="reveal page-width max-w-[900px] text-center">
            <h2 class="mb-6">{{ $em($c['about_heading']) }}</h2>
            <div class="grid gap-6 text-[.875rem] leading-[1.71]">
                <p>{{ $c['about_text_1'] }}</p>
                <p>{{ $c['about_text_2'] }}</p>
                <p>{{ $c['about_signature'] }}</p>
            </div>
        </div>

        {{-- Two people cards; the sand panel starts partway down the photos. --}}
        <div class="page-width relative isolate mt-[clamp(2.5rem,4vw,3.75rem)] grid grid-cols-1 gap-12 pb-[clamp(2.5rem,3.5vw,3rem)] before:absolute before:inset-x-0 before:top-[clamp(9rem,21vw,19rem)] before:bottom-0 before:-z-1 before:bg-sand md:grid-cols-2 md:gap-4 max-md:before:top-[7rem]">
            @foreach ($team as $index => [$name, $role, $photo])
                <figure @class(['reveal text-center', $stagger[$index]])>
                    <img class="mx-auto aspect-[500/456] w-[min(500px,80%)] object-cover" src="{{ $photo }}" alt="{{ $name }}" loading="lazy">
                    <figcaption class="mt-[clamp(2rem,3.8vw,3.4rem)]">
                        <h3>{{ $name }}</h3>
                        <p class="mt-3 text-[#373737]">{{ $role }}</p>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- Gallery: one photo centred, its neighbours peeking in at the edges. --}}
    <section class="relative pt-[clamp(2rem,4vw,3rem)]" id="gallery" aria-label="Gallery">
        <div class="flex cursor-grab snap-x snap-mandatory gap-[14px] overflow-x-auto [scrollbar-width:none] [&.is-dragging]:cursor-grabbing [&.is-dragging]:snap-none [&.is-dragging]:scroll-auto [&::-webkit-scrollbar]:hidden" id="slider" tabindex="0" aria-label="Photo gallery, scroll sideways">
            @foreach ($gallery as $photo)
                <figure class="slide shrink-0 snap-center">
                    <img class="h-[clamp(280px,47vw,670px)] w-auto max-w-[86vw] object-cover select-none [-webkit-user-drag:none]" src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" loading="lazy">
                </figure>
            @endforeach
        </div>
        <button type="button" class="{{ $slideArrow }} left-[clamp(.5rem,2.4vw,2.5rem)] mt-[clamp(1rem,2vw,1.5rem)]" data-slide="prev" data-target="slider" aria-label="Previous photo">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12H4"/><path d="M10 6l-6 6 6 6"/></svg>
        </button>
        <button type="button" class="{{ $slideArrow }} right-[clamp(.5rem,2.4vw,2.5rem)] mt-[clamp(1rem,2vw,1.5rem)]" data-slide="next" data-target="slider" aria-label="Next photo">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h16"/><path d="M14 6l6 6-6 6"/></svg>
        </button>
    </section>

    <section class="pt-[clamp(4rem,7.5vw,7rem)] pb-[clamp(2rem,4vw,3rem)]">
        <figure class="reveal page-width max-w-[900px] text-center">
            <blockquote class="font-display text-[clamp(1.6rem,2.55vw,2.25rem)] leading-[1.17] text-[#373737]">"{{ $c['quote_text'] }}"</blockquote>
            <figcaption class="mt-[clamp(2.5rem,5vw,3.5rem)] font-display text-[.875rem] tracking-[.29em] uppercase">{{ $c['quote_author'] }}</figcaption>
        </figure>
    </section>

    <section class="section-y" id="services">
        <div class="page-width">
            <div class="reveal mb-[clamp(2.5rem,5vw,4rem)] text-center">
                <x-eyebrow>{{ $c['services_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['services_heading']) }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-[14px] sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($services as $index => $service)
                    <article @class(['reveal group relative isolate flex aspect-[3/4.3] flex-col justify-end overflow-hidden bg-sand text-center text-white max-sm:aspect-[4/5]', $stagger[$index] ?? ''])>
                        <img class="absolute inset-0 -z-2 size-full object-cover transition-[scale] duration-1400 group-hover:scale-[1.05]" src="{{ $service['image'] }}" alt="" loading="lazy">
                        <span class="absolute inset-0 -z-1 bg-[linear-gradient(180deg,rgb(0_0_0/0)_35%,rgb(0_0_0/.62)_100%)]" aria-hidden="true"></span>
                        <div class="px-6 pb-[clamp(1.75rem,3vw,2.5rem)]">
                            <h3 class="{{ $cardTitle }} mb-2">{{ $service['title'] }}</h3>
                            <p class="mb-4 line-clamp-4 leading-normal text-white/88">{{ $service['text'] }}</p>
                            <a class="inline-block border-b border-white/60 pb-1 font-display text-[.8rem] tracking-[.24em] uppercase transition-colors duration-300 hover:border-white" href="#contact" aria-label="Enquire about {{ $service['title'] }}">Enquire</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-y bg-sand" id="reviews">
        <div class="page-width">
            <div class="reveal mb-[clamp(2.5rem,5vw,4rem)] text-center">
                <x-eyebrow>{{ $c['reviews_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['reviews_heading']) }}</h2>
            </div>

            <div class="reveal mx-auto grid max-w-[1020px] grid-cols-2 items-center gap-[clamp(.75rem,3vw,2.5rem)] md:grid-cols-[auto_minmax(0,1fr)_auto]" id="review-slider" role="region" aria-roledescription="carousel" aria-label="Reviews from our couples">
                <button type="button" class="{{ $arrowBtn }} max-md:row-start-2 max-md:mt-2 max-md:justify-self-end" data-review="prev" aria-label="Previous review">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                </button>

                <div class="review-track flex snap-x snap-mandatory overflow-x-auto [scrollbar-width:none] max-md:col-span-full max-md:row-start-1 md:col-start-2 [&::-webkit-scrollbar]:hidden" tabindex="0">
                    @foreach ($reviews as $index => $review)
                        <figure class="review-slide relative shrink-0 basis-full snap-center bg-paper px-[clamp(1.5rem,5vw,4.5rem)] py-[clamp(2.5rem,5vw,4rem)] text-center" role="group" aria-roledescription="slide" aria-label="{{ $index + 1 }} of {{ count($reviews) }}">
                            <div class="mb-6 text-[.85rem] tracking-[.4em] text-ink" role="img" aria-label="{{ $review['rating'] }} out of 5 stars">{{ str_repeat('★', $review['rating']) }}<span class="text-line">{{ str_repeat('★', 5 - $review['rating']) }}</span></div>
                            <blockquote class="mb-8 font-display text-[clamp(1.3rem,2.2vw,1.75rem)] leading-[1.35] text-[#373737]">{{ $review['text'] }}</blockquote>
                            <figcaption>
                                @if ($review['photo'])
                                    <img class="mx-auto mb-4 size-16 rounded-full object-cover" src="{{ $review['photo'] }}" alt="" loading="lazy">
                                @endif
                                <strong class="block font-display text-[1.1rem] font-normal tracking-[.15em] uppercase">{{ $review['name'] }}</strong>
                                @if ($review['designation'])
                                    <span class="text-muted">{{ $review['designation'] }}</span>
                                @endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>

                <button type="button" class="{{ $arrowBtn }} max-md:row-start-2 max-md:mt-2" data-review="next" aria-label="Next review">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- site.js fills this with one dot button per review. --}}
                <div class="review-dots col-span-full mt-3 flex justify-center gap-[.7rem] max-md:row-start-3 [&>button]:size-[9px] [&>button]:cursor-pointer [&>button]:rounded-full [&>button]:border [&>button]:border-stone [&>button]:transition-[background-color,border-color,scale] [&>button]:duration-300 [&>button.is-active]:scale-125 [&>button.is-active]:border-ink [&>button.is-active]:bg-ink" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <section class="section-y" id="journal">
        <div class="page-width">
            <div class="reveal mb-[clamp(2.5rem,5vw,4rem)] text-center">
                <x-eyebrow>{{ $c['journal_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['journal_heading']) }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-[clamp(1.5rem,3vw,2.5rem)] sm:grid-cols-3">
                @foreach ($posts as $index => $post)
                    <article @class(['reveal group text-center', $stagger[$index] ?? ''])>
                        <a class="mb-6 block aspect-[4/3] overflow-hidden bg-sand" href="{{ $post['url'] }}" tabindex="-1" aria-hidden="true">
                            @if ($post['image'])
                                <img class="size-full object-cover transition-[scale] duration-1300 group-hover:scale-[1.05]" src="{{ $post['image'] }}" alt="" loading="lazy">
                            @endif
                        </a>
                        <p class="mb-2 font-display text-[.78rem] tracking-[.24em] uppercase text-muted">{{ $post['date'] }}</p>
                        <h3 class="{{ $cardTitle }} mb-2 text-[clamp(1.4rem,1.9vw,1.65rem)]"><a class="transition-opacity duration-300 hover:opacity-70" href="{{ $post['url'] }}">{{ $post['title'] }}</a></h3>
                        <p class="mb-4 text-muted">{{ $post['excerpt'] }}</p>
                        <a class="inline-block border-b border-ink/40 pb-1 font-display text-[.78rem] tracking-[.24em] uppercase transition-colors duration-300 hover:border-ink" href="{{ $post['url'] }}" aria-label="Read {{ $post['title'] }}">Read the story</a>
                    </article>
                @endforeach
            </div>

            {{ $blogs->links('partials.site-pagination') }}
        </div>
    </section>

    {{-- Call to action: a boxed photo banner with the text on the left. --}}
    <section class="section-y">
        <div class="page-width relative isolate flex min-h-[clamp(420px,44vw,640px)] items-center overflow-hidden bg-night">
            <img class="absolute inset-0 -z-2 size-full object-cover" src="{{ $img['cta_image'] }}" alt="A couple walking hand in hand across open hills" loading="lazy">
            <span class="absolute inset-0 -z-1 bg-[linear-gradient(90deg,rgb(0_0_0/.5)_0%,rgb(0_0_0/.2)_60%,rgb(0_0_0/.1)_100%)]" aria-hidden="true"></span>
            <div class="reveal px-[clamp(1.5rem,4vw,3.6rem)] py-12 text-white">
                <p class="mb-4 font-display text-[.875rem]">{{ $c['cta_eyebrow'] }}</p>
                <h2 class="mb-8">{{ $em($c['cta_heading']) }}</h2>
                <a class="{{ $btn }} border-white bg-white text-black hover:bg-transparent hover:text-white" href="#contact">{{ $c['cta_button'] }}</a>
            </div>
        </div>
    </section>

    <section class="section-y" id="contact">
        <div class="page-width grid max-w-[1240px] grid-cols-1 items-start gap-[clamp(2.5rem,7vw,7rem)] md:grid-cols-[minmax(0,1.1fr)_minmax(0,.8fr)]">
            <div class="reveal">
                <x-eyebrow>{{ $c['contact_eyebrow'] }}</x-eyebrow>
                <h2 class="mb-5">{{ $em($c['contact_heading']) }}</h2>
                <p class="mb-[1.1em] text-muted">{{ $c['contact_text'] }}</p>

                @if (session('enquiry_sent'))
                    <p class="mb-6 border border-ink px-5 py-4" role="status">{{ session('enquiry_sent') }}</p>
                @endif

                <form class="mt-10" id="contact-form" action="{{ route('enquiry.store') }}" method="post" novalidate>
                    @csrf
                    <div class="absolute -left-[9999px] size-px overflow-hidden" aria-hidden="true">
                        <label for="website">Leave this empty</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="grid grid-cols-1 gap-x-8 sm:grid-cols-2">
                        <div class="mb-[1.9rem]">
                            <label class="{{ $label }} mb-[.2rem]" for="name">Your names</label>
                            <input class="{{ $input('name') }}" id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" placeholder="Ananya &amp; Rohan" required>
                            @error('name')<p class="{{ $error }}" id="name-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-[1.9rem]">
                            <label class="{{ $label }} mb-[.2rem]" for="email">Email</label>
                            <input class="{{ $input('email') }}" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" placeholder="you@example.com" required>
                            @error('email')<p class="{{ $error }}" id="email-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-x-8 sm:grid-cols-2">
                        <div class="mb-[1.9rem]">
                            <label class="{{ $label }} mb-[.2rem]" for="phone">Phone</label>
                            <input class="{{ $input('phone') }}" id="phone" name="phone" type="tel" value="{{ old('phone') }}" autocomplete="tel">
                            @error('phone')<p class="{{ $error }}" id="phone-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="mb-[1.9rem]">
                            <label class="{{ $label }} mb-[.2rem]" for="date">Event date</label>
                            <input class="{{ $input('date') }}" id="date" name="date" type="date" value="{{ old('date') }}" min="{{ date('Y-m-d') }}">
                            @error('date')<p class="{{ $error }}" id="date-error">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mb-[1.9rem]">
                        <label class="{{ $label }} mb-[.2rem]" for="venue">Venue or city</label>
                        <input class="{{ $input('venue') }}" id="venue" name="venue" type="text" value="{{ old('venue') }}">
                        @error('venue')<p class="{{ $error }}" id="venue-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="mb-[1.9rem]">
                        <label class="{{ $label }} mb-[.2rem]" for="message">Tell us about your plans</label>
                        <textarea class="{{ $input('message') }} resize-y" id="message" name="message" rows="4">{{ old('message') }}</textarea>
                        @error('message')<p class="{{ $error }}" id="message-error">{{ $message }}</p>@enderror
                    </div>
                    @error('website')<p class="{{ $error }}">{{ $message }}</p>@enderror
                    <button type="submit" class="{{ $btnDark }}">Send enquiry</button>
                </form>
            </div>

            <aside class="reveal">
                <img class="aspect-[4/5] w-full object-cover max-md:aspect-[16/10]" src="{{ $img['contact_image'] }}" alt="A bouquet tied to a draped wedding arch" loading="lazy">
                <dl class="mt-9 grid gap-[1.3rem]">
                    @if ($site['email'])
                        <div>
                            <dt class="{{ $label }}">Email</dt>
                            <dd class="mt-[.15rem] text-[1.3rem]"><a class="{{ $detailLink }}" href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></dd>
                        </div>
                    @endif
                    @if ($site['phone'] || $site['phoneAlt'])
                        <div>
                            <dt class="{{ $label }}">Phone</dt>
                            @foreach (array_filter([$site['phone'], $site['phoneAlt']]) as $phone)
                                <dd class="mt-[.15rem] text-[1.3rem]"><a class="{{ $detailLink }}" href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}">{{ $phone }}</a></dd>
                            @endforeach
                        </div>
                    @endif
                    @if ($site['whatsapp'])
                        <div>
                            <dt class="{{ $label }}">WhatsApp</dt>
                            <dd class="mt-[.15rem] text-[1.3rem]"><a class="{{ $detailLink }}" href="https://wa.me/{{ preg_replace('/\D/', '', $site['whatsapp']) }}" target="_blank" rel="noopener noreferrer">{{ $site['whatsapp'] }}</a></dd>
                        </div>
                    @endif
                    @if ($site['address'])
                        <div>
                            <dt class="{{ $label }}">Studio</dt>
                            <dd class="mt-[.15rem] text-[1.3rem] whitespace-pre-line">{{ $site['address'] }}</dd>
                        </div>
                    @endif
                    @if ($site['hours'])
                        <div>
                            <dt class="{{ $label }}">Hours</dt>
                            <dd class="mt-[.15rem] text-[1.3rem]">{{ $site['hours'] }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($site['mapSrc'])
                    <div class="mt-6">
                        <iframe class="block h-60 w-full border-0" src="{{ $site['mapSrc'] }}" title="Map to our studio" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                @endif
            </aside>
        </div>
    </section>
@endsection
