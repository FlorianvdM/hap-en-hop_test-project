<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'naam',
        'tekst',
        'beoordeling',
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];
}