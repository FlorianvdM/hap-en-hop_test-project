<?php

namespace App\Mail;

use App\Models\Bestelling;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BestellingVerzonden extends Mailable
{
    use SerializesModels;

    public function __construct(public Bestelling $bestelling)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ($this->bestelling->wordtOpgehaald() ? 'Je bestelling staat klaar (#' : 'Je bestelling is onderweg (#') . $this->bestelling->id . ')',
            replyTo: [new Address(config('hapenhop.email'), config('hapenhop.naam'))],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.bestelling-verzonden');
    }
}
