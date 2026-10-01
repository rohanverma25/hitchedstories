@extends('admin.layouts.app')

@section('title', 'Settings')

@php
    $value = fn (string $key) => old($key, $settings[$key] ?? '');

    $contactFields = [
        'contact_email' => ['Email', 'email', 'hello@example.com'],
        'contact_phone' => ['Phone', 'text', '+91 98765 43210'],
        'contact_phone_alt' => ['Alternate phone', 'text', ''],
        'whatsapp_number' => ['WhatsApp number', 'text', '+91 98765 43210'],
        'working_hours' => ['Working hours', 'text', 'Mon to Sat, 10am to 7pm'],
    ];
@endphp

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">Please fix the highlighted fields and try again.</div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-main">
                <div class="card">
                    <h2 class="card-title">Content</h2>

                    <div class="field">
                        <label for="site_name">Site name</label>
                        <input id="site_name" type="text" name="site_name" value="{{ $value('site_name') }}" maxlength="255"
                            @class(['invalid' => $errors->has('site_name')])>
                        @error('site_name')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="tagline">Tagline</label>
                        <input id="tagline" type="text" name="tagline" value="{{ $value('tagline') }}" maxlength="255"
                            @class(['invalid' => $errors->has('tagline')])>
                        @error('tagline')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="about_content">About content</label>
                        <textarea id="about_content" name="about_content" rows="5" maxlength="5000"
                            @class(['invalid' => $errors->has('about_content')])>{{ $value('about_content') }}</textarea>
                        <p class="hint">A short introduction to the business, for the footer or contact page.</p>
                        @error('about_content')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="footer_text">Footer text</label>
                        <input id="footer_text" type="text" name="footer_text" value="{{ $value('footer_text') }}" maxlength="1000"
                            placeholder="© {{ date('Y') }} {{ config('app.name') }}. All rights reserved." @class(['invalid' => $errors->has('footer_text')])>
                        @error('footer_text')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Contact info</h2>

                    <div class="field-row">
                        @foreach ($contactFields as $key => [$label, $type, $placeholder])
                            <div class="field">
                                <label for="{{ $key }}">{{ $label }}</label>
                                <input id="{{ $key }}" type="{{ $type }}" name="{{ $key }}" value="{{ $value($key) }}" placeholder="{{ $placeholder }}"
                                    @class(['invalid' => $errors->has($key)])>
                                @error($key)
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <div class="field">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3" maxlength="1000"
                            @class(['invalid' => $errors->has('address')])>{{ $value('address') }}</textarea>
                        @error('address')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="map_embed">Map embed code</label>
                        <textarea id="map_embed" name="map_embed" rows="3" maxlength="5000" spellcheck="false"
                            @class(['code', 'invalid' => $errors->has('map_embed')])>{{ $value('map_embed') }}</textarea>
                        <p class="hint">Paste the &lt;iframe&gt; code from Google Maps (Share, then Embed a map).</p>
                        @error('map_embed')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Social media</h2>

                    <div class="field-row">
                        @foreach ($social as $key => $label)
                            <div class="field">
                                <label for="{{ $key }}">{{ $label }}</label>
                                <input id="{{ $key }}" type="url" name="{{ $key }}" value="{{ $value($key) }}" maxlength="255" placeholder="https://"
                                    @class(['invalid' => $errors->has($key)])>
                                @error($key)
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Scripts</h2>
                    <p class="hint card-hint">For analytics, chat widgets, pixel codes and similar. Paste the code exactly as the provider gives it, including the &lt;script&gt; tags.</p>

                    <div class="field">
                        <label for="head_scripts">Header scripts</label>
                        <textarea id="head_scripts" name="head_scripts" rows="6" maxlength="20000" spellcheck="false"
                            @class(['code', 'invalid' => $errors->has('head_scripts')])>{{ $value('head_scripts') }}</textarea>
                        <p class="hint">Placed inside &lt;head&gt;, for example Google Analytics or Search Console verification.</p>
                        @error('head_scripts')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="body_scripts">Footer scripts</label>
                        <textarea id="body_scripts" name="body_scripts" rows="6" maxlength="20000" spellcheck="false"
                            @class(['code', 'invalid' => $errors->has('body_scripts')])>{{ $value('body_scripts') }}</textarea>
                        <p class="hint">Placed just before &lt;/body&gt;, for example a chat widget.</p>
                        @error('body_scripts')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <aside class="form-side">
                <div class="card">
                    <h2 class="card-title">Save</h2>
                    <p class="hint">Changes apply to the whole site.</p>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save settings</button>
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Logo</h2>

                    <img id="logo-preview" src="{{ $logoUrl }}" alt="Current logo" class="logo-preview" @if (! $logoUrl) hidden @endif>

                    <div class="field">
                        <input id="logo" type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/svg+xml"
                            @class(['invalid' => $errors->has('logo')])>
                        <p class="hint">JPG, PNG, WebP or SVG, up to 2 MB. A transparent PNG or SVG works best.</p>
                        @error('logo')
                            <p class="error">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($logoUrl)
                        <label class="checkbox">
                            <input type="checkbox" name="remove_logo" value="1" @checked(old('remove_logo'))>
                            Remove current logo
                        </label>
                    @endif
                </div>
            </aside>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            var logo = document.getElementById('logo');
            var preview = document.getElementById('logo-preview');

            logo.addEventListener('change', function () {
                if (logo.files && logo.files[0]) {
                    preview.src = URL.createObjectURL(logo.files[0]);
                    preview.hidden = false;
                }
            });
        })();
    </script>
@endpush
