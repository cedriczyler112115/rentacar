<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileCompletionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_incomplete_profile_is_redirected_to_profile_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => null,
            'address' => null,
        ]);

        $this->actingAs($user);

        $res = $this->get(route('dashboard'));
        $res->assertRedirect(route('profile.edit'));
    }

    public function test_profile_page_is_accessible_even_if_incomplete(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => null,
            'address' => null,
        ]);

        $this->actingAs($user);

        $res = $this->get(route('profile.edit'));
        $res->assertOk();
    }
}

