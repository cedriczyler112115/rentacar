<?php

namespace Tests\Feature;

use App\Models\Rental;
use App\Models\Review;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerPublicProfileEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_owner_profile_endpoint_excludes_contact_number_and_returns_reviews(): void
    {
        $owner = User::factory()->create([
            'name' => 'Owner One',
            'contact_number' => '09123456789',
            'address' => 'Owner Address',
            'email_verified_at' => now(),
        ]);

        $renter = User::factory()->create([
            'name' => 'Renter One',
            'contact_number' => '09000000000',
            'address' => 'Renter Address',
            'email_verified_at' => now(),
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

        $res = $this->getJson(route('owners.profile', $owner));
        $res->assertOk();
        $res->assertJsonStructure([
            'owner' => ['id', 'name', 'email', 'address', 'profile_photo_url', 'vehicles_count', 'avg_rating', 'total_reviews'],
            'selected_vehicle',
            'reviews',
            'pagination' => ['current_page', 'last_page', 'per_page', 'total', 'next_page_url', 'prev_page_url'],
        ]);
        $res->assertDontSee('contact_number');
        $res->assertSee('Nice car');
        $res->assertSee('Car A');
    }

    public function test_owner_profile_endpoint_can_return_selected_vehicle_reviews_and_paginate_other_reviews(): void
    {
        $owner = User::factory()->create([
            'name' => 'Owner One',
            'contact_number' => '09123456789',
            'address' => 'Owner Address',
            'email_verified_at' => now(),
        ]);

        $renter = User::factory()->create([
            'name' => 'Renter One',
            'contact_number' => '09000000000',
            'address' => 'Renter Address',
            'email_verified_at' => now(),
        ]);

        $vehicleA = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'user_id' => $owner->id,
        ]);

        $vehicleB = Vehicle::create([
            'name' => 'Car B',
            'license_plate' => 'BBB222',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'user_id' => $owner->id,
        ]);

        for ($i = 0; $i < 2; $i++) {
            $rental = Rental::create([
                'user_id' => $renter->id,
                'vehicle_id' => $vehicleA->id,
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
                'vehicle_id' => $vehicleA->id,
                'owner_id' => $owner->id,
                'reviewer_id' => $renter->id,
                'rating' => 5,
                'comment' => 'A review ' . $i,
            ]);
        }

        for ($i = 0; $i < 6; $i++) {
            $rental = Rental::create([
                'user_id' => $renter->id,
                'vehicle_id' => $vehicleB->id,
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
                'vehicle_id' => $vehicleB->id,
                'owner_id' => $owner->id,
                'reviewer_id' => $renter->id,
                'rating' => 4,
                'comment' => 'B review ' . $i,
            ]);
        }

        $res = $this->getJson(route('owners.profile', $owner) . '?vehicle_id=' . $vehicleA->id . '&per_page=10&vehicle_per_page=10');
        $res->assertOk();

        $json = $res->json();
        $this->assertSame($vehicleA->id, $json['selected_vehicle']['id']);
        $this->assertSame(10, $json['pagination']['per_page']);
        $this->assertSame(8, $json['pagination']['total']);
        $this->assertCount(8, $json['reviews']);
        $res2 = $this->getJson(route('owners.profile', $owner) . '?vehicle_id=' . $vehicleA->id . '&per_page=10&only_vehicle=1');
        $res2->assertOk();
        $json2 = $res2->json();
        $this->assertSame(2, $json2['pagination']['total']);
        foreach ($json2['reviews'] as $rv) {
            $this->assertSame($vehicleA->id, $rv['vehicle']['id']);
        }
        foreach ($json['reviews'] as $rv) {
            $this->assertNotNull($rv['vehicle']['id']);
        }
    }
}
