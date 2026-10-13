<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactBericht extends Model
{
    protected $table = 'contact_berichten';

    protected $fillable = [
        'naam',
        'email',
        'bericht',
        'gelezen',
    ];

    protected $casts = [
        'gelezen' => 'boolean',
    ];
}