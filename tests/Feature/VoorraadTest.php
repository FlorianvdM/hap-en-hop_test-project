<?php

namespace Tests\Feature;

use App\Models\Bestelling;
use App\Models\Pakket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VoorraadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Mail::fake();
    }

    private function pakketMetVoorraad(?int $voorraad): Pakket
    {
        $pakket = Pakket::first();
        $pakket->update(['voorraad' => $voorraad, 'actief' => true]);

        return $pakket->fresh();
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

    public function test_pakketkaart_toont_voorraad_en_uitverkocht(): void
    {
        $pakket = $this->pakketMetVoorraad(4);

        $this->get(route('pakketten.index'))->assertSee('Nog 4 beschikbaar');

        $pakket->update(['voorraad' => 0]);

        $this->get(route('pakketten.index'))->assertSee('Uitverkocht');
    }

    public function test_onbeperkte_voorraad_toont_niets(): void
    {
        $this->pakketMetVoorraad(null);

        $this->get(route('pakketten.index'))
            ->assertDontSee('beschikbaar')
            ->assertDontSee('Uitverkocht');
    }

    public function test_uitverkocht_pakket_kan_niet_in_de_winkelwagen(): void
    {
        $pakket = $this->pakketMetVoorraad(0);

        $this->post(route('winkelwagen.toevoegen', $pakket))->assertSessionHas('fout');

        $this->assertSame([], session('winkelwagen', []));
    }

    public function test_je_kunt_niet_meer_in_de_winkelwagen_dan_er_is(): void
    {
        $pakket = $this->pakketMetVoorraad(2);

        $this->post(route('winkelwagen.toevoegen', $pakket));
        $this->post(route('winkelwagen.toevoegen', $pakket));
        $this->post(route('winkelwagen.toevoegen', $pakket))->assertSessionHas('fout');

        $this->assertSame(2, session('winkelwagen')[$pakket->id]);

        $this->patch(route('winkelwagen.wijzigen', $pakket), ['aantal' => 5])->assertSessionHas('fout');

        $this->assertSame(2, session('winkelwagen')[$pakket->id]);
    }

    public function test_bestellen_boekt_de_voorraad_af(): void
    {
        $pakket = $this->pakketMetVoorraad(5);

        $this->withSession(['winkelwagen' => [$pakket->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens())
            ->assertRedirect(route('pakketten.index'));

        $this->assertSame(2, $pakket->fresh()->voorraad);
    }

    public function test_bestellen_met_te_weinig_voorraad_wordt_geweigerd(): void
    {
        $pakket = $this->pakketMetVoorraad(2);

        $this->withSession(['winkelwagen' => [$pakket->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens())
            ->assertRedirect(route('winkelwagen.index'))
            ->assertSessionHas('fout');

        $this->assertSame(2, $pakket->fresh()->voorraad);
        $this->assertSame(0, Bestelling::count());
    }

    public function test_onbeperkte_voorraad_blijft_onbeperkt_na_bestellen(): void
    {
        $pakket = $this->pakketMetVoorraad(null);

        $this->withSession(['winkelwagen' => [$pakket->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens())
            ->assertRedirect(route('pakketten.index'));

        $this->assertNull($pakket->fresh()->voorraad);
    }

    public function test_verwijderen_van_niet_verzonden_bestelling_zet_voorraad_terug(): void
    {
        $pakket = $this->pakketMetVoorraad(5);

        $this->withSession(['winkelwagen' => [$pakket->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens());

        $bestelling = Bestelling::firstOrFail();
        $admin = User::where('email', 'admin@hapenhop.nl')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.bestellingen.verwijderen', $bestelling))
            ->assertRedirect();

        $this->assertSame(5, $pakket->fresh()->voorraad);
    }

    public function test_verwijderen_van_verzonden_bestelling_zet_voorraad_niet_terug(): void
    {
        $pakket = $this->pakketMetVoorraad(5);

        $this->withSession(['winkelwagen' => [$pakket->id => 3]])
            ->post(route('bestellen.store'), $this->bestelgegevens());

        $bestelling = Bestelling::firstOrFail();
        $bestelling->update(['status' => 'verzonden']);
        $admin = User::where('email', 'admin@hapenhop.nl')->firstOrFail();

        $this->actingAs($admin)
            ->delete(route('admin.bestellingen.verwijderen', $bestelling))
            ->assertRedirect();

        $this->assertSame(2, $pakket->fresh()->voorraad);
    }

    public function test_beheer_kan_voorraad_instellen_en_leeg_laten(): void
    {
        $admin = User::where('email', 'admin@hapenhop.nl')->firstOrFail();
        $pakket = Pakket::first();
        $velden = [
            'naam' => $pakket->naam,
            'prijs' => $pakket->prijs,
            'actief' => 1,
        ];

        $this->actingAs($admin)
            ->put(route('admin.pakketten.update', $pakket), $velden + ['voorraad' => 7])
            ->assertRedirect();
        $this->assertSame(7, $pakket->fresh()->voorraad);

        $this->actingAs($admin)
            ->put(route('admin.pakketten.update', $pakket), $velden + ['voorraad' => ''])
            ->assertRedirect();
        $this->assertNull($pakket->fresh()->voorraad);

        $this->actingAs($admin)
            ->put(route('admin.pakketten.update', $pakket), $velden + ['voorraad' => -1])
            ->assertSessionHasErrors('voorraad');
    }
}
