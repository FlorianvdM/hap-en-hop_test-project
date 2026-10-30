<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactGegevensTest extends TestCase
{
    use RefreshDatabase;

    public function test_telefoonnummer_staat_op_de_contactpagina_als_het_is_ingesteld(): void
    {
        config(['hapenhop.telefoon' => '+31 6 12345678']);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('+31 6 12345678')
            // De belknop gebruikt alleen cijfers en de plus
            ->assertSee('tel:+31612345678', false);
    }

    public function test_zonder_telefoonnummer_staat_er_geen_telefoonregel(): void
    {
        config(['hapenhop.telefoon' => null]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertDontSee('<strong>Telefoon</strong>', false)
            ->assertDontSee('Nog invullen');
    }

    public function test_contactpagina_toont_het_ingestelde_emailadres(): void
    {
        config(['hapenhop.email' => 'test@voorbeeld.nl']);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('mailto:test@voorbeeld.nl', false);
    }
}