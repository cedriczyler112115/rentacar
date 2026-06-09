<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleLicensePlateUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_license_plate_and_it_is_required(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $status = LibAvailabilityStatus::create(['name' => 'Available']);
        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $vehicle = Vehicle::create([
            'name' => 'Car 1',
            'license_plate' => 'OLD123',
            'color' => 'Black',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'price_per_day' => 1000,
            'lib_availability_status_id' => $status->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'year_model' => '2024',
            'seating_capacity' => 4,
            'user_id' => $user->id,
        ]);

        $payload = [
            'name' => $vehicle->name,
            'license_plate' => 'NEW456',
            'color' => $vehicle->color,
            'lib_brand_id' => $vehicle->lib_brand_id,
            'lib_type_id' => $vehicle->lib_type_id,
            'price_per_day' => $vehicle->price_per_day,
            'lib_availability_status_id' => $vehicle->lib_availability_status_id,
            'lib_transmission_id' => $vehicle->lib_transmission_id,
            'lib_fuel_type_id' => $vehicle->lib_fuel_type_id,
            'year_model' => $vehicle->year_model,
            'seating_capacity' => $vehicle->seating_capacity,
            'booked_dates' => json_encode([]),
            'delete_image_ids' => json_encode([]),
        ];

        $res = $this->put(route('my-cars.update', $vehicle->id), $payload);
        $res->assertRedirect(route('my-cars.index'));

        $vehicle->refresh();
        $this->assertSame('NEW456', $vehicle->license_plate);

        $bad = $payload;
        $bad['license_plate'] = '';
        $res2 = $this->from(route('my-cars.index'))->put(route('my-cars.update', $vehicle->id), $bad);
        $res2->assertRedirect(route('my-cars.index'));
        $res2->assertSessionHasErrors('license_plate');
    }
}
