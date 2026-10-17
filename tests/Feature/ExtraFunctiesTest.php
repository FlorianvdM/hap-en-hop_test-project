<?php

namespace Tests\Feature;

use App\Mail\BestellingBevestiging;
use App\Models\Bestelling;
use App\Models\Pakket;
use App\Models\User;
use App\Support\Onderhoud;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ExtraFunctiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    protected function tearDown(): void
    {
        Onderhoud::zet(false);

        parent::tearDown();
    }

    private function admin(): User
    {
        return User::where('email', 'admin@hapenhop.nl')->firstOrFail();
    }

    private function bestelling(): Bestelling
    {
        $pakket = Pakket::first();
        $b = Bestelling::create(['naam' => 'Kees', 'email' => 'kees@example.com', 'gewenste_datum' => now()->addDay(), 'status' => 'nieuw', 'dieetwensen' => ['halal']]);
        $b->regels()->create(['pakket_id' => $pakket->id, 'pakket_naam' => $pakket->naam, 'aantal' => 2, 'prijs' => $pakket->prijs]);

        return $b->load('regels');
    }

    public function test_instellingen_toont_alle_beheerders(): void
    {
        User::factory()->create(['name' => 'Tweede Beheerder', 'email' => 'tweede@example.com', 'is_admin' => true]);
        User::factory()->create(['name' => 'Gewone Klant', 'email' => 'klant@example.com', 'is_admin' => false]);

        $this->actingAs($this->admin())->get('/admin/instellingen')
            ->assertOk()
            ->assertSee('Tweede Beheerder')
            ->assertSee('tweede@example.com')
            ->assertSee('(jij)')
            ->assertDontSee('Gewone Klant');
    }

    public function test_onderhoudsmodus_toont_404_aan_bezoekers_maar_niet_aan_beheerders(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/instellingen/onderhoud')->assertRedirect()->assertSessionHas('succes');
        $this->assertTrue(Onderhoud::aan());

        // beheerder ziet alles + waarschuwing
        $this->get('/')->assertOk();
        $this->get('/admin')->assertOk()->assertSee('Onderhoudsmodus staat aan');

        // bezoekers: 404 overal, behalve inloggen
        auth()->logout();
        $this->app['auth']->forgetGuards();
        foreach (['/', '/pakketten', '/info', '/contact', '/winkelwagen', '/privacy', '/bestaat-niet'] as $pad) {
            $this->get($pad)->assertNotFound();
        }
        $this->get('/login')->assertOk();
        // het beheer stuurt bezoekers door naar de (bereikbare) inlogpagina
        $this->get('/admin')->assertRedirect('/login');

        // een ingelogde gewone klant krijgt ook een 404
        $klant = User::factory()->create(['is_admin' => false]);
        $this->actingAs($klant)->get('/pakketten')->assertNotFound();
        $this->post('/uitloggen')->assertRedirect('/');

        // weer uitzetten
        $this->actingAs($admin)->post('/admin/instellingen/onderhoud')->assertRedirect();
        $this->assertFalse(Onderhoud::aan());
        auth()->logout();
        $this->app['auth']->forgetGuards();
        $this->get('/')->assertOk();
    }

    public function test_halal_is_te_kiezen_en_wordt_opgeslagen(): void
    {
        $pakket = Pakket::first();
        $this->post("/winkelwagen/{$pakket->id}");
        $this->get('/bestellen')->assertOk()->assertSee('Halal')->assertSee('data-laden', false);

        $this->post('/bestellen', [
            'naam' => 'Ali', 'email' => 'ali@example.com',
            'gewenste_datum' => now()->addDays(2)->format('Y-m-d'),
            'dieetwensen' => ['halal'],
        ])->assertRedirect('/pakketten');

        $this->assertSame(['halal'], Bestelling::latest('id')->first()->dieetwensen);

        $csv = $this->actingAs($this->admin())->get('/admin/bestellingen/export')->streamedContent();
        $this->assertStringContainsString('Halal', $csv);
    }

    public function test_bevestigingsmail_bevat_het_logo_als_ingesloten_afbeelding(): void
    {
        $this->assertFileExists(public_path('images/logo-mail.png'));

        Mail::to('klant@example.com')->send(new BestellingBevestiging($this->bestelling()));

        $bericht = Mail::mailer('array')->getSymfonyTransport()->messages()->last()->getOriginalMessage();

        $this->assertStringContainsString('cid:', $bericht->getHtmlBody());
        $this->assertCount(1, $bericht->getAttachments());
        $this->assertSame('logo-mail.png', $bericht->getAttachments()[0]->getFilename());
    }

    public function test_groene_melding_verdwijnt_automatisch(): void
    {
        $pakket = Pakket::first();
        $this->post("/winkelwagen/{$pakket->id}");
        $this->post('/bestellen', ['naam' => 'Ali', 'email' => 'ali@example.com', 'gewenste_datum' => now()->addDays(2)->format('Y-m-d')]);

        $this->get('/pakketten')->assertOk()->assertSee('data-verdwijnt="4000"', false)->assertSee('js/site.js');
    }

    public function test_favicon_staat_op_alle_pagina_typen(): void
    {
        $admin = $this->admin();

        foreach (['/', '/pakketten', '/info', '/contact', '/privacy', '/login', '/registreren', '/winkelwagen', '/bestaat-niet'] as $pad) {
            $this->get($pad)->assertSee('favicon.ico', false)->assertSee('apple-touch-icon.png', false);
        }

        foreach (['/admin', '/admin/instellingen', '/uitloggen'] as $pad) {
            $this->actingAs($admin)->get($pad)->assertSee('favicon.ico', false);
        }

        $this->assertStringContainsString('favicon.ico', view('errors.503')->render());

        foreach (['favicon.ico', 'favicon-32x32.png', 'apple-touch-icon.png', 'offline.html', 'sw.js', 'js/site.js'] as $bestand) {
            $this->assertFileExists(public_path($bestand));
        }
        $this->assertGreaterThan(1000, filesize(public_path('favicon.ico')));
    }

    public function test_503_pagina_is_zelfstandig(): void
    {
        $html = view('errors.503')->render();

        $this->assertStringContainsString('We zijn zo terug', $html);
        $this->assertStringContainsString('503', $html);
    }

    public function test_mails_kunnen_op_de_achtergrond_worden_verstuurd(): void
    {
        config(['hapenhop.mail_wachtrij' => true, 'hapenhop.mail_pauze' => 0, 'queue.default' => 'database']);

        $pakket = Pakket::first();
        $this->post("/winkelwagen/{$pakket->id}");
        $this->post('/bestellen', ['naam' => 'Ali', 'email' => 'ali@example.com', 'gewenste_datum' => now()->addDays(2)->format('Y-m-d')])
            ->assertRedirect('/pakketten');

        // niets is nog verstuurd, wel twee taken klaargezet
        $this->assertSame(2, DB::table('jobs')->count());
        $this->assertCount(0, Mail::mailer('array')->getSymfonyTransport()->messages());

        $this->artisan('queue:work', ['--stop-when-empty' => true, '--tries' => 1])->assertSuccessful();

        $this->assertSame(0, DB::table('jobs')->count());
        $onderwerpen = Mail::mailer('array')->getSymfonyTransport()->messages()
            ->map(fn ($m) => $m->getOriginalMessage()->getSubject())->all();
        $this->assertCount(2, $onderwerpen);
    }
}