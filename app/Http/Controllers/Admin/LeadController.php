<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * List the leads, optionally by status or search term.
     */
    public function index(Request $request): View
    {
        $status = (string) $request->query('status');
        $search = trim((string) $request->query('q'));

        $leads = Lead::query()
            ->when(array_key_exists($status, Lead::STATUSES), fn ($query) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $like = '%'.$search.'%';
                    $query->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like)
                        ->orWhere('venue', 'like', $like);
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.leads.index', [
            'leads' => $leads,
            'counts' => Lead::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'status' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Show a lead.
     */
    public function show(Lead $lead): View
    {
        return view('admin.leads.show', ['lead' => $lead]);
    }

    /**
     * Update a lead's status and private notes.
     */
    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Lead::STATUSES))],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $lead->update($data);

        return redirect()->route('admin.leads.show', $lead)->with('status', 'Lead updated.');
    }

    /**
     * Delete a lead.
     */
    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }
}
