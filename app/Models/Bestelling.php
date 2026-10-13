<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bestelling extends Model
{
    use SoftDeletes;

    protected $table = 'bestellingen';

    protected $fillable = [
        'pakket_id',
        'user_id',
        'naam',
        'email',
        'telefoon',
        'aantal',
        'gewenste_datum',
        'opmerking',
        'status',
    ];

    protected $casts = [
        'gewenste_datum' => 'date',
    ];

    public function pakket(): BelongsTo
    {
        return $this->belongsTo(Pakket::class);
    }
}