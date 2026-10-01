@extends('admin.layouts.app')

@section('title', 'Reviews')

@section('content')
    <div class="page-head">
        <p class="muted">{{ $reviews->total() }} {{ Str::plural('review', $reviews->total()) }}</p>
        <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">+ New review</a>
    </div>

    @if ($reviews->isEmpty())
        <div class="card empty">
            <h2>No reviews yet</h2>
            <p class="muted">Add your first review to see it listed here.</p>
            <a href="{{ route('admin.reviews.create') }}" class="btn btn-primary">Add a review</a>
        </div>
    @else
        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>
                                <div class="blog-cell reviewer-cell">
                                    @if ($review->photo_url)
                                        <img src="{{ $review->photo_url }}" alt="" class="reviewer-photo" loading="lazy">
                                    @else
                                        <div class="reviewer-photo avatar" aria-hidden="true">{{ strtoupper(mb_substr($review->name, 0, 1)) }}</div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.reviews.edit', $review) }}" class="blog-title">{{ $review->name }}</a>
                                        @if ($review->designation)
                                            <div class="blog-slug">{{ $review->designation }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="nowrap">
                                <span class="stars" role="img" aria-label="{{ $review->rating }} out of 5 stars">{{ str_repeat('★', $review->rating) }}<span class="stars-off">{{ str_repeat('★', 5 - $review->rating) }}</span></span>
                            </td>
                            <td class="review-text">{{ Str::limit($review->review, 110) }}</td>
                            <td>
                                @if ($review->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge">Hidden</span>
                                @endif
                            </td>
                            <td class="actions-col">
                                <div class="row-actions">
                                    <a href="{{ route('admin.reviews.edit', $review) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}"
                                        onsubmit="return confirm('Delete this review? This cannot be undone.')">
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

        {{ $reviews->links('admin.partials.pagination') }}
    @endif
@endsection
