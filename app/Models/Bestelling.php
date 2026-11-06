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
        'bezorgkosten',
        'dieetwensen',
        'opmerking',
        'status',
    ];

    protected $casts = [
        'gewenste_datum' => 'date',
        'bezorgkosten' => 'decimal:2',
        'dieetwensen' => 'array',
    ];

    /**
     * Alle statussen. De keuken zet een bestelling op nieuw, in behandeling of klaar;
     * "verzonden" kan pas daarna, met de knop bij Bestellingen. Alleen "verzonden" telt (na verwijderen) mee in de omzet.
     */
    public const STATUSSEN = [
        'nieuw' => 'Nieuw',
        'in behandeling' => 'In behandeling',
        'klaar' => 'Klaar',
        'verzonden' => 'Verzonden',
    ];

    /** De statussen die de keuken kan kiezen. */
    public const KEUKENSTATUSSEN = ['nieuw', 'in behandeling', 'klaar'];

    /** De naam van een status voor deze bestelling, met het verschil tussen ophalen en bezorgen. */
    public function statusNaam(?string $status = null): string
    {
        $status ??= $this->status;

        return match ($status) {
            'klaar' => $this->wordtOpgehaald() ? 'Klaar om op te halen' : 'Klaar voor bezorging',
            'verzonden' => $this->wordtOpgehaald() ? 'Opgehaald' : 'Verzonden',
            default => self::STATUSSEN[$status] ?? ucfirst($status),
        };
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
            fn () => $this->regels->sum(fn ($regel) => $regel->aantal * $regel->prijs) + (float) $this->bezorgkosten
        );
    }
}