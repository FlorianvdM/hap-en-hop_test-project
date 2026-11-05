<?php

namespace Tests\Feature;

use App\Models\Bestelling;
use App\Models\ContactBericht;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExtraPaginasTest extends TestCase
{
    use RefreshDatabase;

    private function aanvraag(array $extra = []): array
    {
        return $extra + [
            'naam' => 'Fatima',
            'email' => 'fatima@example.com',
            'datum' => now()->addWeek()->toDateString(),
            'personen' => 25,
            'gelegenheid' => 'werk',
            'wensen' => 'Graag halal',
        ];
    }

    public function test_contactformulier_slaat_spam_van_bots_niet_op(): void
    {
        $bericht = ['naam' => 'Bot', 'email' => 'bot@example.com', 'bericht' => 'Koop goedkope horloges hier'];

        $this->post(route('contact.store'), $bericht + ['website' => 'http://spam.example'])
            ->assertRedirect(route('contact'))
            ->assertSessionHas('succes');
        $this->assertSame(0, ContactBericht::count());

        $this->post(route('contact.store'), $bericht + ['website' => '']);
        $this->assertSame(1, ContactBericht::count());
    }

    public function test_cateringaanvraag_komt_binnen_als_bericht(): void
    {
        $this->post(route('catering.store'), $this->aanvraag())
            ->assertRedirect(route('catering'))
            ->assertSessionHasNoErrors();

        $bericht = ContactBericht::sole();
        $this->assertStringContainsString('Cateringaanvraag', $bericht->bericht);
        $this->assertStringContainsString('Aantal personen: 25', $bericht->bericht);
        $this->assertStringContainsString('Lunch op het werk', $bericht->bericht);
    }

    public function test_cateringaanvraag_controleert_de_invoer_en_spam(): void
    {
        $this->post(route('catering.store'), $this->aanvraag(['personen' => 0, 'gelegenheid' => 'ruimtevaart', 'datum' => now()->toDateString()]))
            ->assertSessionHasErrors(['personen', 'gelegenheid', 'datum']);

        $this->post(route('catering.store'), $this->aanvraag(['website' => 'x']));
        $this->assertSame(0, ContactBericht::count());
    }

    public function test_beschrijving_sitemap_vragen_en_route(): void
    {
        $this->get(route('catering'))->assertSee('<meta name="description" content="Catering op maat', false);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>' . route('vragen') . '</loc>', false);

        $this->get(route('vragen'))->assertOk()->assertSee('Wanneer kan ik broodjes bestellen?');
        $this->get(route('home'))->assertSee(route('vragen'), false);
        $this->get(route('contact'))->assertSee('Route plannen');
    }

    public function test_klant_verwijdert_eigen_account_en_bestelling_blijft(): void
    {
        $klant = User::factory()->create(['password' => Hash::make('geheim-1234'), 'is_admin' => false]);
        $bestelling = Bestelling::create(['user_id' => $klant->id, 'naam' => 'Klant', 'email' => $klant->email,
            'gewenste_datum' => now()->addDay(), 'status' => 'nieuw']);

        $this->actingAs($klant)->get(route('mijn-bestellingen'))->assertSee('Account verwijderen');

        $this->actingAs($klant)->delete(route('account.verwijderen'), ['wachtwoord' => 'fout'])
            ->assertSessionHasErrors('wachtwoord');
        $this->assertModelExists($klant);

        $this->actingAs($klant)->delete(route('account.verwijderen'), ['wachtwoord' => 'geheim-1234'])
            ->assertRedirect(route('home'));

        $this->assertModelMissing($klant);
        $this->assertGuest();
        $this->assertNull($bestelling->fresh()->user_id);
    }

    public function test_beheerder_kan_eigen_account_niet_zo_verwijderen(): void
    {
        $admin = User::factory()->create(['password' => Hash::make('geheim-1234'), 'is_admin' => true]);

        $this->actingAs($admin)->delete(route('account.verwijderen'), ['wachtwoord' => 'geheim-1234'])
            ->assertRedirect(route('mijn-bestellingen'))
            ->assertSessionHas('fout');

        $this->assertModelExists($admin);
    }
}
