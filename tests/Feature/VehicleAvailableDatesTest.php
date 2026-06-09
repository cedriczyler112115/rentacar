<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleAvailableDatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_booked_dates_in_mixed_format_on_create(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $status = LibAvailabilityStatus::create(['name' => 'Available']);
        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $payload = [
            'name' => 'Car 1',
            'license_plate' => 'AAA111',
            'color' => 'Black',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'price_per_day' => 1000,
            'lib_availability_status_id' => $status->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'displacement' => '1500',
            'year_model' => '2024',
            'seating_capacity' => 4,
            'booked_dates' => json_encode([
                Carbon::now()->addDays(2)->format('Y-m-d'),
                ['start' => Carbon::now()->addDays(5)->format('Y-m-d'), 'end' => Carbon::now()->addDays(7)->format('Y-m-d')],
                Carbon::now()->addDays(10)->format('Y-m-d'),
            ]),
        ];

        $response = $this->post(route('my-cars.store'), $payload);
        $response->assertRedirect(route('my-cars.index'));

        $vehicle = Vehicle::query()->where('name', 'Car 1')->firstOrFail();
        $this->assertIsArray($vehicle->booked_dates);
        $this->assertCount(3, $vehicle->booked_dates);
        $this->assertIsString($vehicle->booked_dates[0]);
        $this->assertIsArray($vehicle->booked_dates[1]);
        $this->assertSame(['start', 'end'], array_keys($vehicle->booked_dates[1]));
    }

    public function test_it_rejects_past_dates(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $status = LibAvailabilityStatus::create(['name' => 'Available']);
        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $yesterday = Carbon::now()->subDay()->format('Y-m-d');

        $payload = [
            'name' => 'Car 2',
            'license_plate' => 'BBB222',
            'color' => 'Black',
            'lib_brand_id' => $brand->id,
            'lib_type_id' => $type->id,
            'price_per_day' => 1000,
            'lib_availability_status_id' => $status->id,
            'lib_transmission_id' => $transmission->id,
            'lib_fuel_type_id' => $fuel->id,
            'seating_capacity' => 4,
            'booked_dates' => json_encode([$yesterday]),
        ];

        $response = $this->from(route('my-cars.index'))->post(route('my-cars.store'), $payload);
        $response->assertRedirect(route('my-cars.index'));
        $response->assertSessionHasErrors('booked_dates');

        $this->assertDatabaseMissing('vehicles', ['name' => 'Car 2']);
    }
}
