@extends('admin.layouts.app')

@section('title', 'Page content')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">Please fix the highlighted fields and try again.</div>
    @endif

    <form method="POST" action="{{ route('admin.content.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-main">
                @foreach ($sections as $id => $section)
                    <div class="card" id="{{ $id }}">
                        <h2 class="card-title">{{ $section['label'] }}</h2>
                        @if ($section['hint'])
                            <p class="hint card-hint">{{ $section['hint'] }}</p>
                        @endif

                        @foreach ($section['fields'] as $key => [$label, $type])
                            <div class="field">
                                <label for="{{ $key }}">{{ $label }}</label>

                                @if ($type === 'image')
                                    <img src="{{ $images[$key] }}" alt="Current image" class="logo-preview" data-preview="{{ $key }}">
                                    <input id="{{ $key }}" type="file" name="{{ $key }}" accept="image/jpeg,image/png,image/webp"
                                        @class(['invalid' => $errors->has($key)])>
                                    @if ($saved[$key])
                                        <label class="checkbox">
                                            <input type="checkbox" name="remove_{{ $key }}" value="1" @checked(old('remove_'.$key))>
                                            Go back to the original image
                                        </label>
                                    @endif
                                @elseif ($type === 'textarea')
                                    <textarea id="{{ $key }}" name="{{ $key }}" rows="4" maxlength="2000"
                                        placeholder="{{ $defaults[$key] }}" @class(['invalid' => $errors->has($key)])>{{ old($key, $saved[$key]) }}</textarea>
                                @else
                                    <input id="{{ $key }}" type="text" name="{{ $key }}" value="{{ old($key, $saved[$key]) }}" maxlength="255"
                                        placeholder="{{ $defaults[$key] }}" @class(['invalid' => $errors->has($key)])>
                                @endif

                                @error($key)
                                    <p class="error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <aside class="form-side">
                <div class="card">
                    <h2 class="card-title">Save</h2>
                    <p class="hint">Leave a text field empty to use the original wording, shown greyed out inside it.</p>
                    <p class="hint">In headings, wrap words in *asterisks* to show them in italics.</p>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save content</button>
                    </div>
                </div>

                <div class="card">
                    <h2 class="card-title">Jump to</h2>
                    <p class="side-links">
                        @foreach ($sections as $id => $section)
                            <a href="#{{ $id }}">{{ $section['label'] }}</a>
                        @endforeach
                    </p>
                    <p class="hint">Site name, contact details and social links live in <a href="{{ route('admin.settings.edit') }}">Settings</a>.</p>
                </div>
            </aside>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('input[type="file"]').forEach(function (input) {
            input.addEventListener('change', function () {
                var preview = document.querySelector('[data-preview="' + input.id + '"]');
                if (preview && input.files && input.files[0]) {
                    preview.src = URL.createObjectURL(input.files[0]);
                }
            });
        });
    </script>
@endpush
