@if ($errors->any())
    <div class="alert alert-danger" role="alert">Please fix the highlighted fields and try again.</div>
@endif

<div class="form-grid">
    <div class="form-main">
        <div class="card">
            <div class="field">
                <label for="title">Title</label>
                <input id="title" type="text" name="title" value="{{ old('title', $blog->title) }}" maxlength="255" required
                    @class(['invalid' => $errors->has('title')])>
                @error('title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="slug">Slug</label>
                <input id="slug" type="text" name="slug" value="{{ old('slug', $blog->slug) }}" maxlength="255" placeholder="my-blog-post"
                    data-autofill="{{ $blog->exists ? 'off' : 'on' }}" @class(['invalid' => $errors->has('slug')])>
                <p class="hint">The address of the post. Leave blank to generate it from the title.</p>
                @error('slug')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"
                    @class(['invalid' => $errors->has('excerpt')])>{{ old('excerpt', $blog->excerpt) }}</textarea>
                <p class="hint">A short summary shown in blog listings.</p>
                @error('excerpt')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="content">Content</label>
                <div @class(['editor-wrap', 'invalid' => $errors->has('content')])>
                    <textarea id="content" name="content" rows="14">{{ old('content', $blog->content) }}</textarea>
                    <div id="editor" hidden></div>
                </div>
                @error('content')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">SEO meta</h2>

            <div class="field">
                <label for="meta_title">Meta title</label>
                <input id="meta_title" type="text" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}" maxlength="255"
                    @class(['invalid' => $errors->has('meta_title')])>
                <p class="hint">Shown in search results and the browser tab. Around 60 characters works best.</p>
                @error('meta_title')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="meta_description">Meta description</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="500"
                    @class(['invalid' => $errors->has('meta_description')])>{{ old('meta_description', $blog->meta_description) }}</textarea>
                <p class="hint">Around 160 characters works best.</p>
                @error('meta_description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="meta_keywords">Meta keywords</label>
                <input id="meta_keywords" type="text" name="meta_keywords" value="{{ old('meta_keywords', $blog->meta_keywords) }}" maxlength="255"
                    placeholder="wedding, venue, planning" @class(['invalid' => $errors->has('meta_keywords')])>
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
                <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $blog->is_published))>
                Published
            </label>
            <p class="hint">Unticked posts are saved as drafts.</p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ $blog->exists ? 'Save changes' : 'Create blog' }}</button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title">Image</h2>

            <img id="image-preview" src="{{ $blog->thumbnail_url }}" alt="" class="image-preview" @if (! $blog->thumbnail_url) hidden @endif>

            <div class="field">
                <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                    @class(['invalid' => $errors->has('image')])>
                <p class="hint">JPG, PNG, WebP or GIF, up to 5 MB. Images larger than {{ \App\Services\ImageService::THUMBNAIL_SIZE }}px get a thumbnail automatically.</p>
                @error('image')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            @if ($blog->image)
                <label class="checkbox">
                    <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                    Remove current image
                </label>
            @endif
        </div>
    </aside>
</div>

@include('admin.partials.form-scripts', ['editorField' => 'content'])
