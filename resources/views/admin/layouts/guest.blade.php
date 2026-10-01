<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="guest">
    <div class="auth">
        <aside class="auth-hero">
            <div class="brand">{{ config('app.name') }}<span>Admin</span></div>
            <div class="auth-hero-text">
                <h2>Every detail,<br><em>beautifully managed.</em></h2>
                <p>Sign in to manage {{ config('app.name') }} from one place.</p>
            </div>
            <div class="auth-hero-foot">&copy; {{ date('Y') }} {{ config('app.name') }}</div>
        </aside>

        <main class="auth-panel">
            <div class="auth-card">
                <div class="brand brand-mobile">{{ config('app.name') }}<span>Admin</span></div>
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
