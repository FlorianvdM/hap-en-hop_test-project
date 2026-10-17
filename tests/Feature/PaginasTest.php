<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Teamlid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaginasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::where('email', 'admin@hapenhop.nl')->firstOrFail();
    }

    /** Een echt (1x1) PNG-bestand; zo is de GD-extensie niet nodig om afbeeldingen te testen. */
    private function foto(string $naam = 'portret.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($naam, $png);
    }

    public function test_productenpagina_toont_alleen_zichtbare_producten_met_voorraad(): void
    {
        Product::create(['naam' => 'Zichtbaar testproduct', 'prijs' => 4.5, 'voorraad' => 3, 'actief' => true]);
        Product::create(['naam' => 'Verborgen testproduct', 'prijs' => 4.5, 'actief' => false]);

        $this->get(route('producten'))
            ->assertOk()
            ->assertSee('Zichtbaar testproduct')
            ->assertSee('Nog 3 beschikbaar')
            ->assertDontSee('Verborgen testproduct');
    }

    public function test_menu_bevat_producten_en_wie_zijn_wij(): void
    {
        $this->get(route('home'))
            ->assertSee(route('producten'), false)
            ->assertSee(route('wie-zijn-wij'), false);
    }

    public function test_wie_zijn_wij_is_leeg_zonder_teamleden(): void
    {
        $this->get(route('wie-zijn-wij'))
            ->assertOk()
            ->assertSee('We stellen ons binnenkort aan je voor.');
    }

    public function test_wie_zijn_wij_toont_zichtbare_teamleden_op_volgorde(): void
    {
        Teamlid::create(['naam' => 'Tweede Persoon', 'volgorde' => 2, 'actief' => true]);
        Teamlid::create(['naam' => 'Eerste Persoon', 'biografie' => 'Een korte bio.', 'volgorde' => 1, 'actief' => true]);
        Teamlid::create(['naam' => 'Verborgen Persoon', 'volgorde' => 0, 'actief' => false]);

        $this->get(route('wie-zijn-wij'))
            ->assertOk()
            ->assertSeeInOrder(['Eerste Persoon', 'Een korte bio.', 'Tweede Persoon'])
            ->assertDontSee('Verborgen Persoon');
    }

    public function test_beheer_kan_teamlid_toevoegen_met_foto(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.team.store'), [
                'naam' => 'Nieuwe Persoon',
                'functie' => 'Oprichter',
                'biografie' => 'Mijn verhaal.',
                'volgorde' => 1,
                'actief' => 1,
                'foto' => $this->foto(),
            ])
            ->assertRedirect(route('admin.team.index'));

        $teamlid = Teamlid::firstOrFail();

        $this->assertSame('Nieuwe Persoon', $teamlid->naam);
        Storage::disk('public')->assertExists($teamlid->foto);
    }

    public function test_foto_vervangen_verwijdert_de_oude_en_zonder_upload_blijft_hij_staan(): void
    {
        $this->actingAs($this->admin())->post(route('admin.team.store'), [
            'naam' => 'Persoon', 'actief' => 1,
            'foto' => $this->foto('a.png'),
        ]);

        $teamlid = Teamlid::firstOrFail();
        $oud = $teamlid->foto;

        // Zonder nieuwe foto blijft de oude staan
        $this->actingAs($this->admin())->put(route('admin.team.update', $teamlid), [
            'naam' => 'Persoon Nieuw', 'actief' => 1,
        ]);
        $this->assertSame($oud, $teamlid->fresh()->foto);
        Storage::disk('public')->assertExists($oud);

        // Met nieuwe foto wordt de oude verwijderd
        $this->actingAs($this->admin())->put(route('admin.team.update', $teamlid), [
            'naam' => 'Persoon Nieuw', 'actief' => 1,
            'foto' => $this->foto('b.png'),
        ]);

        Storage::disk('public')->assertMissing($oud);
        Storage::disk('public')->assertExists($teamlid->fresh()->foto);
    }

    public function test_verwijderen_ruimt_de_foto_op(): void
    {
        $this->actingAs($this->admin())->post(route('admin.team.store'), [
            'naam' => 'Persoon', 'actief' => 1,
            'foto' => $this->foto('a.png'),
        ]);

        $teamlid = Teamlid::firstOrFail();
        $pad = $teamlid->foto;

        $this->actingAs($this->admin())->delete(route('admin.team.destroy', $teamlid))->assertRedirect();

        $this->assertSame(0, Teamlid::count());
        Storage::disk('public')->assertMissing($pad);
    }

    public function test_alleen_afbeeldingen_en_niet_te_groot(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.team.store'), [
                'naam' => 'Persoon', 'actief' => 1,
                'foto' => UploadedFile::fake()->create('bestand.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('foto');

        $this->actingAs($this->admin())
            ->post(route('admin.team.store'), [
                'naam' => 'Persoon', 'actief' => 1,
                'foto' => $this->foto('groot.png')->size(5000),
            ])
            ->assertSessionHasErrors('foto');
    }

    public function test_beheer_team_is_alleen_voor_admins(): void
    {
        $this->get(route('admin.team.index'))->assertRedirect();

        $gewoon = User::factory()->create(['is_admin' => false]);

        $this->actingAs($gewoon)->get(route('admin.team.index'))->assertForbidden();
    }
}
