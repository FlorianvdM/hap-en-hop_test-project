<?php

namespace Tests\Feature;

use App\Models\ContactBericht;
use App\Models\User;
use App\Support\BerichtAssistent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerichtConceptTest extends TestCase
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

    private function bericht(): ContactBericht
    {
        return ContactBericht::create([
            'naam' => 'Test Klant',
            'email' => 'klant@example.com',
            'bericht' => 'Hebben jullie een halal pakket voor 20 personen?',
        ]);
    }

    public function test_concept_wordt_getoond_voor_beheerder(): void
    {
        $bericht = $this->bericht();

        $this->app->instance(BerichtAssistent::class, new class extends BerichtAssistent
        {
            public function __construct()
            {
            }

            public function beschikbaar(): bool
            {
                return true;
            }

            public function concept(ContactBericht $bericht): string
            {
                return 'Dit is een testconcept.';
            }
        });

        $this->actingAs($this->admin())
            ->post(route('admin.berichten.concept', $bericht))
            ->assertRedirect()
            ->assertSessionHas('concept', ['id' => $bericht->id, 'tekst' => 'Dit is een testconcept.']);
    }

    public function test_zonder_api_sleutel_komt_er_een_melding(): void
    {
        config(['services.anthropic.key' => null]);

        $this->actingAs($this->admin())
            ->post(route('admin.berichten.concept', $this->bericht()))
            ->assertSessionHas('fout')
            ->assertSessionMissing('concept');
    }

    public function test_bezoeker_krijgt_geen_toegang(): void
    {
        $this->post(route('admin.berichten.concept', $this->bericht()))
            ->assertRedirect();
    }
}
