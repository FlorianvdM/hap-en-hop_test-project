<?php

namespace Tests\Feature;

use App\Models\Pakket;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AfbeeldingTest extends TestCase
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

    /** Een echt (1x1) PNG-bestand; zo is de GD-extensie niet nodig. */
    private function foto(string $naam = 'foto.png'): UploadedFile
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');

        return UploadedFile::fake()->createWithContent($naam, $png);
    }

    public function test_pakket_afbeelding_uploaden_vervangen_en_opruimen(): void
    {
        $this->actingAs($this->admin())->post(route('admin.pakketten.store'), [
            'naam' => 'Fotopakket', 'prijs' => 10, 'actief' => 1, 'afbeelding' => $this->foto('a.png'),
        ])->assertRedirect(route('admin.pakketten.index'));

        $pakket = Pakket::where('naam', 'Fotopakket')->firstOrFail();
        $oud = $pakket->afbeelding;
        Storage::disk('public')->assertExists($oud);

        // Zonder upload blijft de afbeelding staan
        $this->actingAs($this->admin())->put(route('admin.pakketten.update', $pakket), [
            'naam' => 'Fotopakket', 'prijs' => 12, 'actief' => 1,
        ]);
        $this->assertSame($oud, $pakket->fresh()->afbeelding);

        // Met nieuwe upload wordt de oude verwijderd
        $this->actingAs($this->admin())->put(route('admin.pakketten.update', $pakket), [
            'naam' => 'Fotopakket', 'prijs' => 12, 'actief' => 1, 'afbeelding' => $this->foto('b.png'),
        ]);
        Storage::disk('public')->assertMissing($oud);
        $nieuw = $pakket->fresh()->afbeelding;
        Storage::disk('public')->assertExists($nieuw);

        $this->actingAs($this->admin())->delete(route('admin.pakketten.destroy', $pakket));
        Storage::disk('public')->assertMissing($nieuw);
    }

    public function test_product_afbeelding_wordt_getoond_en_opgeruimd(): void
    {
        $this->actingAs($this->admin())->post(route('admin.producten.store'), [
            'naam' => 'Fotoproduct', 'prijs' => 4, 'actief' => 1, 'afbeelding' => $this->foto(),
        ])->assertRedirect(route('admin.producten.index'));

        $product = Product::where('naam', 'Fotoproduct')->firstOrFail();

        $this->get(route('producten'))->assertSee(e($product->afbeeldingUrl()), false);
        $this->get(route('home'))->assertSee(e($product->afbeeldingUrl()), false);

        $pad = $product->afbeelding;
        $this->actingAs($this->admin())->delete(route('admin.producten.destroy', $product));
        Storage::disk('public')->assertMissing($pad);
    }

    public function test_alleen_afbeeldingen_en_niet_te_groot(): void
    {
        $this->actingAs($this->admin())->post(route('admin.pakketten.store'), [
            'naam' => 'X', 'prijs' => 1, 'actief' => 1,
            'afbeelding' => UploadedFile::fake()->create('bestand.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('afbeelding');

        $this->actingAs($this->admin())->post(route('admin.producten.store'), [
            'naam' => 'X', 'prijs' => 1, 'actief' => 1,
            'afbeelding' => $this->foto('groot.png')->size(5000),
        ])->assertSessionHasErrors('afbeelding');
    }
}
