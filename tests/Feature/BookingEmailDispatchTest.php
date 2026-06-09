<?php

namespace Tests\Feature;

use App\Models\BookingEmailLog;
use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibMunicipality;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingEmailDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_submission_queues_owner_email_log_and_job(): void
    {
        Mail::fake();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);
        $pending = LibAvailabilityStatus::firstOrCreate(['name' => 'Pending']);

        $brand = LibBrand::create(['name' => 'Toyota']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

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
            'name' => 'Toyota Camry',
            'license_plate' => 'AAA111',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'lib_availability_status_id' => $available->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'color' => 'Black',
            'year_model' => '2024',
            'user_id' => $owner->id,
        ]);

        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);

        DB::table('lib_municipality_type_prices')->insert([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($renter);

        $from = now()->addDays(2)->format('Y-m-d H:i:s');
        $to = now()->addDays(3)->format('Y-m-d H:i:s');

        $res = $this->post(route('rentals.store', ['enc_id' => encrypt($vehicle->id)]), [
            'region' => $municipality->region,
            'province' => $municipality->province,
            'municipality' => $municipality->municipality,
            'datetime_from' => $from,
            'datetime_to' => $to,
            'pickup_location' => 'Main Branch',
            'agree_terms' => '1',
        ]);

        $res->assertRedirect('/my-bookings');

        $rental = Rental::query()->first();
        $this->assertNotNull($rental);

        $this->assertSame(1, BookingEmailLog::query()->where('rental_id', $rental->id)->where('type', 'booking_created_owner')->count());
        Mail::assertSent(\App\Mail\BookingCreatedOwnerMail::class);
    }

    public function test_renter_cancellation_queues_owner_cancellation_email(): void
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

        $this->actingAs($renter);
        $this->postJson(route('rentals.cancel_by_renter', ['rental' => $rental->id]))->assertOk();

        $this->assertSame(1, BookingEmailLog::query()->where('rental_id', $rental->id)->where('type', 'booking_cancelled_owner')->count());
        Mail::assertSent(\App\Mail\BookingCancelledOwnerMail::class);
    }
}
