<?php

namespace Tests\Feature;

use App\Mail\BestellingVerzonden;
use App\Models\Bestelling;
use App\Models\ContactBericht;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BeheerLijstenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Mail::fake();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@hapenhop.nl')->firstOrFail();
    }

    private function bestelling(array $extra = []): Bestelling
    {
        $bestelling = Bestelling::create($extra + [
            'naam' => 'Anna',
            'email' => 'anna@example.com',
            'gewenste_datum' => now()->addDays(3),
            'levering' => 'ophalen',
            'status' => 'nieuw',
        ]);

        $bestelling->regels()->create(['pakket_naam' => 'Broodje kaas', 'aantal' => 2, 'prijs' => 4]);

        return $bestelling;
    }

    public function test_klaar_om_op_te_halen_mailt_de_klant_een_keer(): void
    {
        $bestelling = $this->bestelling();

        $this->actingAs($this->admin())->patch(route('admin.bestellingen.status', $bestelling), ['status' => 'klaar'])
            ->assertSessionHas('succes');
        Mail::assertSent(BestellingVerzonden::class, 1);

        // Daarna op verzonden zetten (opgehaald): geen tweede mail
        $this->actingAs($this->admin())->patch(route('admin.bestellingen.status', $bestelling), ['status' => 'verzonden']);
        Mail::assertSent(BestellingVerzonden::class, 1);
        $this->assertSame('verzonden', $bestelling->fresh()->status);
    }

    public function test_klaar_kan_niet_bij_een_bestelling_die_wordt_bezorgd(): void
    {
        $bestelling = $this->bestelling(['levering' => 'bezorgen', 'straat' => 'Dorpsstraat 1', 'postcode' => '3991 AA', 'plaats' => 'Houten']);

        $this->actingAs($this->admin())->patch(route('admin.bestellingen.status', $bestelling), ['status' => 'klaar'])
            ->assertSessionHasErrors('status');
        $this->assertSame('nieuw', $bestelling->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_klaar_telt_niet_mee_in_de_omzet_en_wordt_definitief_verwijderd(): void
    {
        $bestelling = $this->bestelling(['status' => 'klaar']);

        $this->actingAs($this->admin())->delete(route('admin.bestellingen.verwijderen', $bestelling));

        $this->assertNull(Bestelling::withTrashed()->find($bestelling->id));
    }

    public function test_bestellingen_zoeken_filteren_en_pagina(): void
    {
        $anna = $this->bestelling();
        $this->bestelling(['naam' => 'Bram', 'email' => 'bram@example.com', 'status' => 'verzonden']);

        $this->actingAs($this->admin())->get(route('admin.bestellingen', ['zoek' => 'bram']))
            ->assertSee('bram@example.com')->assertDontSee('anna@example.com');

        $this->actingAs($this->admin())->get(route('admin.bestellingen', ['zoek' => '#' . $anna->id]))
            ->assertSee('anna@example.com')->assertDontSee('bram@example.com');

        $this->actingAs($this->admin())->get(route('admin.bestellingen', ['status' => 'verzonden']))
            ->assertSee('bram@example.com')->assertDontSee('anna@example.com');

        for ($i = 0; $i < 20; $i++) {
            $this->bestelling(['email' => "extra{$i}@example.com"]);
        }

        $this->actingAs($this->admin())->get(route('admin.bestellingen'))
            ->assertSee('Pagina 1 van 2');
        $this->actingAs($this->admin())->get(route('admin.bestellingen', ['page' => 2]))
            ->assertOk()->assertSee('Pagina 2 van 2');
    }

    public function test_berichten_zoeken_en_filteren(): void
    {
        ContactBericht::create(['naam' => 'Lisa', 'email' => 'lisa@example.com', 'bericht' => "Cateringaanvraag\nAantal personen: 30"]);
        ContactBericht::create(['naam' => 'Tom', 'email' => 'tom@example.com', 'bericht' => 'Gewone vraag over broodjes', 'gelezen' => true]);

        $this->actingAs($this->admin())->get(route('admin.berichten', ['filter' => 'catering']))
            ->assertSee('lisa@example.com')->assertDontSee('tom@example.com');

        $this->actingAs($this->admin())->get(route('admin.berichten', ['filter' => 'ongelezen']))
            ->assertSee('lisa@example.com')->assertDontSee('tom@example.com');

        $this->actingAs($this->admin())->get(route('admin.berichten', ['zoek' => 'broodjes']))
            ->assertSee('tom@example.com')->assertDontSee('lisa@example.com');
    }

    public function test_keuken_toont_een_dag_met_bezorglijst(): void
    {
        $donderdag = now()->next('Thursday');
        $this->bestelling(['naam' => 'Bezorgklant', 'email' => 'bezorg@example.com', 'gewenste_datum' => $donderdag,
            'levering' => 'bezorgen', 'straat' => 'Sleepboot 9', 'postcode' => '3991 CN', 'plaats' => 'Houten', 'telefoon' => '0612345678']);
        $this->bestelling(['naam' => 'Andere dag', 'gewenste_datum' => $donderdag->copy()->addDay()]);

        $this->actingAs($this->admin())->get(route('admin.keuken', ['datum' => $donderdag->toDateString()]))
            ->assertOk()
            ->assertSee('Bezorgen (1)')
            ->assertSee('Sleepboot 9')
            ->assertSee('Afdrukken')
            ->assertDontSee('Andere dag');
    }

    public function test_qr_code_staat_bij_instellingen_en_is_te_downloaden(): void
    {
        $this->actingAs($this->admin())->get(route('admin.instellingen'))
            ->assertOk()
            ->assertSee('<svg', false)
            ->assertSee(route('pakketten.index'))
            ->assertSee('wijst nog naar je eigen computer');

        $this->actingAs($this->admin())->get(route('admin.qr'))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertDownload('hapenhop-qr-code.svg');

        // Met het echte adres verdwijnt de waarschuwing
        config(['hapenhop.qr_url' => 'https://haphop.com/pakketten']);
        $this->actingAs($this->admin())->get(route('admin.instellingen'))
            ->assertSee('https://haphop.com/pakketten')
            ->assertDontSee('wijst nog naar je eigen computer');
    }

    public function test_qr_code_alleen_voor_beheerders(): void
    {
        $this->get(route('admin.qr'))->assertRedirect(route('login'));
    }
}
