<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialMediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_toont_alle_sociale_iconen(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('op TikTok')
            ->assertSee('op Instagram')
            ->assertSee('op Facebook')
            ->assertSee('facebook.com/profile.php?id=61595299014970', false);
    }

    public function test_facebook_icoon_verdwijnt_als_er_geen_link_is_ingesteld(): void
    {
        config(['hapenhop.facebook' => null]);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('op Facebook');
    }
}