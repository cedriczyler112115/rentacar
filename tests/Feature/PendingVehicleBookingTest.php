<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibMunicipality;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PendingVehicleBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_with_pending_availability_can_be_booked(): void
    {
        $user = User::factory()->create();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);
        $pending = LibAvailabilityStatus::firstOrCreate(['name' => 'Pending']);

        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $vehicle = Vehicle::create([
            'name' => 'Test Vehicle',
            'license_plate' => 'ABC-123',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'price_per_day' => 1000,
            'lib_availability_status_id' => $pending->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'seating_capacity' => 4,
            'user_id' => User::factory()->create()->id,
        ]);

        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);

        DB::table('lib_municipality_type_prices')->insert([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $from = Carbon::now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s');
        $to = Carbon::now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i:s');

        $this->actingAs($user);

        $response = $this->post(route('rentals.store', ['enc_id' => Crypt::encrypt($vehicle->id)]), [
            'region' => $municipality->region,
            'province' => $municipality->province,
            'municipality' => $municipality->municipality,
            'datetime_from' => $from,
            'datetime_to' => $to,
            'pickup_location' => 'Main Branch',
            'agree_terms' => '1',
        ]);

        $response->assertRedirect('/my-bookings');

        $this->assertDatabaseHas('rentals', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
            'status' => 'Pending',
        ]);

        $vehicle->refresh();
        $this->assertSame($pending->id, $vehicle->lib_availability_status_id);
    }

    public function test_vehicle_with_non_available_or_pending_status_is_rejected(): void
    {
        $user = User::factory()->create();

        $available = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);
        $pending = LibAvailabilityStatus::firstOrCreate(['name' => 'Pending']);
        $maintenance = LibAvailabilityStatus::firstOrCreate(['name' => 'Maintenance']);

        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $vehicle = Vehicle::create([
            'name' => 'Test Vehicle',
            'license_plate' => 'XYZ-999',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'price_per_day' => 1000,
            'lib_availability_status_id' => $maintenance->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'seating_capacity' => 4,
            'user_id' => User::factory()->create()->id,
        ]);

        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);

        DB::table('lib_municipality_type_prices')->insert([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 500,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $from = Carbon::now()->addDay()->setTime(10, 0)->format('Y-m-d H:i:s');
        $to = Carbon::now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i:s');

        $this->actingAs($user);

        $response = $this->from('/book')->post(route('rentals.store', ['enc_id' => Crypt::encrypt($vehicle->id)]), [
            'region' => $municipality->region,
            'province' => $municipality->province,
            'municipality' => $municipality->municipality,
            'datetime_from' => $from,
            'datetime_to' => $to,
            'pickup_location' => 'Main Branch',
            'agree_terms' => '1',
        ]);

        $response->assertSessionHasErrors('municipality');
        $this->assertDatabaseMissing('rentals', [
            'user_id' => $user->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $this->assertSame($maintenance->id, $vehicle->refresh()->lib_availability_status_id);
        $this->assertNotSame($available->id, $maintenance->id);
        $this->assertNotSame($pending->id, $maintenance->id);
    }
}
