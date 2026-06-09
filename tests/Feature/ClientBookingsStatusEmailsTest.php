<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClientBookingsStatusEmailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_confirm_sends_email_to_renter(): void
    {
        Mail::fake();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);
        $rented = LibAvailabilityStatus::firstOrCreate(['name' => 'Rented']);

        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $renter = User::factory()->create([
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
            'lib_availability_status_id' => $available->id,
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
            'datetime_from' => now()->addDay(),
            'datetime_to' => now()->addDays(2),
            'estimated_price' => 1000,
            'status' => 'Pending',
        ]);

        $this->actingAs($owner);
        $this->postJson(route('rentals.confirm', ['rental' => $rental->id]))->assertOk();

        Mail::assertSent(\App\Mail\BookingConfirmedRenterMail::class, function ($m) use ($renter) {
            return $m->hasTo($renter->email);
        });
    }

    public function test_owner_reject_sends_email_to_renter_with_reason(): void
    {
        Mail::fake();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);

        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $renter = User::factory()->create([
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
            'lib_availability_status_id' => $available->id,
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
            'datetime_from' => now()->addDay(),
            'datetime_to' => now()->addDays(2),
            'estimated_price' => 1000,
            'status' => 'Pending',
        ]);

        $this->actingAs($owner);
        $reason = 'Vehicle is not available.';
        $this->postJson(route('rentals.cancel', ['rental' => $rental->id]), [
            'rejection_reason' => $reason,
        ])->assertOk();

        Mail::assertSent(\App\Mail\BookingRejectedRenterMail::class, function ($m) use ($renter, $reason) {
            return $m->hasTo($renter->email) && $m->reason === $reason;
        });
    }

    public function test_owner_complete_sends_email_to_renter(): void
    {
        Mail::fake();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);

        $owner = User::factory()->create([
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $renter = User::factory()->create([
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
            'lib_availability_status_id' => $available->id,
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
            'datetime_from' => now()->subDays(2),
            'datetime_to' => now()->subDay(),
            'estimated_price' => 1000,
            'status' => 'Confirmed',
        ]);

        $this->actingAs($owner);
        $this->postJson(route('rentals.complete', ['rental' => $rental->id]))->assertOk();

        Mail::assertSent(\App\Mail\BookingCompletedRenterMail::class, function ($m) use ($renter) {
            return $m->hasTo($renter->email);
        });
    }
}

