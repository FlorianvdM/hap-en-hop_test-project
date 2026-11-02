<?php

namespace Tests\Feature;

use App\Models\Bestelling;
use App\Models\Pakket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Aanvallen die een kwaadwillende bezoeker zou proberen. Elke test moet laten zien dat de aanval mislukt.
 */
class HackPogingenTest extends TestCase
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
        return tap(User::factory()->create(), fn (User $u) => $u->forceFill(['is_admin' => true])->save());
    }

    public function test_registreren_met_is_admin_maakt_geen_beheerder(): void
    {
        $this->post('/registreren', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'geheim123',
            'password_confirmation' => 'geheim123',
            'is_admin' => 1,
        ]);

        $this->assertFalse((bool) User::where('email', 'hacker@example.com')->first()->is_admin);
        $this->get('/admin')->assertForbidden();
    }

    public function test_gast_en_klant_komen_niet_in_het_beheer(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/admin/bestellingen/export')->assertRedirect('/login');

        $klant = User::factory()->create();
        $this->actingAs($klant)->get('/admin/gebruikers')->assertForbidden();
        $this->actingAs($klant)->put("/admin/gebruikers/{$klant->id}", [
            'name' => $klant->name,
            'email' => $klant->email,
            'is_admin' => 1,
        ])->assertForbidden();

        $this->assertFalse((bool) $klant->fresh()->is_admin);
    }

    public function test_beheerder_kan_eigen_rol_niet_afnemen(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put("/admin/gebruikers/{$admin->id}", [
            'name' => $admin->name,
            'email' => $admin->email,
        ]);

        $this->assertTrue((bool) $admin->fresh()->is_admin);
    }

    public function test_klant_ziet_geen_bestellingen_van_een_ander(): void
    {
        $ik = User::factory()->create();
        $ander = User::factory()->create();

        Bestelling::create([
            'user_id' => $ander->id,
            'naam' => 'Geheime Klant',
            'email' => 'geheim@example.com',
            'gewenste_datum' => now()->addDays(3),
            'levering' => 'ophalen',
            'status' => 'nieuw',
        ]);

        $this->actingAs($ik)->get('/mijn-bestellingen')
            ->assertOk()
            ->assertDontSee('Geheime Klant');
    }

    public function test_xss_in_contactbericht_wordt_niet_uitgevoerd(): void
    {
        $script = '<script>alert("hack")</script>';

        $this->post('/contact', [
            'naam' => $script,
            'email' => 'x@example.com',
            'bericht' => $script . ' en nog wat tekst',
        ])->assertRedirect();

        $this->actingAs($this->admin())->get('/admin/berichten')
            ->assertOk()
            ->assertDontSee($script, false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_sql_injectie_bij_inloggen_lukt_niet(): void
    {
        $this->post('/login', [
            'email' => "admin@hapenhop.nl' OR '1'='1",
            'password' => "' OR '1'='1",
        ]);

        $this->assertGuest();
    }

    public function test_wachtwoord_raden_wordt_na_vijf_pogingen_geblokkeerd(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => 'admin@hapenhop.nl', 'password' => "fout{$i}"]);
        }

        $this->post('/login', ['email' => 'admin@hapenhop.nl', 'password' => 'nogeen'])
            ->assertStatus(429);
    }

    public function test_massaal_accounts_aanmaken_wordt_geblokkeerd(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/registreren', ['name' => 'Bot', 'email' => "bot{$i}@example.com"]);
        }

        $this->post('/registreren', ['name' => 'Bot', 'email' => 'bot5@example.com'])
            ->assertStatus(429);
    }

    public function test_verborgen_pakket_kan_niet_in_de_winkelwagen(): void
    {
        $pakket = Pakket::first();
        $pakket->update(['actief' => false]);

        $this->post("/winkelwagen/{$pakket->id}")->assertNotFound();
    }

    public function test_negatief_of_enorm_aantal_wordt_geweigerd(): void
    {
        $pakket = Pakket::first();
        $pakket->update(['actief' => true, 'voorraad' => null]);
        $this->post("/winkelwagen/{$pakket->id}");

        $this->patch("/winkelwagen/{$pakket->id}", ['aantal' => -5])->assertSessionHasErrors('aantal');
        $this->patch("/winkelwagen/{$pakket->id}", ['aantal' => 99999])->assertSessionHasErrors('aantal');
    }

    public function test_eigen_prijs_meesturen_verandert_de_bestelprijs_niet(): void
    {
        $pakket = Pakket::first();
        $pakket->update(['actief' => true, 'voorraad' => null]);
        $this->post("/winkelwagen/{$pakket->id}");

        $this->post('/bestellen', [
            'naam' => 'Slimmerik',
            'email' => 'slim@example.com',
            'gewenste_datum' => now()->addDays(3)->toDateString(),
            'levering' => 'ophalen',
            'prijs' => '0.01',
            'status' => 'verzonden',
        ]);

        $bestelling = Bestelling::latest('id')->first();
        $this->assertSame('nieuw', $bestelling->status);
        $this->assertEquals($pakket->prijs, $bestelling->regels->first()->prijs);
    }

    public function test_php_bestand_uploaden_als_afbeelding_wordt_geweigerd(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())->post('/admin/team', [
            'naam' => 'Hacker',
            'actief' => 1,
            'foto' => UploadedFile::fake()->createWithContent('shell.php', '<?php system($_GET["c"]); ?>'),
        ])->assertSessionHasErrors('foto');

        $this->assertEmpty(Storage::disk('public')->allFiles());
    }
}
