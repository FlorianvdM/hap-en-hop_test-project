<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bestelling extends Model
{
    use SoftDeletes;

    protected $table = 'bestellingen';

    protected $fillable = [
        'user_id',
        'naam',
        'email',
        'telefoon',
        'gewenste_datum',
        'levering',
        'straat',
        'postcode',
        'plaats',
        'dieetwensen',
        'opmerking',
        'status',
    ];

    protected $casts = [
        'gewenste_datum' => 'date',
        'dieetwensen' => 'array',
    ];

    /**
     * Alle statussen met hun naam in het beheer. "Klaar" is een tussenstap voor bestellingen die worden opgehaald;
     * alleen "verzonden" telt (na verwijderen) mee in de omzet.
     */
    public const STATUSSEN = [
        'nieuw' => 'Nieuw',
        'in behandeling' => 'In behandeling',
        'klaar' => 'Klaar om op te halen',
        'verzonden' => 'Verzonden',
    ];

    /** De statussen die bij deze bestelling passen: "klaar" alleen als de klant ophaalt. */
    public function mogelijkeStatussen(): array
    {
        return $this->wordtOpgehaald()
            ? self::STATUSSEN
            : array_diff_key(self::STATUSSEN, ['klaar' => true]);
    }

    public function wordtOpgehaald(): bool
    {
        return $this->levering === 'ophalen';
    }

    /** Het bezorgadres op twee regels, of null als er niet bezorgd wordt of geen adres is. */
    public function bezorgadres(): ?string
    {
        if ($this->wordtOpgehaald() || ! $this->straat) {
            return null;
        }

        return $this->straat . "
" . trim($this->postcode . ' ' . $this->plaats);
    }

    public function leveringTekst(): string
    {
        return $this->wordtOpgehaald() ? 'Ophalen' : 'Bezorgen';
    }

    public function regels(): HasMany
    {
        return $this->hasMany(Bestelregel::class);
    }

    protected function totaal(): Attribute
    {
        return Attribute::get(
            fn () => $this->regels->sum(fn ($regel) => $regel->aantal * $regel->prijs)
        );
    }
}