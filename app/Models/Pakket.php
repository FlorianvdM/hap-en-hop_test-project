<?php

namespace App\Models;

use App\Models\Concerns\HeeftAfbeelding;
use App\Models\Concerns\HeeftVoorraad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pakket extends Model
{
    use HeeftAfbeelding;
    use HeeftVoorraad;

    protected $table = 'pakketten';

    protected $fillable = [
        'naam',
        'beschrijving',
        'afbeelding',
        'prijs',
        'voorraad',
        'aantal_personen',
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];

    public function bestelregels(): HasMany
    {
        return $this->hasMany(Bestelregel::class);
    }
}