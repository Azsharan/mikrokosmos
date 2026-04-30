<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteActiveFlagTest extends TestCase
{
    use RefreshDatabase;

    public function test_site_is_blocked_when_active_flag_is_false(): void
    {
        config()->set('app.active', false);

        $this->get(route('home'))
            ->assertStatus(503)
            ->assertSee('PRÓXIMAMENTE');
    }

    public function test_site_is_accessible_when_active_flag_is_true(): void
    {
        config()->set('app.active', true);

        $this->get(route('home'))->assertOk();
    }
}

