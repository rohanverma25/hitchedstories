@switch($name)
    @case('instagram_url')
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".6" fill="currentColor"/></svg>
        @break
    @case('facebook_url')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3a4 4 0 0 0-4 4v3H7v4h3v6h4v-6h3l1-4h-4V8z"/></svg>
        @break
    @case('youtube_url')
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.5l5 2.5-5 2.5z"/></svg>
        @break
    @case('twitter_url')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4l16 16"/><path d="M20 4L4 20"/></svg>
        @break
    @case('linkedin_url')
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 11v6"/><path d="M8 7.5v.01"/><path d="M12 17v-6"/><path d="M12 13.5c0-1.4 1-2.5 2.5-2.5S17 12 17 13.5V17"/></svg>
        @break
    @case('pinterest_url')
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M11 8.5c2.5-.8 4.5.6 4.3 2.7-.2 2-1.6 3.3-3 3.1-1-.1-1.5-.8-1.3-1.7L12 7l-2 9"/></svg>
        @break
@endswitch
