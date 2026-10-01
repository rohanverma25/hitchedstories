@extends('admin.layouts.app')

@section('title', 'Services')

@section('content')
    <div class="page-head">
        <p class="muted">{{ $services->total() }} {{ Str::plural('service', $services->total()) }}</p>
        <a href="{{ route('admin.services.create') }}" class="btn btn-primary">+ New service</a>
    </div>

    @if ($services->isEmpty())
        <div class="card empty">
            <h2>No services yet</h2>
            <p class="muted">Add your first service to see it listed here.</p>
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">Add a service</a>
        </div>
    @else
        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Price</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $service)
                        <tr>
                            <td>
                                <div class="blog-cell">
                                    @if ($service->thumbnail_url)
                                        <img src="{{ $service->thumbnail_url }}" alt="" class="blog-thumb" loading="lazy">
                                    @else
                                        <div class="blog-thumb blog-thumb-empty" aria-hidden="true"></div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.services.edit', $service) }}" class="blog-title">{{ $service->title }}</a>
                                        <div class="blog-slug">/{{ $service->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="nowrap">{{ $service->price !== null ? number_format((float) $service->price, 2) : '—' }}</td>
                            <td class="nowrap">{{ $service->sort_order }}</td>
                            <td>
                                @if ($service->is_published)
                                    <span class="badge badge-success">Published</span>
                                @else
                                    <span class="badge">Hidden</span>
                                @endif
                            </td>
                            <td class="actions-col">
                                <div class="row-actions">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-ghost btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}"
                                        onsubmit="return confirm('Delete this service? This cannot be undone.')">
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

        {{ $services->links('admin.partials.pagination') }}
    @endif
@endsection
