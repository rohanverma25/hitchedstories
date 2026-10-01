@extends('admin.layouts.app')

@section('title', 'Blogs')

@section('content')
    <div class="page-head">
        <p class="muted">{{ $blogs->total() }} {{ Str::plural('blog', $blogs->total()) }}</p>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">+ New blog</a>
    </div>

    @if ($blogs->isEmpty())
        <div class="card empty">
            <h2>No blogs yet</h2>
            <p class="muted">Write your first post to see it listed here.</p>
            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">Create a blog</a>
        </div>
    @else
        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Blog</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($blogs as $blog)
                        <tr>
                            <td>
                                <div class="blog-cell">
                                    @if ($blog->thumbnail_url)
                                        <img src="{{ $blog->thumbnail_url }}" alt="" class="blog-thumb" loading="lazy">
                                    @else
                                        <div class="blog-thumb blog-thumb-empty" aria-hidden="true"></div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="blog-title">{{ $blog->title }}</a>
                                        <div class="blog-slug">/{{ $blog->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if ($blog->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge">Draft</span>
                                @endif
                            </td>
                            <td class="nowrap">{{ $blog->updated_at->format('j M Y') }}</td>
                            <td class="actions-col">
                                <div class="row-actions">
                                    <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.blogs.destroy', $blog) }}"
                                        onsubmit="return confirm('Delete this blog? This cannot be undone.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $blogs->links('admin.partials.pagination') }}
    @endif
@endsection
