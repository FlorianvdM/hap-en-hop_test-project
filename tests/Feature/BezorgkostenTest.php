<?php

namespace Tests\Feature;

use App\Models\Bestelling;
use App\Models\Pakket;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BezorgkostenTest extends TestCase
{
    use RefreshDatabase;

    private array $adres = ['straat' => 'Kerkstraat 1', 'postcode' => '3511 AB', 'plaats' => 'Utrecht'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        config(['hapenhop.bezorging' => [
            'starttarief' => 1.50, 'per_km' => 0.75, 'max_km' => 10, 'route_sleutel' => null, 'omrijfactor' => 1.3,
        ]]);
    }

    public function test_bezorgkosten_worden_uitgerekend_met_starttarief_en_km(): void
    {
        // 3,3 km hemelsbreed x 1,3 = 4,3 km, naar boven afgerond 5 km: 1,50 + 5 x 0,75 = 5,25
        $this->getJson(route('bezorgkosten', $this->adres))
            ->assertOk()
            ->assertJson(['km' => 5, 'kosten' => 5.25, 'tekst' => '€ 5,25']);
    }

    public function test_te_ver_weg_wordt_geweigerd(): void
    {
        config(['hapenhop.bezorging.max_km' => 3]);

        $this->getJson(route('bezorgkosten', $this->adres))
            ->assertStatus(422)
            ->assertJsonPath('fout', fn ($fout) => str_contains($fout, 'We bezorgen tot 3 km'));

        // Ook bij het bestellen zelf wordt het geweigerd, en er komt geen bestelling
        $this->withSession(['winkelwagen' => [Pakket::firstOrFail()->id => 1]])
            ->post(route('bestellen.store'), $this->bestelGegevens())
            ->assertSessionHasErrors('straat');

        $this->assertSame(0, Bestelling::count());
    }

    public function test_postcode_die_niet_klopt_geeft_adres_niet_gevonden(): void
    {
        // De nep-PDOK geeft een adres met een andere postcode terug dan de klant invulde
        $this->pdokPostcode = '1234AB';

        $this->getJson(route('bezorgkosten', $this->adres))
            ->assertStatus(422)
            ->assertJsonPath('fout', fn ($fout) => str_contains($fout, 'niet vinden'));
    }

    public function test_met_sleutel_gebruiken_we_de_echte_route(): void
    {
        config(['hapenhop.bezorging.route_sleutel' => 'test-sleutel']);
        Http::fake(['api.openrouteservice.org/*' => Http::response([
            'features' => [['properties' => ['summary' => ['distance' => 7200]]]],
        ])]);

        // 7,2 km over de weg wordt 8 km: 1,50 + 8 x 0,75 = 7,50
        $this->getJson(route('bezorgkosten', $this->adres))
            ->assertOk()
            ->assertJson(['km' => 8, 'kosten' => 7.50]);
    }

    public function test_als_de_route_niet_lukt_rekenen_we_hemelsbreed(): void
    {
        config(['hapenhop.bezorging.route_sleutel' => 'test-sleutel']);
        Http::fake(['api.openrouteservice.org/*' => Http::response([], 500)]);

        $this->getJson(route('bezorgkosten', $this->adres))
            ->assertOk()
            ->assertJson(['km' => 5, 'kosten' => 5.25]);
    }

    public function test_bestelformulier_toont_de_tarieven(): void
    {
        $this->withSession(['winkelwagen' => [Pakket::firstOrFail()->id => 1]])
            ->get(route('bestellen.create'))
            ->assertOk()
            ->assertSee('€ 1,50 plus € 0,75 per km (tot 10 km)', false)
            ->assertSee('id="bezorg-info"', false);
    }

    private function bestelGegevens(): array
    {
        return $this->adres + [
            'naam' => 'Test', 'email' => 'test@example.com', 'levering' => 'bezorgen',
            'gewenste_datum' => now()->next('Thursday')->toDateString(), 'tijdslot' => config('hapenhop.tijdsloten')[0],
        ];
    }
}
