<?php

namespace App\Services;

use App\Mail\NewLeadMail;
use App\Models\Lead;
use App\Support\MailSettings;
use Illuminate\Support\Facades\Mail;
use Throwable;

class LeadNotifier
{
    /**
     * Email the admin about a new lead. A mail failure is logged and never
     * stops the visitor's enquiry from being saved.
     */
    public function notify(Lead $lead): bool
    {
        if (! MailSettings::notificationsEnabled() || ! ($recipients = MailSettings::recipients())) {
            return false;
        }

        try {
            MailSettings::apply();
            Mail::to($recipients)->send(new NewLeadMail($lead));

            return true;
        } catch (Throwable $e) {
            report($e);

            return false;
        }
    }
}
