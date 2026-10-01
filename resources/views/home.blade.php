@extends('layouts.site')

@php
    $c = $site['content'];
    $img = $site['images'];
    $em = fn (string $text) => \App\Support\HomeContent::emphasise($text);
    $portfolio = collect([1, 2, 3])->map(fn ($n) => [$c["portfolio_{$n}_title"], $c["portfolio_{$n}_caption"], $img["portfolio_{$n}_image"]]);

    // Cards in a row fade in one after another.
    $stagger = ['', 'delay-120', 'delay-240', 'delay-360'];

    $btn = 'relative z-0 inline-block overflow-hidden border px-[2.4rem] py-4 font-body text-[.8rem] font-semibold tracking-[.28em] uppercase cursor-pointer transition-[color,border-color] duration-450 before:absolute before:inset-0 before:-z-1 before:origin-bottom before:scale-y-0 before:transition-[scale] before:duration-450 hover:before:scale-y-100 max-sm:px-[1.8rem] max-sm:py-[.95rem]';
    $arrowBtn = 'grid size-[54px] shrink-0 cursor-pointer place-items-center rounded-full border border-line text-ink transition-colors duration-350 hover:border-ink hover:bg-ink hover:text-white max-sm:size-[46px] [&_svg]:size-5 [&_svg]:line-icon';
    $ornament = 'mx-auto mt-10 block h-[70px] w-px bg-line';
    $label = 'block text-[.74rem] font-semibold tracking-[.26em] uppercase text-muted';
    $input = fn (string $name) => 'w-full rounded-none border-b bg-transparent py-[.55rem] text-ink transition-colors duration-300 placeholder:text-[#b9b5ad] placeholder:italic focus:border-gold focus:outline-none '
        .($errors->has($name) ? 'border-error' : 'border-line');
    $error = 'mt-[.35rem] text-[.9rem] text-error';
    $detailLink = 'border-b border-transparent transition-colors duration-300 hover:border-current';
@endphp

