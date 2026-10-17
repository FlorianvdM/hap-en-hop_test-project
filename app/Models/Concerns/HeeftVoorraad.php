<?php

namespace App\Models\Concerns;

/** Voorraad is een aantal; null betekent onbeperkt beschikbaar. */
trait HeeftVoorraad
{
    public function voorraadBeperkt(): bool
    {
        return $this->voorraad !== null;
    }

    public function uitverkocht(): bool
    {
        return $this->voorraadBeperkt() && $this->voorraad <= 0;
    }

    /** Hoeveel er maximaal besteld kan worden; null als er geen grens is. */
    public function maxBestelbaar(): ?int
    {
        return $this->voorraadBeperkt() ? max(0, (int) $this->voorraad) : null;
    }

    /** Korte tekst voor de website, of null als de voorraad onbeperkt is. */
    public function voorraadTekst(): ?string
    {
        if (! $this->voorraadBeperkt()) {
            return null;
        }

        return $this->uitverkocht() ? 'Uitverkocht' : "Nog {$this->voorraad} beschikbaar";
    }
}
