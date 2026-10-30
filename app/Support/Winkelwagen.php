<?php

namespace App\Support;

use App\Models\Pakket;
use App\Models\Product;
use Illuminate\Support\Collection;

class Winkelwagen
{
    private const SLEUTEL = 'winkelwagen';

    /**
     * De inhoud in de sessie: [sleutel => aantal]
     * - Een pakket heeft als sleutel het pakket-id (een getal, bijvoorbeeld 3).
     * - Een product (bijvoorbeeld een broodje) heeft als sleutel "p" + het product-id (bijvoorbeeld "p5").
     */
    public static function items(): array
    {
        return session(self::SLEUTEL, []);
    }

    public static function aantalStuks(): int
    {
        return array_sum(self::items());
    }

    /** De sleutel van een product in de winkelwagen */
    public static function productSleutel(int $productId): string
    {
        return 'p' . $productId;
    }

    public static function toevoegen(int|string $sleutel, int $aantal = 1): void
    {
        $items = self::items();
        $items[$sleutel] = min(($items[$sleutel] ?? 0) + $aantal, 100);

        session([self::SLEUTEL => $items]);
    }

    public static function wijzigen(int|string $sleutel, int $aantal): void
    {
        $items = self::items();

        if (isset($items[$sleutel])) {
            $items[$sleutel] = max(1, min($aantal, 100));
            session([self::SLEUTEL => $items]);
        }
    }

    public static function verwijderen(int|string $sleutel): void
    {
        $items = self::items();
        unset($items[$sleutel]);

        session([self::SLEUTEL => $items]);
    }

    public static function leegmaken(): void
    {
        session()->forget(self::SLEUTEL);
    }

    /**
     * De regels van de winkelwagen met de actuele gegevens van pakketten en producten.
     * Pakketten en producten die verborgen, verwijderd of niet (meer) bestelbaar zijn,
     * worden uit de winkelwagen gehaald.
     */
    public static function regels(): Collection
    {
        $items = self::items();

        if ($items === []) {
            return collect();
        }

        // Sorteer de sleutels in pakket-id's en product-id's
        $pakketIds = [];
        $productIds = [];

        foreach (array_keys($items) as $sleutel) {
            if (is_int($sleutel)) {
                $pakketIds[] = $sleutel;
            } else {
                $productIds[] = (int) substr($sleutel, 1);
            }
        }

        $pakketten = Pakket::whereIn('id', $pakketIds)
            ->where('actief', true)
            ->get()
            ->keyBy('id');

        $producten = Product::whereIn('id', $productIds)
            ->where('actief', true)
            ->where('bestelbaar', true)
            ->get()
            ->keyBy('id');

        $regels = collect();
        $schoon = [];

        foreach ($items as $sleutel => $aantal) {
            if (is_int($sleutel)) {
                $soort = 'pakket';
                $item = $pakketten->get($sleutel);
            } else {
                $soort = 'product';
                $item = $producten->get((int) substr($sleutel, 1));
            }

            // Bestaat het niet meer of is het verborgen? Dan laten we het weg.
            if (!$item) {
                continue;
            }

            $schoon[$sleutel] = $aantal;

            $regels->push((object) [
                'sleutel' => $sleutel,
                'soort' => $soort,          // 'pakket' of 'product'
                'item' => $item,            // het Pakket of Product zelf
                'aantal' => $aantal,
                'totaal' => $aantal * $item->prijs,
            ]);
        }

        // Is er iets weggehaald? Dan bewaren we de opgeschoonde winkelwagen.
        if (count($schoon) !== count($items)) {
            session([self::SLEUTEL => $schoon]);
        }

        return $regels;
    }

    public static function totaal(Collection $regels): float
    {
        return (float) $regels->sum('totaal');
    }
}