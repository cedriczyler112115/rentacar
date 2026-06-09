<?php

namespace Tests\Feature;

use App\Models\BookingEmailLog;
use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibMunicipality;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminDispatchingEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dispatch_sends_email_to_vehicle_owner(): void
    {
        Mail::fake();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);
        LibAvailabilityStatus::firstOrCreate(['name' => 'Rented']);

        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $admin = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Admin Address',
        ]);

        $owner = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Owner Address',
        ]);

        $vehicle = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'lib_availability_status_id' => $available->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'color' => 'White',
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

        $this->actingAs($admin);

        $from = now()->addDays(2)->format('Y-m-d H:i:s');
        $to = now()->addDays(3)->format('Y-m-d H:i:s');

        $res = $this->postJson(route('admin.dispatching.dispatch-store'), [
            'vehicle_id' => $vehicle->id,
            'region' => $municipality->region,
            'province' => $municipality->province,
            'municipality' => $municipality->municipality,
            'datetime_from' => $from,
            'datetime_to' => $to,
            'pickup_location' => 'Main Branch',
        ]);

        $res->assertOk();

        Mail::assertSent(\App\Mail\DispatchCreatedOwnerMail::class, function ($m) use ($owner) {
            return $m->hasTo($owner->email);
        });

        $this->assertSame(1, BookingEmailLog::query()->where('type', 'dispatch_created_owner')->count());
    }
}

