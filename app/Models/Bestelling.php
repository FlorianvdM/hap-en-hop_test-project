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