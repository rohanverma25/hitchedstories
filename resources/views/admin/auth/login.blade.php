@extends('admin.layouts.guest')

@section('title', 'Sign in')

@section('content')
    <h1>Welcome back</h1>
    <p class="muted">Sign in to your admin account to continue.</p>

    <form method="POST" action="{{ route('admin.login.store') }}" novalidate>
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required autofocus autocomplete="username"
                @class(['invalid' => $errors->has('email')])>
            @error('email')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Your password" required autocomplete="current-password"
                @class(['invalid' => $errors->has('password')])>
            @error('password')
                <p class="error">{{ $message }}</p>
            @enderror
        </div>

        <label class="checkbox">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            Remember me
        </label>

        <button type="submit" class="btn btn-primary btn-block">Sign in</button>
    </form>
@endsection
