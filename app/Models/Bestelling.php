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
        'dieetwensen',
        'opmerking',
        'status',
    ];

    protected $casts = [
        'gewenste_datum' => 'date',
        'dieetwensen' => 'array',
    ];

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