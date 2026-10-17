<?php

namespace Tests\Feature;

use App\Mail\BestellingVerzonden;
use App\Models\Bestelling;
use App\Models\Pakket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class KeukenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@hapenhop.nl')->firstOrFail();
    }

    private function bestelling(string $naam, string $status, int $aantal, array $dieet = [], string $datum = '+3 days'): Bestelling
    {
        $pakket = Pakket::firstOrFail();

        $bestelling = Bestelling::create([
            'naam' => $naam,
            'email' => strtolower($naam) . '@example.com',
            'gewenste_datum' => now()->modify($datum),
            'status' => $status,
            'dieetwensen' => $dieet,
        ]);

        $bestelling->regels()->create([
            'pakket_id' => $pakket->id,
            'pakket_naam' => 'Testpakket',
            'aantal' => $aantal,
            'prijs' => 10,
        ]);

        return $bestelling;
    }

    public function test_keuken_telt_openstaande_bestellingen_per_pakket_en_toont_dieetwensen(): void
    {
        $this->bestelling('Anna', 'nieuw', 2, ['halal']);
        $this->bestelling('Bram', 'in behandeling', 3);
        $this->bestelling('Klaas', 'verzonden', 9);

        $this->actingAs($this->admin())
            ->get(route('admin.keuken'))
            ->assertOk()
            ->assertSee('<strong>5&times;</strong> Testpakket', false)
            ->assertSee('Halal')
            ->assertSee('Anna')
            ->assertDontSee('Klaas');
    }

    public function test_keuken_is_alleen_voor_admins(): void
    {
        $this->get(route('admin.keuken'))->assertRedirect();

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get(route('admin.keuken'))
            ->assertForbidden();
    }

    public function test_klant_krijgt_een_mail_bij_verzonden_maar_niet_bij_herhaling(): void
    {
        Mail::fake();
        $bestelling = $this->bestelling('Anna', 'in behandeling', 1);

        $this->actingAs($this->admin())
            ->patch(route('admin.bestellingen.status', $bestelling), ['status' => 'verzonden']);

        Mail::assertSent(BestellingVerzonden::class, fn ($mail) => $mail->hasTo('anna@example.com'));

        $this->actingAs($this->admin())
            ->patch(route('admin.bestellingen.status', $bestelling), ['status' => 'verzonden']);

        Mail::assertSent(BestellingVerzonden::class, 1);
    }

    public function test_mail_vermeldt_ophalen_of_bezorgen(): void
    {
        $ophalen = $this->bestelling('Anna', 'nieuw', 1);
        $ophalen->update(['levering' => 'ophalen']);
        $bezorgen = $this->bestelling('Bram', 'nieuw', 1);

        $this->assertStringContainsString('klaar om op te halen', (new BestellingVerzonden($ophalen))->render());
        $this->assertStringContainsString('verzonden en komt eraan', (new BestellingVerzonden($bezorgen))->render());
    }

    public function test_levering_is_verplicht_en_alleen_ophalen_of_bezorgen(): void
    {
        $pakket = Pakket::firstOrFail();
        $gegevens = [
            'naam' => 'Test', 'email' => 'test@example.com',
            'gewenste_datum' => now()->addDays(3)->toDateString(),
        ];

        $this->withSession(['winkelwagen' => [$pakket->id => 1]])
            ->post(route('bestellen.store'), $gegevens)
            ->assertSessionHasErrors('levering');

        $this->withSession(['winkelwagen' => [$pakket->id => 1]])
            ->post(route('bestellen.store'), $gegevens + ['levering' => 'drone'])
            ->assertSessionHasErrors('levering');

        $this->withSession(['winkelwagen' => [$pakket->id => 1]])
            ->post(route('bestellen.store'), $gegevens + ['levering' => 'ophalen'])
            ->assertSessionHasNoErrors();

        $this->assertSame('ophalen', Bestelling::firstOrFail()->levering);
    }

    public function test_bezorgen_vraagt_om_een_adres_en_ophalen_bewaart_er_geen(): void
    {
        $pakket = Pakket::firstOrFail();
        $gegevens = [
            'naam' => 'Test', 'email' => 'test@example.com',
            'gewenste_datum' => now()->addDays(3)->toDateString(),
        ];

        $this->withSession(['winkelwagen' => [$pakket->id => 1]])
            ->post(route('bestellen.store'), $gegevens + ['levering' => 'bezorgen'])
            ->assertSessionHasErrors(['straat', 'postcode', 'plaats']);

        $this->withSession(['winkelwagen' => [$pakket->id => 1]])
            ->post(route('bestellen.store'), $gegevens + [
                'levering' => 'bezorgen', 'straat' => 'Kerkstraat 1', 'postcode' => '3511 AB', 'plaats' => 'Utrecht',
            ])
            ->assertSessionHasNoErrors();

        $this->withSession(['winkelwagen' => [$pakket->id => 1]])
            ->post(route('bestellen.store'), $gegevens + [
                'levering' => 'ophalen', 'straat' => 'Nep 1', 'postcode' => '1', 'plaats' => 'Nep',
            ]);

        $this->assertSame('Kerkstraat 1', Bestelling::where('levering', 'bezorgen')->firstOrFail()->straat);
        $this->assertNull(Bestelling::where('levering', 'ophalen')->firstOrFail()->straat);
    }

    public function test_adresknop_staat_alleen_bij_bezorgen(): void
    {
        $bezorgen = $this->bestelling('Anna', 'nieuw', 1);
        $bezorgen->update(['levering' => 'bezorgen', 'straat' => 'Kerkstraat 1', 'postcode' => '3511 AB', 'plaats' => 'Utrecht']);
        $ophalen = $this->bestelling('Bram', 'nieuw', 1);
        $ophalen->update(['levering' => 'ophalen']);

        foreach (['admin.bestellingen', 'admin.keuken'] as $route) {
            $this->actingAs($this->admin())
                ->get(route($route))
                ->assertSee('Kerkstraat 1')
                ->assertSee('3511 AB Utrecht')
                ->assertSee('class="adres-details"', false);
        }

        $this->assertNull($ophalen->bezorgadres());
        $this->assertSame(1, substr_count(
            $this->actingAs($this->admin())->get(route('admin.keuken'))->getContent(),
            'class="adres-details"'
        ));
    }

    public function test_geen_mail_bij_andere_status(): void
    {
        Mail::fake();
        $bestelling = $this->bestelling('Anna', 'nieuw', 1);

        $this->actingAs($this->admin())
            ->patch(route('admin.bestellingen.status', $bestelling), ['status' => 'in behandeling']);

        Mail::assertNothingSent();
    }
}
