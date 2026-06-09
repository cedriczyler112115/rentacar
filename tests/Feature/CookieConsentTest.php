<?php

namespace Tests\Feature;

use App\Models\CookieConsentEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_accept_cookie_preferences(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $this->actingAs($user);

        $res = $this->postJson(route('cookie-consent.store'), [
            'analytics' => true,
            'marketing' => false,
        ]);

        $res->assertOk();
        $res->assertCookie('aar_cookie_consent');
        $this->assertSame(1, CookieConsentEvent::query()->where('user_id', $user->id)->where('action', 'accepted')->count());
    }

    public function test_guest_cannot_access_cookie_consent_endpoints(): void
    {
        $this->post(route('cookie-consent.store'))->assertRedirect();
    }
}

