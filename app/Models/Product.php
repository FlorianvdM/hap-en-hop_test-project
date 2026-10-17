<?php

namespace App\Models;

use App\Models\Concerns\HeeftAfbeelding;
use App\Models\Concerns\HeeftVoorraad;
use Illuminate\Database\Eloquent\Model;

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
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];
}