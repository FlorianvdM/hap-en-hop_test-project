<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Teamlid extends Model
{
    protected $table = 'teamleden';

    protected $fillable = [
        'naam',
        'functie',
        'biografie',
        'foto',
        'volgorde',
        'actief',
    ];

    protected $casts = [
        'actief' => 'boolean',
    ];

    /** Het webadres van de foto, of null als er nog geen foto is. */
    public function fotoUrl(): ?string
    {
        return $this->foto ? Storage::disk('public')->url($this->foto) : null;
    }
}
