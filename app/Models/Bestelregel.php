<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bestelregel extends Model
{
    // pakket_naam bevat de naam van het pakket of product op het moment van bestellen
    protected $fillable = [
        'pakket_id',
        'product_id',
        'pakket_naam',
        'aantal',
        'prijs',
    ];

    public function bestelling(): BelongsTo
    {
        return $this->belongsTo(Bestelling::class)->withTrashed();
    }

    /** Alleen gevuld als de regel een pakket is */
    public function pakket(): BelongsTo
    {
        return $this->belongsTo(Pakket::class);
    }

    /** Alleen gevuld als de regel een product (bijvoorbeeld een broodje) is */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function totaal(): Attribute
    {
        return Attribute::get(fn () => $this->aantal * $this->prijs);
    }
}