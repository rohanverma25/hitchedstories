{{--
    Shared behaviour for content forms: rich text editor on the #{{ $editorField }} textarea,
    slug auto-fill from #title into #slug, and a preview for the #image file input.
--}}
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        (function () {
            // Rich text editor. The plain textarea stays as the fallback if the editor script fails to load.
            var textarea = document.getElementById(@json($editorField));
            var editorEl = document.getElementById('editor');

            if (window.Quill) {
                var quill = new Quill(editorEl, {
                    theme: 'snow',
                    placeholder: 'Start writing…',
                    modules: {
                        toolbar: [
                            [{ header: [2, 3, 4, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            [{ align: [] }],
                            ['blockquote', 'code-block', 'link'],
                            ['clean']
                        ]
                    }
                });

                quill.clipboard.dangerouslyPasteHTML(textarea.value);
                textarea.hidden = true;
                editorEl.hidden = false;

                quill.on('text-change', function () {
                    textarea.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
                });
            }

            // Fill the slug from the title until the slug is edited by hand.
            var title = document.getElementById('title');
            var slug = document.getElementById('slug');
            var autofill = slug.dataset.autofill === 'on' && slug.value === '';

            function slugify(value) {
                return value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
                    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
            }

            title.addEventListener('input', function () {
                if (autofill) slug.value = slugify(title.value);
            });
            slug.addEventListener('input', function () {
                autofill = slug.value === '' && slug.dataset.autofill === 'on';
            });
            slug.addEventListener('blur', function () {
                slug.value = slugify(slug.value);
            });

            // Preview the chosen image.
            var image = document.getElementById('image');
            var preview = document.getElementById('image-preview');

            image.addEventListener('change', function () {
                if (image.files && image.files[0]) {
                    preview.src = URL.createObjectURL(image.files[0]);
                    preview.hidden = false;
                }
            });
        })();
    </script>
@endpush
