<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOwnerRatingsPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_aaracc_user_can_view_owner_ratings_page(): void
    {
        $admin = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $owner = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
            'name' => 'Owner One',
        ]);

        $renter = User::factory()->create([
            'is_aaracc' => false,
            'email_verified_at' => now(),
            'contact_number' => '09000000002',
            'address' => 'Renter Address',
        ]);

        $vehicle = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'user_id' => $owner->id,
        ]);

        $rental = Rental::create([
            'user_id' => $renter->id,
            'vehicle_id' => $vehicle->id,
            'pickup_location' => 'Pickup',
            'region' => 'Region',
            'province' => 'Province',
            'municipality' => 'Municipality',
            'destination_price' => 0,
            'has_carwash' => false,
            'carwash_fee' => 0,
            'extra_hours' => 0,
            'extra_hours_fee' => 0,
            'datetime_from' => now()->subDays(3),
            'datetime_to' => now()->subDays(2),
            'estimated_price' => 1000,
            'status' => 'Completed',
        ]);

        Review::create([
            'rental_id' => $rental->id,
            'vehicle_id' => $vehicle->id,
            'owner_id' => $owner->id,
            'reviewer_id' => $renter->id,
            'rating' => 4,
            'comment' => 'Nice car',
        ]);

        $this->actingAs($admin);

        $res = $this->get(route('admin.owner-ratings.index'));
        $res->assertOk();
        $res->assertSee('Owners Rating');
        $res->assertSee('Owner One');
        $res->assertSee('Car A');
        $res->assertSee('Nice car');
    }
}

