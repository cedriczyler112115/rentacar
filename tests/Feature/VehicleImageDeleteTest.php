<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VehicleImageDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_delete_existing_photo_via_edit_update(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $status = LibAvailabilityStatus::firstOrCreate(['name' => 'Available']);
        $brand = LibBrand::create(['name' => 'Brand A']);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 0]);
        $transmission = LibTransmission::create(['name' => 'Automatic']);
        $fuel = LibFuelType::create(['name' => 'Gasoline']);

        $vehicle = Vehicle::create([
            'name' => 'Car 1',
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
            'user_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->image('photo.jpg');
        $path = $file->store('vehicles', 'public');

        $img = VehicleImage::create([
            'vehicle_id' => $vehicle->id,
            'image_path' => $path,
            'is_primary' => true,
        ]);

        Storage::disk('public')->assertExists($path);

        $payload = [
            'name' => $vehicle->name,
            'license_plate' => 'ABC123',
            'color' => $vehicle->color,
            'lib_brand_id' => $vehicle->lib_brand_id,
            'lib_type_id' => $vehicle->lib_type_id,
            'price_per_day' => $vehicle->price_per_day,
            'lib_availability_status_id' => $vehicle->lib_availability_status_id,
            'lib_transmission_id' => $vehicle->lib_transmission_id,
            'lib_fuel_type_id' => $vehicle->lib_fuel_type_id,
            'displacement' => $vehicle->displacement,
            'year_model' => $vehicle->year_model,
            'seating_capacity' => $vehicle->seating_capacity,
            'booked_dates' => json_encode([]),
            'delete_image_ids' => json_encode([$img->id]),
        ];

        $res = $this->put(route('my-cars.update', $vehicle->id), $payload);
        $res->assertRedirect(route('my-cars.index'));

        $this->assertDatabaseMissing('vehicle_images', ['id' => $img->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
