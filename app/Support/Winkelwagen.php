<?php

namespace App\Support;

use App\Models\Pakket;
use Illuminate\Support\Collection;

class Winkelwagen
{
    private const SLEUTEL = 'winkelwagen';

    /** De inhoud in de sessie: [pakket_id => aantal] */
    public static function items(): array
    {
        return session(self::SLEUTEL, []);
    }

    public static function aantalStuks(): int
    {
        return array_sum(self::items());
    }

    public static function toevoegen(int $pakketId, int $aantal = 1): void
    {
        $items = self::items();
        $items[$pakketId] = min(($items[$pakketId] ?? 0) + $aantal, 100);

        session([self::SLEUTEL => $items]);
    }

    public static function wijzigen(int $pakketId, int $aantal): void
    {
        $items = self::items();

        if (isset($items[$pakketId])) {
            $items[$pakketId] = max(1, min($aantal, 100));
            session([self::SLEUTEL => $items]);
        }
    }

    public static function verwijderen(int $pakketId): void
    {
        $items = self::items();
        unset($items[$pakketId]);

        session([self::SLEUTEL => $items]);
    }

    public static function leegmaken(): void
    {
        session()->forget(self::SLEUTEL);
    }

    /**
     * De regels van de winkelwagen met de actuele pakketgegevens.
     * Pakketten die verborgen of verwijderd zijn, worden uit de winkelwagen gehaald.
     */
    public static function regels(): Collection
    {
        $items = self::items();

        if ($items === []) {
            return collect();
        }

        $pakketten = Pakket::whereIn('id', array_keys($items))
            ->where('actief', true)
            ->get()
            ->keyBy('id');

        $schoon = array_intersect_key($items, $pakketten->all());

        if (count($schoon) !== count($items)) {
            session([self::SLEUTEL => $schoon]);
        }

        return collect($schoon)->map(function ($aantal, $pakketId) use ($pakketten) {
            $pakket = $pakketten[$pakketId];

            return (object) [
                'pakket' => $pakket,
                'aantal' => $aantal,
                'totaal' => $aantal * $pakket->prijs,
            ];
        })->values();
    }

    public static function totaal(Collection $regels): float
    {
        return (float) $regels->sum('totaal');
    }
}