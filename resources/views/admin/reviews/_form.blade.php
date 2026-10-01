@if ($errors->any())
    <div class="alert alert-danger" role="alert">Please fix the highlighted fields and try again.</div>
@endif

<div class="form-grid">
    <div class="form-main">
        <div class="card">
            <div class="field">
                <label for="name">Reviewer name</label>
                <input id="name" type="text" name="name" value="{{ old('name', $review->name) }}" maxlength="255" required
                    @class(['invalid' => $errors->has('name')])>
                @error('name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="designation">Designation</label>
                <input id="designation" type="text" name="designation" value="{{ old('designation', $review->designation) }}" maxlength="255"
                    placeholder="Bride, Mumbai" @class(['invalid' => $errors->has('designation')])>
                <p class="hint">Optional. Shown under the name, such as a role or a city.</p>
                @error('designation')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <fieldset class="rating">
                    <legend>Rating</legend>
                    <div class="rating-stars">
                        @foreach ([5, 4, 3, 2, 1] as $star)
                            <input type="radio" id="rating-{{ $star }}" name="rating" value="{{ $star }}"
                                @checked((int) old('rating', $review->rating) === $star)>
                            <label for="rating-{{ $star }}" title="{{ $star }} {{ Str::plural('star', $star) }}">
                                <span class="sr-only">{{ $star }} {{ Str::plural('star', $star) }}</span>★
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                @error('rating')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="review">Review</label>
                <textarea id="review" name="review" rows="7" maxlength="2000" required
                    @class(['invalid' => $errors->has('review')])>{{ old('review', $review->review) }}</textarea>
                @error('review')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <aside class="form-side">
        <div class="card">
            <h2 class="card-title">Publish</h2>

            <label class="checkbox">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $review->is_published))>
                Published
            </label>
            <p class="hint">Unticked reviews stay hidden.</p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ $review->exists ? 'Save changes' : 'Create review' }}</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Photo</h2>

            <img id="photo-preview" src="{{ $review->photo_url }}" alt="" class="photo-preview" @if (! $review->photo_url) hidden @endif>

            <div class="field">
                <input id="photo" type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                    @class(['invalid' => $errors->has('photo')])>
                <p class="hint">Optional. JPG, PNG or WebP, up to 2 MB.</p>
                @error('photo')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            @if ($review->photo)
                <label class="checkbox">
                    <input type="checkbox" name="remove_photo" value="1" @checked(old('remove_photo'))>
                    Remove current photo
                </label>
            @endif
        </div>
    </aside>
</div>

@push('scripts')
    <script>
        (function () {
            var photo = document.getElementById('photo');
            var preview = document.getElementById('photo-preview');

            photo.addEventListener('change', function () {
                if (photo.files && photo.files[0]) {
                    preview.src = URL.createObjectURL(photo.files[0]);
                    preview.hidden = false;
                }
            });
        })();
    </script>
@endpush
