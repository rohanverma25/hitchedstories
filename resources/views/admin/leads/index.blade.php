@extends('admin.layouts.app')

@section('title', 'Leads')

@section('content')
    <div class="page-head">
        <p class="muted">{{ $leads->total() }} {{ Str::plural('lead', $leads->total()) }}{{ $status || $search ? ' found' : '' }}</p>
    </div>

    <form method="GET" action="{{ route('admin.leads.index') }}" class="lead-filters">
        <div class="lead-tabs">
            <a href="{{ route('admin.leads.index', array_filter(['q' => $search])) }}" @class(['active' => ! $status])>All <span>{{ $counts->sum() }}</span></a>
            @foreach (\App\Models\Lead::STATUSES as $value => $label)
                <a href="{{ route('admin.leads.index', array_filter(['status' => $value, 'q' => $search])) }}" @class(['active' => $status === $value])>{{ $label }} <span>{{ $counts[$value] ?? 0 }}</span></a>
            @endforeach
        </div>
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <input type="search" name="q" value="{{ $search }}" placeholder="Search name, email, phone or venue" aria-label="Search leads">
        <button type="submit" class="btn btn-ghost">Search</button>
    </form>

    @if ($leads->isEmpty())
        <div class="card empty">
            <h2>{{ $status || $search ? 'No matching leads' : 'No leads yet' }}</h2>
            <p class="muted">Enquiries sent from the contact form on your website will appear here.</p>
        </div>
    @else
        <div class="card table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Couple</th>
                        <th>Contact</th>
                        <th>Wedding</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leads as $lead)
                        <tr>
                            <td>
                                <a href="{{ route('admin.leads.show', $lead) }}" class="blog-title">{{ $lead->name }}</a>
                                @if ($lead->message)
                                    <div class="blog-slug">{{ Str::limit($lead->message, 60) }}</div>
                                @endif
                            </td>
                            <td>
                                <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                @if ($lead->phone)
                                    <div class="blog-slug">{{ $lead->phone }}</div>
                                @endif
                            </td>
                            <td>
                                {{ $lead->event_date?->format('j M Y') ?? 'Date not set' }}
                                @if ($lead->venue)
                                    <div class="blog-slug">{{ $lead->venue }}</div>
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $lead->status }}">{{ \App\Models\Lead::STATUSES[$lead->status] ?? $lead->status }}</span></td>
                            <td class="nowrap">{{ $lead->created_at->format('j M Y, g:i a') }}</td>
                            <td class="actions-col">
                                <div class="row-actions">
                                    <a href="{{ route('admin.leads.show', $lead) }}" class="btn btn-ghost btn-sm">View</a>
                                    <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}"
                                        onsubmit="return confirm('Delete this lead? This cannot be undone.')">
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

        {{ $leads->links('admin.partials.pagination') }}
    @endif
@endsection