@section('content')
    <section class="relative grid min-h-svh items-start justify-items-center overflow-hidden bg-sand text-center after:absolute after:inset-0 after:bg-[linear-gradient(180deg,rgb(0_0_0/92%)_0%,rgb(0_0_0/72%)_38%,rgb(0_0_0/20%)_62%,rgb(0_0_0/0%)_78%)]" id="top">
        <img class="absolute inset-0 size-full origin-[50%_100%] animate-hero-zoom object-cover object-[50%_100%]" src="{{ $img['hero_image'] }}" alt="A couple walking hand in hand across open hills beneath a wide pale sky" fetchpriority="high">
        <div class="relative z-2 animate-fade-up px-6 pt-[clamp(10.5rem,25vh,15rem)] pb-16 max-md:pt-[clamp(8.5rem,22vh,12rem)]">
            <h1 class="mx-auto mb-8 max-w-[13em] text-[clamp(2.1rem,5.2vw,4.3rem)] leading-[1.22] tracking-[.13em] text-white max-sm:tracking-[.09em]">{{ $site['name'] }} {{ $c['hero_title'] }} <span class="whitespace-nowrap">{{ $c['hero_title_accent'] }}</span></h1>
            <p class="text-[clamp(.78rem,1.1vw,.95rem)] leading-loose font-semibold tracking-[.36em] uppercase text-white/85 max-sm:tracking-[.26em]">{{ $c['hero_tagline_1'] }} <span class="block">{{ $c['hero_tagline_2'] }}</span></p>
        </div>
    </section>

    <section class="section-y" id="intro">
        <div class="reveal page-width max-w-[780px] text-center">
            <x-eyebrow>{{ $c['intro_eyebrow'] }}</x-eyebrow>
            <h2>{{ $em($c['intro_heading']) }}</h2>
            <p class="mt-6 text-[1.4rem] font-light">{{ $c['intro_text'] }}</p>
            <span class="{{ $ornament }}" aria-hidden="true"></span>
        </div>
    </section>

    <section class="pt-[clamp(1.5rem,4vw,3rem)] pb-[clamp(4.5rem,10vw,8.5rem)]" id="portfolio">
        <div class="page-width">
            <div class="reveal mb-[clamp(2.5rem,5vw,4rem)] text-center">
                <x-eyebrow>{{ $c['portfolio_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['portfolio_heading']) }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-11 sm:grid-cols-3 sm:gap-[clamp(1.25rem,3vw,2.75rem)]">
                @foreach ($portfolio as $index => [$title, $caption, $file])
                    <a @class(['reveal group block text-center', $stagger[$index], 'sm:mt-[clamp(0rem,5vw,4.5rem)]' => $index === 1]) href="#gallery">
                        <div class="relative mb-6 overflow-hidden bg-sand after:absolute after:inset-[.9rem] after:scale-[1.04] after:border after:border-white/60 after:opacity-0 after:transition-[opacity,scale] after:duration-700 group-hover:after:scale-100 group-hover:after:opacity-100">
                            <img class="aspect-[3/4] w-full object-cover transition-[scale,filter] duration-1400 group-hover:scale-[1.07] group-hover:brightness-[.82]" src="{{ $file }}" alt="{{ $title }} portfolio" loading="lazy">
                            <span class="absolute top-[1.4rem] left-[1.6rem] z-1 font-display text-[1.3rem] text-white [text-shadow:0_1px_12px_rgb(0_0_0/.35)]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="absolute top-1/2 left-1/2 z-1 grid size-24 -translate-1/2 scale-60 place-items-center rounded-full bg-paper/94 text-[.7rem] font-semibold tracking-[.28em] uppercase text-ink opacity-0 transition-[opacity,scale] duration-600 group-hover:scale-100 group-hover:opacity-100">View</span>
                        </div>
                        <h3 class="transition-colors duration-300 group-hover:text-gold">{{ $title }}</h3>
                        <p class="mt-[.35rem] text-muted italic">{{ $caption }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-y bg-sand" id="about">
        <div class="page-width grid grid-cols-1 items-center gap-[clamp(2.5rem,7vw,7rem)] md:grid-cols-[minmax(0,.95fr)_minmax(0,1fr)]">
            <div class="reveal relative pr-[14%] pb-[14%] before:absolute before:inset-[1.5rem_calc(14%_-_1.5rem)_calc(14%_-_1.5rem)_1.5rem] before:border before:border-gold before:opacity-55">
                <img class="relative aspect-[4/5] w-full object-cover" src="{{ $img['about_image'] }}" alt="A bride and groom sharing a kiss beneath her veil" loading="lazy">
                <img class="absolute right-0 bottom-0 aspect-[4/5] w-[46%] border-10 border-sand object-cover shadow-[0_24px_50px_rgb(43_42_39/.16)] max-sm:border-6" src="{{ $img['about_image_small'] }}" alt="Two hands resting together, wedding rings on show" loading="lazy">
            </div>
            <div class="reveal">
                <x-eyebrow lines="start">{{ $c['about_eyebrow'] }}</x-eyebrow>
                <h2 class="mb-[1.8rem]">{{ $em($c['about_heading']) }}</h2>
                <p class="mb-[1.1em]">{{ $c['about_text_1'] }}</p>
                <p class="mb-[1.1em]">{{ $c['about_text_2'] }}</p>
                <p class="mt-[1.6rem] mb-[1.1em] text-[1.5rem] font-light text-gold italic">{{ $c['about_signature'] }}</p>
                <a class="{{ $btn }} mt-4 border-ink text-ink before:bg-ink hover:text-white" href="#contact">{{ $c['about_button'] }}</a>
            </div>
        </div>
    </section>

    <section class="section-y bg-night text-mist" id="services">
        <div class="page-width">
            <div class="reveal mb-[clamp(2.5rem,5vw,4rem)] text-center">
                <x-eyebrow tone="dark">{{ $c['services_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['services_heading']) }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-[clamp(1rem,1.8vw,1.75rem)] sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($services as $index => $service)
                    <div @class(['reveal', $stagger[$index] ?? ''])>
                        <article class="group relative isolate flex aspect-[3/4.3] flex-col justify-end overflow-hidden bg-[#1d1c1a] shadow-[0_24px_50px_rgb(0_0_0/.28)] transition-[translate,box-shadow] duration-700 before:absolute before:inset-0 before:-z-1 before:bg-[linear-gradient(180deg,rgb(20_19_17/.35)_0%,rgb(20_19_17/0)_28%,rgb(20_19_17/.55)_55%,rgb(20_19_17/.92)_100%)] after:pointer-events-none after:absolute after:inset-[.85rem] after:scale-[1.04] after:border after:border-white/50 after:opacity-0 after:transition-[opacity,scale] after:duration-800 focus-within:after:scale-100 focus-within:after:opacity-100 hover:-translate-y-2 hover:shadow-[0_34px_64px_rgb(0_0_0/.4)] hover:after:scale-100 hover:after:opacity-100 max-sm:aspect-[4/5] motion-reduce:hover:translate-y-0">
                            <div class="absolute inset-0 -z-2">
                                <img class="size-full object-cover brightness-90 saturate-[.92] transition-[scale,filter] duration-1600 group-hover:scale-[1.08] group-hover:brightness-100 group-hover:saturate-[1.05]" src="{{ $service['image'] }}" alt="" loading="lazy">
                            </div>
                            <span class="absolute top-[1.2rem] left-[1.4rem] font-display text-2xl leading-none text-white [text-shadow:0_1px_14px_rgb(0_0_0/.45)] after:mt-[.55rem] after:block after:h-px after:w-[26px] after:bg-gold after:transition-[width] after:duration-600 group-hover:after:w-[54px]">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="px-[1.6rem] pt-[1.6rem] pb-[1.7rem] text-white">
                                <h3 class="mb-[.7rem] text-[clamp(1.5rem,2vw,1.85rem)] [text-shadow:0_1px_16px_rgb(0_0_0/.4)]">{{ $service['title'] }}</h3>
                                <p class="mb-[1.1rem] line-clamp-4 text-[1.1rem] leading-normal text-white/84">{{ $service['text'] }}</p>
                                <a class="inline-flex items-center gap-[.6rem] text-[.78rem] font-semibold tracking-[.24em] uppercase text-cream transition-[gap,color] duration-500 group-hover:gap-4 group-hover:text-white focus-visible:gap-4 focus-visible:text-white [&_svg]:size-[18px] [&_svg]:line-icon" href="#contact" aria-label="Enquire about {{ $service['title'] }}">
                                    Enquire
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12h16"/><path d="M14 6l6 6-6 6"/></svg>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="pt-[clamp(4.5rem,10vw,8.5rem)] pb-[clamp(3rem,7vw,6rem)]" id="gallery">
        <div class="reveal page-width mb-[clamp(2rem,4vw,3.25rem)] flex items-end justify-between gap-8">
            <div>
                <x-eyebrow lines="start">{{ $c['gallery_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['gallery_heading']) }}</h2>
            </div>
            <div class="flex gap-3">
                <button type="button" class="{{ $arrowBtn }}" data-slide="prev" data-target="slider" aria-label="Previous photos">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 5l-7 7 7 7"/></svg>
                </button>
                <button type="button" class="{{ $arrowBtn }}" data-slide="next" data-target="slider" aria-label="Next photos">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>

        <div class="flex cursor-grab snap-x snap-mandatory scroll-px-[max(1.5rem,(100vw_-_1240px)/2)] gap-[clamp(.9rem,1.6vw,1.5rem)] overflow-x-auto px-[max(1.5rem,(100vw_-_1240px)/2)] [scrollbar-width:none] [&.is-dragging]:cursor-grabbing [&.is-dragging]:snap-none [&.is-dragging]:scroll-auto [&::-webkit-scrollbar]:hidden" id="slider" tabindex="0" aria-label="Photo gallery, scroll sideways">
            @foreach ($gallery as $photo)
                <figure class="slide group w-[clamp(240px,27vw,400px)] shrink-0 snap-start overflow-hidden even:mt-[clamp(1.5rem,4vw,3.5rem)] max-sm:w-[72vw]">
                    <img class="aspect-[4/5] w-full object-cover transition-[scale] duration-1200 select-none group-hover:scale-[1.04] [-webkit-user-drag:none]" src="{{ $photo['src'] }}" alt="{{ $photo['alt'] }}" loading="lazy">
                </figure>
            @endforeach
        </div>
    </section>

    <section class="pt-[clamp(1.5rem,4vw,3rem)] pb-[clamp(4.5rem,10vw,8.5rem)]">
        <div class="reveal page-width max-w-[780px] text-center">
            <span class="block h-[3.2rem] font-display text-[6rem] leading-none text-gold opacity-60" aria-hidden="true">“</span>
            <blockquote class="text-[clamp(1.8rem,3.6vw,2.9rem)] leading-[1.3] font-light italic">{{ $c['quote_text'] }}</blockquote>
            <span class="{{ $ornament }}" aria-hidden="true"></span>
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

                <div class="review-track flex snap-x snap-mandatory overflow-x-auto [scrollbar-width:none] max-md:col-span-full max-md:row-start-1 [&::-webkit-scrollbar]:hidden" tabindex="0">
                    @foreach ($reviews as $index => $review)
                        <figure class="review-slide relative shrink-0 basis-full snap-center bg-paper px-[clamp(1.5rem,5vw,4.5rem)] py-[clamp(2.5rem,5vw,4rem)] text-center" role="group" aria-roledescription="slide" aria-label="{{ $index + 1 }} of {{ count($reviews) }}">
                            <span class="block h-[2.6rem] font-display text-[5rem] leading-none text-gold opacity-60" aria-hidden="true">“</span>
                            <div class="mb-6 text-[.85rem] tracking-[.4em] text-gold" role="img" aria-label="{{ $review['rating'] }} out of 5 stars">{{ str_repeat('★', $review['rating']) }}<span class="text-line">{{ str_repeat('★', 5 - $review['rating']) }}</span></div>
                            <blockquote class="mb-8 text-[clamp(1.35rem,2.4vw,1.9rem)] leading-normal font-light italic">{{ $review['text'] }}</blockquote>
                            <figcaption>
                                @if ($review['photo'])
                                    <img class="mx-auto mb-4 size-16 rounded-full object-cover" src="{{ $review['photo'] }}" alt="" loading="lazy">
                                @endif
                                <strong class="block font-display text-[1.4rem] font-normal">{{ $review['name'] }}</strong>
                                @if ($review['designation'])
                                    <span class="text-[.74rem] font-semibold tracking-[.28em] uppercase text-muted">{{ $review['designation'] }}</span>
                                @endif
                            </figcaption>
                        </figure>
                    @endforeach
                </div>

                <button type="button" class="{{ $arrowBtn }} max-md:row-start-2 max-md:mt-2" data-review="next" aria-label="Next review">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- site.js fills this with one dot button per review. --}}
                <div class="review-dots col-span-full mt-3 flex justify-center gap-[.7rem] max-md:row-start-3 [&>button]:size-[9px] [&>button]:cursor-pointer [&>button]:rounded-full [&>button]:border [&>button]:border-stone [&>button]:transition-[background-color,border-color,scale] [&>button]:duration-300 [&>button.is-active]:scale-125 [&>button.is-active]:border-gold [&>button.is-active]:bg-gold" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <section class="section-y" id="journal">
        <div class="page-width">
            <div class="reveal mb-[clamp(2.5rem,5vw,4rem)] text-center">
                <x-eyebrow>{{ $c['journal_eyebrow'] }}</x-eyebrow>
                <h2>{{ $em($c['journal_heading']) }}</h2>
            </div>

            <div class="grid grid-cols-1 gap-[clamp(1.5rem,3.5vw,3rem)] sm:grid-cols-3">
                @foreach ($posts as $index => $post)
                    <article @class(['reveal group', $stagger[$index] ?? ''])>
                        <a class="mb-[1.6rem] block aspect-[4/3] overflow-hidden bg-sand" href="{{ $post['url'] }}" tabindex="-1" aria-hidden="true">
                            @if ($post['image'])
                                <img class="size-full object-cover transition-[scale] duration-1300 group-hover:scale-[1.06]" src="{{ $post['image'] }}" alt="" loading="lazy">
                            @endif
                        </a>
                        <p class="mb-[.6rem] text-[.72rem] font-semibold tracking-[.28em] uppercase text-gold">{{ $post['date'] }}</p>
                        <h3 class="mb-[.8rem] text-[1.7rem] leading-[1.2]"><a class="transition-colors duration-300 hover:text-gold" href="{{ $post['url'] }}">{{ $post['title'] }}</a></h3>
                        <p class="mb-[1.1em] text-[1.12rem] text-muted">{{ $post['excerpt'] }}</p>
                        <a class="inline-block bg-[linear-gradient(currentColor,currentColor)] bg-[length:100%_1px] bg-left-bottom bg-no-repeat pb-[.3rem] text-[.76rem] font-semibold tracking-[.26em] uppercase transition-[background-size,color] duration-450 hover:bg-[length:40%_1px] hover:text-gold" href="{{ $post['url'] }}" aria-label="Read {{ $post['title'] }}">Read the story</a>
                    </article>
                @endforeach
            </div>

            {{ $blogs->links('partials.site-pagination') }}
        </div>
    </section>

    <section class="relative grid min-h-[min(78vh,680px)] place-items-center overflow-hidden px-6 py-20 text-center text-white before:pointer-events-none before:absolute before:inset-[1.1rem] before:z-1 before:border before:border-white/28 after:absolute after:inset-0 after:bg-[linear-gradient(180deg,rgb(30_28_25/.55)_0%,rgb(30_28_25/.25)_40%,rgb(30_28_25/.6)_100%)] max-sm:before:inset-[.6rem]">
        <img class="absolute inset-0 size-full object-cover" src="{{ $img['cta_image'] }}" alt="A couple standing close on a windswept beach, the bride's veil trailing behind her" loading="lazy">
        <div class="reveal relative z-2">
            <x-eyebrow tone="light">{{ $c['cta_eyebrow'] }}</x-eyebrow>
            <h2 class="mb-[2.4rem] text-[clamp(2.3rem,5vw,4.2rem)] [&_em]:text-cream">{{ $em($c['cta_heading']) }}</h2>
            <a class="{{ $btn }} border-white/75 text-white before:bg-white hover:border-white hover:text-ink" href="#contact">{{ $c['cta_button'] }}</a>
        </div>
    </section>

    <section class="section-y" id="contact">
        <div class="page-width grid grid-cols-1 items-start gap-[clamp(2.5rem,7vw,7rem)] md:grid-cols-[minmax(0,1.1fr)_minmax(0,.8fr)]">
            <div class="reveal">
                <x-eyebrow lines="start">{{ $c['contact_eyebrow'] }}</x-eyebrow>
                <h2 class="mb-[1.8rem]">{{ $em($c['contact_heading']) }}</h2>
                <p class="mb-[1.1em]">{{ $c['contact_text'] }}</p>

                @if (session('enquiry_sent'))
                    <p class="mb-6 border border-gold px-5 py-4 text-[1.1rem]" role="status">{{ session('enquiry_sent') }}</p>
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
                            <label class="{{ $label }} mb-[.2rem]" for="date">Wedding date</label>
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
                    <button type="submit" class="{{ $btn }} border-transparent bg-gold text-white before:bg-ink">Send enquiry</button>
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
