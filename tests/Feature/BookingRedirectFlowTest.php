<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingRedirectFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_clicking_book_now_redirects_to_login_with_intended_booking_url(): void
    {
        $available = LibAvailabilityStatus::create(['name' => 'Available']);
        $vehicle = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'lib_availability_status_id' => $available->id,
        ]);

        $res = $this->get(route('book.now', $vehicle));
        $res->assertRedirect();
        $this->assertStringContainsString('/login', $res->headers->get('Location'));
        $this->assertTrue(session()->has('url.intended'));
    }

    public function test_login_redirects_back_to_vehicle_booking_after_successful_authentication(): void
    {
        $available = LibAvailabilityStatus::create(['name' => 'Available']);
        $vehicle = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'lib_availability_status_id' => $available->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $redirect = $this->get(route('book.now', $vehicle))->headers->get('Location');
        $this->assertNotNull($redirect);

        $this->get($redirect)->assertOk();

        $login = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $login->assertRedirect();
        $this->assertStringContainsString('/book/', $login->headers->get('Location'));
    }

    public function test_profile_completion_preserves_booking_intent_after_login(): void
    {
        $available = LibAvailabilityStatus::create(['name' => 'Available']);
        $vehicle = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'lib_availability_status_id' => $available->id,
        ]);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '',
            'address' => '',
        ]);

        $redirect = $this->get(route('book.now', $vehicle))->headers->get('Location');
        $this->assertNotNull($redirect);
        $this->get($redirect)->assertOk();

        $login = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $login->assertRedirect();
        $bookUrl = $login->headers->get('Location');
        $this->assertNotNull($bookUrl);
        $this->get($bookUrl)->assertRedirect(route('profile.edit'));

        $this->actingAs($user);
        $upd = $this->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'contact_number' => '09000000000',
            'address' => 'Address',
        ]);

        $upd->assertRedirect();
        $this->assertStringContainsString('/book/', $upd->headers->get('Location'));
    }
}
