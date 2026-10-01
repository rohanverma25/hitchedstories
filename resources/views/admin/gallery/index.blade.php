@extends('admin.layouts.app')

@section('title', 'Gallery')

@section('content')
    <div class="card">
        <h2 class="card-title">Upload images</h2>

        <form method="POST" action="{{ route('admin.gallery.store') }}" enctype="multipart/form-data" class="upload-form">
            @csrf

            <div class="field">
                <label for="images">Choose images</label>
                <input id="images" type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/webp,image/gif"
                    @class(['invalid' => $errors->has('images') || $errors->has('images.*')])>
                <p class="hint">
                    Select several at once: up to {{ \App\Http\Requests\Admin\GalleryUploadRequest::MAX_FILES }} images, 5 MB each, JPG, PNG, WebP or GIF.
                    Images larger than {{ \App\Services\ImageService::THUMBNAIL_SIZE }}px get a thumbnail automatically.
                </p>
                <p class="hint" id="images-summary" hidden></p>

                @error('images')
                    <p class="error">{{ $message }}</p>
                @enderror
                @foreach ($errors->get('images.*') as $messages)
                    @foreach ($messages as $message)
                        <p class="error">{{ $message }}</p>
                    @endforeach
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <div class="page-head gallery-head">
        <p class="muted">{{ $images->total() }} {{ Str::plural('image', $images->total()) }}</p>

        @if ($images->isNotEmpty())
            <form method="POST" action="{{ route('admin.gallery.destroy-many') }}" id="bulk-delete" class="bulk-actions"
                onsubmit="return confirm('Delete the selected images? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <label class="checkbox"><input type="checkbox" id="select-all"> Select all</label>
                <button type="submit" class="btn btn-danger btn-sm" id="bulk-delete-button" disabled>Delete selected</button>
            </form>
        @endif
    </div>

    @error('ids')
        <div class="alert alert-danger" role="alert">{{ $message }}</div>
    @enderror

    @if ($images->isEmpty())
        <div class="card empty">
            <h2>The gallery is empty</h2>
            <p class="muted">Upload some images above to see them here.</p>
        </div>
    @else
        <div class="gallery-grid">
            @foreach ($images as $image)
                <div class="gallery-item">
                    <a href="{{ $image->image_url }}" target="_blank" rel="noopener" title="Open full image">
                        <img src="{{ $image->thumbnail_url }}" alt="Gallery image {{ $image->id }}" loading="lazy">
                    </a>
                    <label class="gallery-check">
                        <input type="checkbox" name="ids[]" value="{{ $image->id }}" form="bulk-delete" class="gallery-checkbox">
                        <span class="sr-only">Select image {{ $image->id }}</span>
                    </label>
                    <form method="POST" action="{{ route('admin.gallery.destroy', $image) }}" class="gallery-delete"
                        onsubmit="return confirm('Delete this image? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>

        {{ $images->links('admin.partials.pagination') }}
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            // Summarise the chosen files before upload.
            var input = document.getElementById('images');
            var summary = document.getElementById('images-summary');

            input.addEventListener('change', function () {
                var total = 0;
                for (var i = 0; i < input.files.length; i++) total += input.files[i].size;

                summary.hidden = input.files.length === 0;
                summary.textContent = input.files.length + ' selected, ' + (total / 1048576).toFixed(1) + ' MB in total.';
            });

            // Select all / enable the bulk delete button.
            var selectAll = document.getElementById('select-all');
            var button = document.getElementById('bulk-delete-button');
            var boxes = document.querySelectorAll('.gallery-checkbox');

            if (!selectAll) return;

            function refresh() {
                var checked = document.querySelectorAll('.gallery-checkbox:checked').length;
                button.disabled = checked === 0;
                button.textContent = checked ? 'Delete selected (' + checked + ')' : 'Delete selected';
                selectAll.checked = checked === boxes.length;
            }

            selectAll.addEventListener('change', function () {
                boxes.forEach(function (box) { box.checked = selectAll.checked; });
                refresh();
            });
            boxes.forEach(function (box) { box.addEventListener('change', refresh); });
        })();
    </script>
@endpush
