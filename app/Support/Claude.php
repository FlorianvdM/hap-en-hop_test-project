<?php

namespace App\Support;

use Anthropic\Client;
use RuntimeException;

class Claude
{
    /** Is er een API-sleutel ingesteld? */
    public function beschikbaar(): bool
    {
        return filled(config('services.anthropic.key'));
    }

    /** Stuurt één vraag naar Claude en geeft de tekst van het antwoord terug. */
    public function vraag(string $systeem, string $gebruiker): string
    {
        if (! $this->beschikbaar()) {
            throw new RuntimeException('Er is geen API-sleutel ingesteld.');
        }

        $client = new Client(apiKey: config('services.anthropic.key'));

        $antwoord = $client->messages->create(
            model: config('services.anthropic.model'),
            maxTokens: 8000,
            outputConfig: ['effort' => 'low'],
            system: $systeem,
            messages: [
                ['role' => 'user', 'content' => $gebruiker],
            ],
        );

        foreach ($antwoord->content as $blok) {
            if ($blok->type === 'text') {
                return trim($blok->text);
            }
        }

        throw new RuntimeException('Het model gaf geen tekst terug.');
    }
}
