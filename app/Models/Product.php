<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'producten';

    protected $fillable = [
        'naam',
        'beschrijving',
        'prijs',
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];
}