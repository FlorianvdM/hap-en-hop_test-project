<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Afbeeldingen
{
    public const REGEL = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

    /**
     * Slaat een geüploade afbeelding op en zet het pad in de gegevens.
     * Zonder upload blijft de oude afbeelding staan; bij een nieuwe upload wordt de oude verwijderd.
     */
    public static function opslaan(Request $request, array $data, ?string $oud, string $map): array
    {
        unset($data['afbeelding']);

        if ($request->hasFile('afbeelding')) {
            self::verwijderen($oud);
            $data['afbeelding'] = $request->file('afbeelding')->store($map, 'public');
        }

        return $data;
    }

    public static function verwijderen(?string $pad): void
    {
        if ($pad) {
            Storage::disk('public')->delete($pad);
        }
    }
}
