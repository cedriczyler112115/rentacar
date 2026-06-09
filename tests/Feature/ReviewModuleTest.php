<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_one_review_per_completed_booking_only(): void
    {
        $owner = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
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

        $completed = Rental::create([
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

        $pending = Rental::create([
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
            'datetime_from' => now()->addDay(),
            'datetime_to' => now()->addDays(2),
            'estimated_price' => 1000,
            'status' => 'Pending',
        ]);

        $this->actingAs($renter);

        $res = $this->postJson(route('reviews.store'), [
            'rental_id' => $completed->id,
            'rating' => 5,
            'comment' => 'Great service!',
        ]);
        $res->assertOk();

        $this->assertDatabaseHas('reviews', [
            'rental_id' => $completed->id,
            'vehicle_id' => $vehicle->id,
            'owner_id' => $owner->id,
            'reviewer_id' => $renter->id,
            'rating' => 5,
        ]);

        $res2 = $this->postJson(route('reviews.store'), [
            'rental_id' => $completed->id,
            'rating' => 4,
            'comment' => 'Second review attempt',
        ]);
        $res2->assertStatus(422);

        $res3 = $this->postJson(route('reviews.store'), [
            'rental_id' => $pending->id,
            'rating' => 4,
            'comment' => 'Not completed',
        ]);
        $res3->assertStatus(422);
    }

    public function test_vehicle_reviews_endpoint_returns_reviews_and_average(): void
    {
        $owner = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
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

        $this->actingAs($renter);
        $res = $this->getJson(route('reviews.vehicle', $vehicle));
        $res->assertOk();
        $res->assertJsonFragment(['avg_rating' => 4.0]);
        $res->assertJsonFragment(['total_reviews' => 1]);
    }
}

