@extends('layouts.site')

@php
    $published = $blog->published_at ?? $blog->created_at;
    $description = $blog->meta_description ?: ($blog->excerpt ?: Str::limit(trim(html_entity_decode(strip_tags($blog->content))), 155));
    $pageTitle = ($blog->meta_title ?: $blog->title).' · '.$site['name'];
    $url = route('blog.show', $blog->slug);
    $shareText = rawurlencode($blog->title);
    $shareUrl = rawurlencode($url);
@endphp

@section('title', $pageTitle)
@section('description', $description)
@section('body-class', 'page-plain')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">
@endpush

@push('meta')
    <link rel="canonical" href="{{ $url }}">
    @if ($blog->meta_keywords)
        <meta name="keywords" content="{{ $blog->meta_keywords }}">
    @endif
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="{{ $site['name'] }}">
    <meta property="og:title" content="{{ $blog->meta_title ?: $blog->title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $url }}">
    <meta property="article:published_time" content="{{ $published->toIso8601String() }}">
    <meta property="article:modified_time" content="{{ $blog->updated_at->toIso8601String() }}">
    <meta name="twitter:card" content="{{ $blog->image_url ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $blog->meta_title ?: $blog->title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if ($blog->image_url)
        <meta property="og:image" content="{{ $blog->image_url }}">
        <meta name="twitter:image" content="{{ $blog->image_url }}">
    @endif
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $blog->title,
            'description' => $description,
            'image' => $blog->image_url,
            'datePublished' => $published->toIso8601String(),
            'dateModified' => $blog->updated_at->toIso8601String(),
            'mainEntityOfPage' => $url,
            'author' => ['@type' => 'Organization', 'name' => $site['name']],
            'publisher' => ['@type' => 'Organization', 'name' => $site['name']],
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
@endpush

@section('content')
    <article class="article">
        <header class="article-head container narrow">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('home') }}#journal">Journal</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ Str::limit($blog->title, 40) }}</span>
            </nav>

            <p class="eyebrow">Journal</p>
            <h1>{{ $blog->title }}</h1>

            <p class="article-meta">
                <time datetime="{{ $published->toDateString() }}">{{ $published->format('j F Y') }}</time>
                <span aria-hidden="true">·</span>
                <span>{{ $readingTime }} min read</span>
                <span aria-hidden="true">·</span>
                <span>By {{ $site['name'] }}</span>
            </p>

            @if ($blog->excerpt)
                <p class="lead">{{ $blog->excerpt }}</p>
            @endif
        </header>

        @if ($blog->image_url)
            <figure class="container article-image">
                <img src="{{ $blog->image_url }}" alt="{{ $blog->title }}" fetchpriority="high">
            </figure>
        @endif

        <div class="article-body">
            {!! $blog->content !!}
        </div>

        <div class="article-share">
            <span class="article-share-label">Share this story</span>
            <div class="article-share-links">
                <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener noreferrer">Facebook</a>
                <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&amp;url={{ $shareUrl }}" target="_blank" rel="noopener noreferrer">X</a>
                <a href="https://pinterest.com/pin/create/button/?url={{ $shareUrl }}&amp;description={{ $shareText }}@if ($blog->image_url)&amp;media={{ rawurlencode($blog->image_url) }}@endif" target="_blank" rel="noopener noreferrer">Pinterest</a>
                <button type="button" data-copy-link="{{ $url }}">Copy link</button>
            </div>
            <p class="article-share-status" role="status" aria-live="polite" hidden>Link copied</p>
        </div>

        @if ($previous || $next)
            <nav class="article-nav container" aria-label="More stories">
                @if ($previous)
                    <a class="article-nav-link" href="{{ route('blog.show', $previous->slug) }}" rel="prev">
                        <span class="article-nav-label">&larr; Previous story</span>
                        <span class="article-nav-title">{{ $previous->title }}</span>
                    </a>
                @else
                    <span></span>
                @endif
                @if ($next)
                    <a class="article-nav-link article-nav-next" href="{{ route('blog.show', $next->slug) }}" rel="next">
                        <span class="article-nav-label">Next story &rarr;</span>
                        <span class="article-nav-title">{{ $next->title }}</span>
                    </a>
                @endif
            </nav>
        @endif
    </article>

    @if ($more->isNotEmpty())
        <section class="journal section section-accent">
            <div class="container">
                <div class="section-head">
                    <p class="eyebrow">Keep reading</p>
                    <h2>More from <em>the journal</em></h2>
                </div>

                <div class="journal-grid">
                    @foreach ($more as $post)
                        <article class="post">
                            <a class="post-image" href="{{ route('blog.show', $post->slug) }}" tabindex="-1" aria-hidden="true">
                                @if ($post->thumbnail_url)
                                    <img src="{{ $post->thumbnail_url }}" alt="" loading="lazy">
                                @endif
                            </a>
                            <p class="post-date">{{ ($post->published_at ?? $post->created_at)->format('j F Y') }}</p>
                            <h3><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h3>
                            @if ($post->excerpt)
                                <p class="post-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                            @endif
                            <a class="text-link" href="{{ route('blog.show', $post->slug) }}" aria-label="Read {{ $post->title }}">Read the story</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="article-cta section">
        <div class="container narrow">
            <p class="eyebrow">Let's begin</p>
            <h2>Planning your <em>own day?</em></h2>
            <p class="lead">Tell us a little about it and we will write back within two days.</p>
            <a class="btn btn-solid" href="{{ route('home') }}#contact">Get in touch</a>
        </div>
    </section>
@endsection
