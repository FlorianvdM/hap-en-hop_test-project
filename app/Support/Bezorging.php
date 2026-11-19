<?php

namespace App\Support;

use DomainException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Berekent de bezorgkosten op basis van de afstand tussen Hap & Hop en het bezorgadres.
 *
 * Stap 1: adres omzetten naar coördinaten met de gratis PDOK Locatieserver (Kadaster, alleen Nederland).
 * Stap 2: afstand berekenen. Met een sleutel van openrouteservice de echte route over de weg,
 *         anders hemelsbreed x de omrijfactor.
 * Stap 3: kosten = starttarief + km x prijs per km.
 */
class Bezorging
{
    private const PDOK_URL = 'https://api.pdok.nl/bzk/locatieserver/search/v3_1/free';

    private const ROUTE_URL = 'https://api.openrouteservice.org/v2/directions/driving-car';

    /**
     * Geeft ['km' => 4, 'kosten' => 4.50] terug.
     * Gooit een DomainException met een nette melding voor de klant als het niet lukt.
     */
    public static function bereken(string $straat, string $postcode, string $plaats): array
    {
        $instellingen = config('hapenhop.bezorging');

        $start = self::coordinaten(config('hapenhop.adres'));
        $doel = self::coordinaten("{$straat}, {$postcode} {$plaats}", $postcode);

        // Ons eigen adres hoort altijd te bestaan; lukt dat niet, dan is er iets mis met de instellingen
        if (! $start) {
            throw new DomainException('We kunnen de bezorgkosten nu niet berekenen. Probeer het later opnieuw of kies voor ophalen.');
        }

        if (! $doel) {
            throw new DomainException('We kunnen dit adres niet vinden. Controleer de straat, het huisnummer en de postcode.');
        }

        // Altijd minstens 1 km, en altijd naar boven afronden (3,2 km wordt 4 km)
        $km = max(1, (int) ceil(self::afstand($start, $doel)));

        if ($km > $instellingen['max_km']) {
            throw new DomainException("We bezorgen tot {$instellingen['max_km']} km. Jouw adres ligt op ongeveer {$km} km, maar je kunt de bestelling wel zelf ophalen.");
        }

        return [
            'km' => $km,
            'kosten' => round($instellingen['starttarief'] + $km * $instellingen['per_km'], 2),
        ];
    }

    /** Korte uitleg van de tarieven, voor op de website. Bijvoorbeeld: "€ 1,50 plus € 0,75 per km (tot 10 km)". */
    public static function uitleg(): string
    {
        $instellingen = config('hapenhop.bezorging');

        return '€ ' . number_format($instellingen['starttarief'], 2, ',', '.')
            . ' plus € ' . number_format($instellingen['per_km'], 2, ',', '.')
            . " per km (tot {$instellingen['max_km']} km)";
    }

    /**
     * Zoekt de coördinaten [lengtegraad, breedtegraad] van een adres.
     * Geeft null als het adres niet bestaat, of als de postcode niet klopt bij het gevonden adres.
     * We onthouden het antwoord een dag, zodat we PDOK niet steeds opnieuw vragen.
     */
    private static function coordinaten(string $adres, ?string $postcode = null): ?array
    {
        return Cache::remember('adres:' . md5(strtolower($adres)), now()->addDay(), function () use ($adres, $postcode) {
            try {
                $antwoord = Http::timeout(5)->get(self::PDOK_URL, [
                    'q' => $adres,
                    'fq' => 'type:adres',
                    'rows' => 1,
                    'fl' => 'postcode,centroide_ll',
                ]);
            } catch (Throwable $e) {
                report($e);
                throw new DomainException('We kunnen de bezorgkosten nu niet berekenen. Probeer het later opnieuw of kies voor ophalen.');
            }

            if ($antwoord->failed()) {
                throw new DomainException('We kunnen de bezorgkosten nu niet berekenen. Probeer het later opnieuw of kies voor ophalen.');
            }

            $gevonden = $antwoord->json('response.docs.0');

            if (! $gevonden) {
                return null;
            }

            // PDOK zoekt "ongeveer". Klopt de postcode niet, dan is het waarschijnlijk een ander adres.
            if ($postcode && self::schoon($gevonden['postcode'] ?? '') !== self::schoon($postcode)) {
                return null;
            }

            // Het antwoord ziet eruit als "POINT(5.1506 52.0201)"
            preg_match('/POINT\(([\d.]+) ([\d.]+)\)/', $gevonden['centroide_ll'] ?? '', $delen);

            return $delen ? [(float) $delen[1], (float) $delen[2]] : null;
        });
    }

    /** Afstand in km. Eerst de echte route proberen, anders hemelsbreed x de omrijfactor. */
    private static function afstand(array $start, array $doel): float
    {
        $sleutel = config('hapenhop.bezorging.route_sleutel');

        if ($sleutel) {
            try {
                $antwoord = Http::timeout(5)
                    ->withHeaders(['Authorization' => $sleutel])
                    ->get(self::ROUTE_URL, [
                        'start' => "{$start[0]},{$start[1]}",
                        'end' => "{$doel[0]},{$doel[1]}",
                    ]);

                $meters = $antwoord->json('features.0.properties.summary.distance');

                if ($antwoord->successful() && $meters !== null) {
                    return $meters / 1000;
                }
            } catch (Throwable $e) {
                // Lukt de route niet, dan gaan we hieronder verder met hemelsbreed
                report($e);
            }
        }

        return self::hemelsbreed($start, $doel) * config('hapenhop.bezorging.omrijfactor');
    }

    /** Afstand in een rechte lijn in km (haversine-formule: rekent met de ronding van de aarde). */
    private static function hemelsbreed(array $start, array $doel): float
    {
        [$lon1, $lat1] = array_map('deg2rad', $start);
        [$lon2, $lat2] = array_map('deg2rad', $doel);

        $a = sin(($lat2 - $lat1) / 2) ** 2 + cos($lat1) * cos($lat2) * sin(($lon2 - $lon1) / 2) ** 2;

        return 6371 * 2 * asin(sqrt($a));
    }

    /** "3511 ab" en "3511AB" worden allebei "3511AB" */
    private static function schoon(string $postcode): string
    {
        return strtoupper(str_replace(' ', '', $postcode));
    }
}