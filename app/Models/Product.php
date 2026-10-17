<?php

namespace App\Models;

use App\Models\Concerns\HeeftVoorraad;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HeeftVoorraad;

    protected $table = 'producten';

    protected $fillable = [
        'naam',
        'beschrijving',
        'prijs',
        'voorraad',
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];
}