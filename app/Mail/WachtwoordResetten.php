<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WachtwoordResetten extends Mailable
{
    public function __construct(public string $url)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Stel een nieuw wachtwoord in voor Hap & Hop');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.wachtwoord-resetten');
    }
}