@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="welcome">
        <div>
            <h2>Welcome back, {{ auth('admin')->user()->name }}</h2>
            <p>Here is what is happening on {{ config('app.name') }} today.</p>
        </div>
        <div class="welcome-date">{{ now()->format('l, j F Y') }}</div>
    </div>

    <div class="stats">
        <a class="stat" href="{{ route('admin.leads.index') }}">
            <div class="stat-icon stat-icon-alt">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg>
            </div>
            <div>
                <div class="stat-label">Leads{{ $newLeadCount ? ' ('.$newLeadCount.' new)' : '' }}</div>
                <div class="stat-value">{{ number_format($leadCount) }}</div>
            </div>
        </a>
        <a class="stat" href="{{ route('admin.blogs.index') }}">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><path d="M9 13h6"/><path d="M9 17h6"/></svg>
            </div>
            <div>
                <div class="stat-label">Blogs</div>
                <div class="stat-value">{{ number_format($blogCount) }}</div>
            </div>
        </a>
        <a class="stat" href="{{ route('admin.services.index') }}">
            <div class="stat-icon stat-icon-plum">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/></svg>
            </div>
            <div>
                <div class="stat-label">Services</div>
                <div class="stat-value">{{ number_format($serviceCount) }}</div>
            </div>
        </a>
        <a class="stat" href="{{ route('admin.gallery.index') }}">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="1.8"/><path d="M21 16l-5-5-8 8"/></svg>
            </div>
            <div>
                <div class="stat-label">Gallery images</div>
                <div class="stat-value">{{ number_format($galleryCount) }}</div>
            </div>
        </a>
        <a class="stat" href="{{ route('admin.reviews.index') }}">
            <div class="stat-icon stat-icon-alt">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/></svg>
            </div>
            <div>
                <div class="stat-label">Reviews</div>
                <div class="stat-value">{{ number_format($reviewCount) }}</div>
            </div>
        </a>
        <div class="stat">
            <div class="stat-icon stat-icon-plum">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-label">Users</div>
                <div class="stat-value">{{ number_format($userCount) }}</div>
            </div>
        </div>
        <div class="stat">
            <div class="stat-icon stat-icon-alt">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l8 3v6c0 4.5-3.2 8-8 9-4.8-1-8-4.5-8-9V6l8-3z"/><path d="M9 12l2 2 4-4"/></svg>
            </div>
            <div>
                <div class="stat-label">Admins</div>
                <div class="stat-value">{{ number_format($adminCount) }}</div>
            </div>
        </div>
    </div>
@endsection
