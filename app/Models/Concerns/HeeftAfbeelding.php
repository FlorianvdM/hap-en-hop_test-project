<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/** Een optionele afbeelding die op de schijf `public` staat. */
trait HeeftAfbeelding
{
    /** Het webadres van de afbeelding, of null als er nog geen is. */
    public function afbeeldingUrl(): ?string
    {
        return $this->afbeelding ? Storage::disk('public')->url($this->afbeelding) : null;
    }
}
