<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnquiryRequest;
use App\Models\Lead;
use App\Services\LeadNotifier;
use Illuminate\Http\RedirectResponse;

class EnquiryController extends Controller
{
    /**
     * Save an enquiry from the contact form as a lead.
     */
    public function store(EnquiryRequest $request, LeadNotifier $notifier): RedirectResponse
    {
        $data = $request->validated();

        $lead = Lead::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'event_date' => $data['date'] ?? null,
            'venue' => $data['venue'] ?? null,
            'message' => $data['message'] ?? null,
            'ip_address' => $request->ip(),
        ]);

        $notifier->notify($lead);

        return redirect()->to(route('home').'#contact')
            ->with('enquiry_sent', 'Thank you. We have your details and will write back within two days.');
    }
}
