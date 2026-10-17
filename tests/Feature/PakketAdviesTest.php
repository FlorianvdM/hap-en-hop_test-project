<?php

namespace Tests\Feature;

use App\Support\PakketAdviseur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PakketAdviesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function nepAdviseur(): void
    {
        $this->app->instance(PakketAdviseur::class, new class extends PakketAdviseur
        {
            public function __construct()
            {
            }

            public function beschikbaar(): bool
            {
                return true;
            }

            public function advies(int $aantal, ?string $wensen): string
            {
                return "Testadvies voor {$aantal} personen.";
            }
        });
    }

    public function test_advies_wordt_getoond(): void
    {
        $this->nepAdviseur();

        $this->post(route('pakketten.advies'), ['aantal' => 20, 'wensen' => 'halal'])
            ->assertRedirect()
            ->assertSessionHas('advies', 'Testadvies voor 20 personen.');
    }

    public function test_aantal_is_verplicht_en_moet_redelijk_zijn(): void
    {
        $this->nepAdviseur();

        $this->post(route('pakketten.advies'), ['aantal' => 0])->assertSessionHasErrors('aantal');
        $this->post(route('pakketten.advies'), ['aantal' => 100000])->assertSessionHasErrors('aantal');
        $this->post(route('pakketten.advies'), [])->assertSessionHasErrors('aantal');
    }

    public function test_te_lange_wensen_worden_geweigerd(): void
    {
        $this->nepAdviseur();

        $this->post(route('pakketten.advies'), ['aantal' => 10, 'wensen' => str_repeat('a', 501)])
            ->assertSessionHasErrors('wensen');
    }

    public function test_zonder_api_sleutel_komt_er_een_melding(): void
    {
        config(['services.anthropic.key' => null]);

        $this->post(route('pakketten.advies'), ['aantal' => 10])
            ->assertSessionHas('fout')
            ->assertSessionMissing('advies');
    }

    public function test_pakkettenpagina_toont_het_adviesformulier(): void
    {
        $this->get(route('pakketten.index'))
            ->assertOk()
            ->assertSee('Advies vragen');
    }
}
