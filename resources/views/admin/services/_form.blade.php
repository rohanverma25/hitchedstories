@if ($errors->any())
    <div class="alert alert-danger" role="alert">Please fix the highlighted fields and try again.</div>
@endif

<div class="form-grid">
    <div class="form-main">
        <div class="card">
            <div class="field">
                <label for="title">Title</label>
                <input id="title" type="text" name="title" value="{{ old('title', $service->title) }}" maxlength="255" required
                    @class(['invalid' => $errors->has('title')])>
                @error('title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="slug">Slug</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $service->slug) }}" maxlength="255" placeholder="wedding-photography"
                    data-autofill="{{ $service->exists ? 'off' : 'on' }}" @class(['invalid' => $errors->has('slug')])>
                <p class="hint">The address of the service page. Leave blank to generate it from the title.</p>
                @error('slug')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="short_description">Short description</label>
                <textarea id="short_description" name="short_description" rows="3" maxlength="500"
                    @class(['invalid' => $errors->has('short_description')])>{{ old('short_description', $service->short_description) }}</textarea>
                <p class="hint">A one or two line summary shown in service listings.</p>
                @error('short_description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="description">Description</label>
                <div @class(['editor-wrap', 'invalid' => $errors->has('description')])>
                    <textarea id="description" name="description" rows="14">{{ old('description', $service->description) }}</textarea>
                    <div id="editor" hidden></div>
                </div>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">SEO meta</h2>

            <div class="field">
                <label for="meta_title">Meta title</label>
                <input id="meta_title" type="text" name="meta_title" value="{{ old('meta_title', $service->meta_title) }}" maxlength="255"
                    @class(['invalid' => $errors->has('meta_title')])>
                <p class="hint">Shown in search results and the browser tab. Around 60 characters works best.</p>
                @error('meta_title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="meta_description">Meta description</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="500"
                    @class(['invalid' => $errors->has('meta_description')])>{{ old('meta_description', $service->meta_description) }}</textarea>
                <p class="hint">Around 160 characters works best.</p>
                @error('meta_description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="meta_keywords">Meta keywords</label>
                <input id="meta_keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords', $service->meta_keywords) }}" maxlength="255"
                    placeholder="wedding, photography, venue" @class(['invalid' => $errors->has('meta_keywords')])>
                <p class="hint">Separate keywords with commas.</p>
                @error('meta_keywords')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <aside class="form-side">
        <div class="card">
            <h2 class="card-title">Publish</h2>

            <label class="checkbox">
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $service->is_published))>
                Published
            </label>
            <p class="hint">Unticked services stay hidden.</p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ $service->exists ? 'Save changes' : 'Create service' }}</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Details</h2>

            <div class="field">
                <label for="price">Starting price</label>
                <input id="price" type="number" name="price" value="{{ old('price', $service->price) }}" min="0" step="0.01" inputmode="decimal"
                    @class(['invalid' => $errors->has('price')])>
                <p class="hint">Optional. Leave blank if the price is on request.</p>
                @error('price')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="sort_order">Display order</label>
                <input id="sort_order" type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}" min="0" step="1"
                    @class(['invalid' => $errors->has('sort_order')])>
                <p class="hint">Lower numbers are listed first.</p>
                @error('sort_order')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Image</h2>

            <img id="image-preview" src="{{ $service->thumbnail_url }}" alt="" class="image-preview" @if (! $service->thumbnail_url) hidden @endif>

            <div class="field">
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                    @class(['invalid' => $errors->has('image')])>
                <p class="hint">JPG, PNG, WebP or GIF, up to 5 MB. Images larger than {{ \App\Services\ImageService::THUMBNAIL_SIZE }}px get a thumbnail automatically.</p>
                @error('image')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            @if ($service->image)
                <label class="checkbox">
                    <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                    Remove current image
                </label>
            @endif
        </div>
    </aside>
</div>

@include('admin.partials.form-scripts', ['editorField' => 'description'])
