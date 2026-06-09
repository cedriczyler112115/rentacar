<?php

namespace Tests\Feature;

use App\Models\LibAvailabilityStatus;
use App\Models\LibBrand;
use App\Models\LibFuelType;
use App\Models\LibTransmission;
use App\Models\LibType;
use App\Models\Rental;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDispatchingFilterAndSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_dispatching_lists_only_aaracc_owned_vehicles_and_sorts_by_priority_and_last_rented_date(): void
    {
        $type = LibType::create(['name' => 'Sedan']);
        LibBrand::create(['name' => 'Brand A']);
        LibTransmission::create(['name' => 'Automatic']);
        LibFuelType::create(['name' => 'Gasoline']);
        LibAvailabilityStatus::create(['id' => 1, 'name' => 'Available']);

        $admin = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
        ]);

        $aaraccOwner = User::factory()->create(['is_aaracc' => true]);
        $nonAaraccOwner = User::factory()->create(['is_aaracc' => false]);

        $neverRented = Vehicle::factory()->create([
            'user_id' => $aaraccOwner->id,
            'lib_type_id' => $type->id,
            'lib_availability_status_id' => 1,
            'name' => 'Never Rented',
        ]);

        $rentedOld = Vehicle::factory()->create([
            'user_id' => $aaraccOwner->id,
            'lib_type_id' => $type->id,
            'lib_availability_status_id' => 1,
            'name' => 'Rented Old',
        ]);

        $rentedNew = Vehicle::factory()->create([
            'user_id' => $aaraccOwner->id,
            'lib_type_id' => $type->id,
            'lib_availability_status_id' => 1,
            'name' => 'Rented New',
        ]);

        Vehicle::factory()->create([
            'user_id' => $nonAaraccOwner->id,
            'lib_type_id' => $type->id,
            'lib_availability_status_id' => 1,
            'name' => 'Non Aaracc Vehicle',
        ]);

        Rental::create([
            'user_id' => $admin->id,
            'vehicle_id' => $rentedOld->id,
            'region' => 'Region X',
            'province' => 'Province Y',
            'municipality' => 'Municipality Z',
            'destination_price' => 0,
            'has_carwash' => false,
            'carwash_fee' => 0,
            'extra_hours' => 0,
            'extra_hours_fee' => 0,
            'datetime_from' => now()->subDays(10),
            'datetime_to' => now()->subDays(9),
            'estimated_price' => 1000,
            'status' => 'Completed',
        ]);

        Rental::create([
            'user_id' => $admin->id,
            'vehicle_id' => $rentedNew->id,
            'region' => 'Region X',
            'province' => 'Province Y',
            'municipality' => 'Municipality Z',
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

        $res = $this->actingAs($admin)->get(route('admin.dispatching.index', ['type_id' => $type->id]));
        $res->assertOk();

        $html = $res->getContent();
        $this->assertStringNotContainsString('Non Aaracc Vehicle', $html);

        $posNever = strpos($html, 'Never Rented');
        $posOld = strpos($html, 'Rented Old');
        $posNew = strpos($html, 'Rented New');

        $this->assertNotFalse($posNever);
        $this->assertNotFalse($posOld);
        $this->assertNotFalse($posNew);

        $this->assertTrue($posNever < $posOld);
        $this->assertTrue($posOld < $posNew);
    }
}
