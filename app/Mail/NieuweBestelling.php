<?php

namespace App\Mail;

use App\Models\Bestelling;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NieuweBestelling extends Mailable
{
    use SerializesModels;

    public function __construct(public Bestelling $bestelling)
    {
    }

    public function envelope(): Envelope
    {
        // Eventuele regeleinden uit de naam halen, zodat de naam nooit de e-mailkop kan beïnvloeden
        $naam = trim(preg_replace('/\s+/', ' ', $this->bestelling->naam));

        return new Envelope(
            subject: 'Nieuwe bestelling #' . $this->bestelling->id . ' van ' . $naam,
            // Antwoorden gaan rechtstreeks naar de klant
            replyTo: [new Address($this->bestelling->email, $naam)],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.nieuwe-bestelling');
    }
}