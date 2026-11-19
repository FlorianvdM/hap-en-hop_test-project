<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    // Coördinaten (lengtegraad, breedtegraad) die de nep-PDOK teruggeeft in de tests
    public const HAP_EN_HOP = [5.15, 52.02];

    public const KLANT = [5.15, 52.05]; // ongeveer 3,3 km naar het noorden

    // Zet een test dit op bijvoorbeeld '1234AB', dan geeft de nep-PDOK een adres met die (verkeerde) postcode terug
    protected ?string $pdokPostcode = null;

    protected function setUp(): void
    {
        parent::setUp();

        // Tests mogen nooit echt het internet op. PDOK geven we een nep-antwoord:
        // het adres van Hap & Hop (postcode 3991) ligt op HAP_EN_HOP, elk ander adres op KLANT.
        Http::preventStrayRequests();
        Http::fake([
            'api.pdok.nl/*' => function (Request $request) {
                $zoek = $request->data()['q'] ?? '';
                preg_match('/\d{4}\s?[A-Za-z]{2}/', $zoek, $postcode);
                [$lon, $lat] = str_contains($zoek, '3991') ? self::HAP_EN_HOP : self::KLANT;

                return Http::response(['response' => ['docs' => [[
                    'postcode' => $this->pdokPostcode ?? strtoupper(str_replace(' ', '', $postcode[0] ?? '')),
                    'centroide_ll' => "POINT({$lon} {$lat})",
                ]]]]);
            },
        ]);
    }
}
