<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\Rental;
use App\Models\RentalLog;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalRejectionReasonTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_rejects_booking_with_reason_stored_in_rental_log(): void
    {
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

        $reason = 'Invalid schedule / vehicle not available.';
        $res = $this->postJson(route('rentals.cancel', ['rental' => $rental->id]), [
            'rejection_reason' => $reason,
        ]);

        $res->assertOk();

        $rental->refresh();
        $this->assertSame('Rejected', $rental->status);

        $log = RentalLog::query()->where('rental_id', $rental->id)->where('action', 'rejected')->first();
        $this->assertNotNull($log);
        $this->assertSame($reason, $log->new_values['rejection_reason'] ?? null);
    }
}

