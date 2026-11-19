<?php

namespace Tests\Feature;

use App\Models\Pakket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BtwTest extends TestCase
{
    use RefreshDatabase;

    public function test_btw_nummer_staat_in_footer_en_voorwaarden_als_het_is_ingesteld(): void
    {
        config(['hapenhop.btw' => 'NL123456789B01']);

        $this->get(route('home'))->assertSee('Btw-id NL123456789B01');
        $this->get(route('voorwaarden'))->assertSee('Btw-id: NL123456789B01');
    }

    public function test_zonder_btw_nummer_staat_er_niets(): void
    {
        config(['hapenhop.btw' => null]);

        $this->get(route('home'))->assertDontSee('Btw-id');
        $this->get(route('voorwaarden'))->assertDontSee('Btw-id');
    }

    public function test_prijzen_zijn_inclusief_btw_staat_in_voorwaarden_en_winkelwagen(): void
    {
        $this->seed();

        $this->get(route('voorwaarden'))->assertSee('Alle prijzen op de website zijn inclusief btw.');
        $this->withSession(['winkelwagen' => [Pakket::firstOrFail()->id => 1]])
            ->get(route('winkelwagen.index'))
            ->assertSee('Alle prijzen zijn inclusief btw.');
    }
}
