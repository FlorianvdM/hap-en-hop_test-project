<?php

namespace App\Models;

use App\Models\Concerns\HeeftAfbeelding;
use App\Models\Concerns\HeeftVoorraad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HeeftAfbeelding;
    use HeeftVoorraad;

    protected $table = 'producten';

    protected $fillable = [
        'naam',
        'beschrijving',
        'afbeelding',
        'prijs',
        'voorraad',
        'bestelbaar', // true = klanten kunnen dit product in de winkelwagen leggen
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
        'bestelbaar' => 'boolean',
    ];

    /** De bestelregels waarop dit product voorkomt */
    public function bestelregels(): HasMany
    {
        return $this->hasMany(Bestelregel::class);
    }
}