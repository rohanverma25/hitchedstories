@extends('admin.layouts.app')

@section('title', $lead->name)

@section('content')
    <div class="page-head">
        <a href="{{ route('admin.leads.index') }}" class="btn btn-ghost">&larr; All leads</a>
    </div>

    <div class="form-grid">
        <div class="form-main">
            <div class="card">
                <h2 class="card-title">Enquiry</h2>
                <dl class="lead-details">
                    <div><dt>Names</dt><dd>{{ $lead->name }}</dd></div>
                    <div><dt>Email</dt><dd><a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a></dd></div>
                    <div><dt>Phone</dt><dd>{{ $lead->phone ?: 'Not given' }}</dd></div>
                    <div><dt>Wedding date</dt><dd>{{ $lead->event_date?->format('l, j F Y') ?? 'Not given' }}</dd></div>
                    <div><dt>Venue or city</dt><dd>{{ $lead->venue ?: 'Not given' }}</dd></div>
                    <div><dt>Received</dt><dd>{{ $lead->created_at->format('j F Y, g:i a') }}</dd></div>
                </dl>
                <h3 class="lead-subtitle">Their plans</h3>
                <p class="lead-message">{{ $lead->message ?: 'No message.' }}</p>
            </div>
        </div>

        <aside class="form-side">
            <form method="POST" action="{{ route('admin.leads.update', $lead) }}" class="card">
                @csrf
                @method('PUT')
                <h2 class="card-title">Follow-up</h2>

                <div class="field">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        @foreach (\App\Models\Lead::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $lead->status) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="notes">Private notes</label>
                    <textarea id="notes" name="notes" rows="5" maxlength="5000" @class(['invalid' => $errors->has('notes')])>{{ old('notes', $lead->notes) }}</textarea>
                    <p class="hint">Only visible here in the admin panel.</p>
                    @error('notes')
                        <p class="error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.leads.destroy', $lead) }}" class="card"
                onsubmit="return confirm('Delete this lead? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete lead</button>
            </form>
        </aside>
    </div>
@endsection
