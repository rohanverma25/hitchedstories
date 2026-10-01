<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewLeadMail extends Mailable
{
    public function __construct(public Lead $lead)
    {
    }

    /**
     * Replying goes straight to the couple.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->lead->email, $this->lead->name)],
            subject: 'New enquiry from '.$this->lead->name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.new-lead');
    }
}
