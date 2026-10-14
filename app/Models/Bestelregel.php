<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bestelregel extends Model
{
    protected $fillable = [
        'pakket_id',
        'pakket_naam',
        'aantal',
        'prijs',
    ];

    public function bestelling(): BelongsTo
    {
        return $this->belongsTo(Bestelling::class)->withTrashed();
    }

    public function pakket(): BelongsTo
    {
        return $this->belongsTo(Pakket::class);
    }

    protected function totaal(): Attribute
    {
        return Attribute::get(fn () => $this->aantal * $this->prijs);
    }
}