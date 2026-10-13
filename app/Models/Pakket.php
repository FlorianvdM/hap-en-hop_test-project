<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pakket extends Model
{
    protected $table = 'pakketten';

    protected $fillable = [
        'naam',
        'beschrijving',
        'prijs',
        'aantal_personen',
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];

    public function bestellingen(): HasMany
    {
        return $this->hasMany(Bestelling::class);
    }
}