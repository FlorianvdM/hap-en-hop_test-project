<?php

namespace Tests\Feature;

use App\Models\Bestelling;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BroodjesBestellenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Mail::fake();

        // Geen wachttijd tussen de mails, anders duurt elke bestel-test 11 seconden
        config(['hapenhop.mail_pauze' => 0]);
    }

    /** Maakt een bestelbaar broodje, met een eigen voorraad. */
    private function broodje(?int $voorraad = null, bool $bestelbaar = true): Product
    {
        return Product::create([
            'naam' => 'Broodje Testbal',
            'beschrijving' => 'Alleen voor de test',
            'prijs' => 4.00,
            'voorraad' => $voorraad,
            'bestelbaar' => $bestelbaar,
            'actief' => true,
        ]);
    }

    private function bestelgegevens(): array
    {
        return [
            'naam' => 'Test Klant',
            'email' => 'klant@example.com',
            'gewenste_datum' => now()->addDays(3)->toDateString(),
            'levering' => 'ophalen',
        ];
    }

    public function test_bestelbaar_broodje_heeft_een_bestelknop(): void
    {
        $this->broodje();

        $this->get(route('producten'))
            ->assertOk()
            ->assertSee('Broodje Testbal')
            ->assertSee('In winkelwagen');
    }

    public function test_product_dat_niet_bestelbaar_is_heeft_geen_bestelknop(): void
    {
        $this->broodje(null, false);

        $this->get(route('producten'))
            ->assertSee('Broodje Testbal')
            ->assertDontSee('In winkelwagen');
    }

    public function test_broodje_komt_in_de_winkelwagen(): void
    {
        $broodje = $this->broodje();

        $this->post(route('winkelwagen.product.toevoegen', $broodje))->assertSessionHas('succes');

        // Een product staat in de sessie onder "p" + het product-id
        $this->assertSame(1, session('winkelwagen')['p' . $broodje->id]);
    }

    public function test_product_dat_niet_bestelbaar_is_kan_niet_in_de_winkelwagen(): void
    {
        $product = $this->broodje(null, false);

        $this->post(route('winkelwagen.product.toevoegen', $product))->assertNotFound();

        $this->assertSame([], session('winkelwagen', []));
    }

    public function test_bestellen_boekt_de_voorraad_van_het_broodje_af(): void
    {
        $broodje = $this->broodje(5);

        $this->withSession(['winkelwagen' => ['p' . $broodje->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens())
            ->assertRedirect(route('pakketten.index'));

        $this->assertSame(2, $broodje->fresh()->voorraad);

        // De bestelregel hoort bij het product en niet bij een pakket
        $this->assertDatabaseHas('bestelregels', [
            'product_id' => $broodje->id,
            'pakket_id' => null,
            'pakket_naam' => 'Broodje Testbal',
            'aantal' => 3,
        ]);
    }

    public function test_bestellen_met_te_weinig_voorraad_wordt_geweigerd(): void
    {
        $broodje = $this->broodje(2);

        $this->withSession(['winkelwagen' => ['p' . $broodje->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens())
            ->assertRedirect(route('winkelwagen.index'))
            ->assertSessionHas('fout');

        $this->assertSame(2, $broodje->fresh()->voorraad);
        $this->assertSame(0, Bestelling::count());
    }

    public function test_verwijderen_van_niet_verzonden_bestelling_zet_voorraad_van_broodje_terug(): void
    {
        $broodje = $this->broodje(5);

        $this->withSession(['winkelwagen' => ['p' . $broodje->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens());

        $admin = User::where('email', 'admin@hapenhop.nl')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.bestellingen.verwijderen', Bestelling::firstOrFail()))
            ->assertRedirect();

        $this->assertSame(5, $broodje->fresh()->voorraad);
    }

    public function test_broodje_verdwijnt_uit_de_winkelwagen_als_het_niet_meer_bestelbaar_is(): void
    {
        $broodje = $this->broodje();

        $this->withSession(['winkelwagen' => ['p' . $broodje->id => 2]]);

        $broodje->update(['bestelbaar' => false]);

        $this->get(route('winkelwagen.index'))->assertOk()->assertSee('Je winkelwagen is leeg.');

        $this->assertSame([], session('winkelwagen', []));
    }

    public function test_product_met_bestellingen_kan_niet_worden_verwijderd(): void
    {
        $broodje = $this->broodje();

        $this->withSession(['winkelwagen' => ['p' . $broodje->id => 1]])
            ->post(route('bestellen.store'), $this->bestelgegevens());

        $admin = User::where('email', 'admin@hapenhop.nl')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.producten.destroy', $broodje))
            ->assertSessionHas('fout');

        $this->assertDatabaseHas('producten', ['id' => $broodje->id]);
    }
}