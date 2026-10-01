@extends('admin.layouts.app')

@section('title', 'Email / SMTP')

@php
    $value = fn (string $key, $default = '') => old($key, $settings[$key] ?? $default);
    $smtpOn = old('smtp_enabled', ($settings['smtp_enabled'] ?? '0') === '1');
    $notifyOn = old('lead_notify_enabled', $notifyEnabled);
@endphp

@section('content')
    @if (session('mail_error'))
        <div class="alert alert-danger" role="alert">{{ session('mail_error') }}</div>
    @endif
    @if ($errors->any() && ! $errors->has('test_email'))
        <div class="alert alert-danger" role="alert">Please fix the highlighted fields and try again.</div>
    @endif

    <div class="form-grid">
        <form method="POST" action="{{ route('admin.mail.update') }}" class="form-main" id="mail-form">
            @csrf
            @method('PUT')

            <div class="card">
                <h2 class="card-title">New enquiry notifications</h2>

                <label class="checkbox">
                    <input type="checkbox" name="lead_notify_enabled" value="1" @checked($notifyOn)>
                    Email me when someone sends an enquiry
                </label>

                <div class="field">
                    <label for="lead_notify_to">Send notifications to</label>
                    <input id="lead_notify_to" type="text" name="lead_notify_to" value="{{ $value('lead_notify_to') }}" maxlength="500"
                        placeholder="{{ $notifyDefault ?: 'you@example.com' }}" @class(['invalid' => $errors->has('lead_notify_to')])>
                    <p class="hint">Separate several addresses with commas. Left empty, the contact email from Settings is used{{ $notifyDefault ? ' ('.$notifyDefault.')' : ', which is not set yet' }}.</p>
                    @error('lead_notify_to')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="card">
                <h2 class="card-title">SMTP server</h2>
                <p class="hint card-hint">Use the details from your email provider (Gmail, Zoho, your hosting, Brevo and so on). Gmail needs an app password, not your normal password.</p>

                <label class="checkbox">
                    <input type="checkbox" name="smtp_enabled" value="1" @checked($smtpOn)>
                    Send email through this SMTP server
                </label>

                <div class="field-row">
                    <div class="field">
                        <label for="smtp_host">Host</label>
                        <input id="smtp_host" type="text" name="smtp_host" value="{{ $value('smtp_host') }}" maxlength="255" placeholder="smtp.example.com"
                            autocomplete="off" @class(['invalid' => $errors->has('smtp_host')])>
                        @error('smtp_host')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="smtp_port">Port</label>
                        <input id="smtp_port" type="number" name="smtp_port" value="{{ $value('smtp_port', 587) }}" min="1" max="65535"
                            @class(['invalid' => $errors->has('smtp_port')])>
                        @error('smtp_port')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="smtp_encryption">Encryption</label>
                        <select id="smtp_encryption" name="smtp_encryption">
                            @foreach ($encryptions as $key => $label)
                                <option value="{{ $key }}" @selected($value('smtp_encryption', 'tls') === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('smtp_encryption')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="smtp_username">Username</label>
                        <input id="smtp_username" type="text" name="smtp_username" value="{{ $value('smtp_username') }}" maxlength="255"
                            autocomplete="off" @class(['invalid' => $errors->has('smtp_username')])>
                        @error('smtp_username')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="smtp_password">Password</label>
                        <input id="smtp_password" type="password" name="smtp_password" value="" maxlength="500" autocomplete="new-password"
                            placeholder="{{ $hasPassword ? 'Saved. Type to replace it.' : '' }}" @class(['invalid' => $errors->has('smtp_password')])>
                        <p class="hint">Stored encrypted and never shown again.</p>
                        @error('smtp_password')
                            <p class="error">{{ $message }}</p>
                        @enderror
                        @if ($hasPassword)
                            <label class="checkbox">
                                <input type="checkbox" name="clear_smtp_password" value="1">
                                Remove the saved password
                            </label>
                        @endif
                    </div>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="mail_from_address">Sender email</label>
                        <input id="mail_from_address" type="email" name="mail_from_address" value="{{ $value('mail_from_address') }}" maxlength="255"
                            placeholder="hello@yourdomain.com" @class(['invalid' => $errors->has('mail_from_address')])>
                        <p class="hint">Most providers only accept an address on your own domain or account.</p>
                        @error('mail_from_address')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label for="mail_from_name">Sender name</label>
                        <input id="mail_from_name" type="text" name="mail_from_name" value="{{ $value('mail_from_name') }}" maxlength="255"
                            placeholder="{{ $settings['site_name'] ?? config('app.name') }}" @class(['invalid' => $errors->has('mail_from_name')])>
                        @error('mail_from_name')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </form>

        <aside class="form-side">
            <div class="card">
                <h2 class="card-title">Save</h2>
                <p class="hint">Save first, then send a test to check the connection.</p>
                <div class="form-actions">
                    <button type="submit" form="mail-form" class="btn btn-primary">Save email settings</button>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.mail.test') }}" class="card">
                @csrf
                <h2 class="card-title">Send a test email</h2>
                <div class="field">
                    <label for="test_email">Send to</label>
                    <input id="test_email" type="email" name="test_email" value="{{ old('test_email', auth('admin')->user()->email) }}" required
                        @class(['invalid' => $errors->has('test_email')])>
                    @error('test_email')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-ghost">Send test</button>
                </div>
                <p class="hint">Uses the saved settings, not unsaved changes on this page.</p>
            </form>
        </aside>
    </div>
@endsection
