<?php

namespace Tests\Unit;

use App\Models\LibMunicipality;
use App\Models\LibMunicipalityTypePrice;
use App\Models\LibType;
use App\Services\MunicipalityTypePricingService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MunicipalityTypePricingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_throws_when_no_type_price_exists(): void
    {
        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);
        $type = LibType::create(['name' => 'Sedan', 'carwash_fee' => 250]);

        $service = new MunicipalityTypePricingService();

        $this->expectException(ValidationException::class);
        $service->getPriceForType($municipality->id, $type->id);
    }

    public function test_it_returns_type_specific_price_when_exists(): void
    {
        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);
        $type = LibType::create(['name' => 'SUV', 'carwash_fee' => 250]);

        LibMunicipalityTypePrice::create([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 1200,
        ]);

        $service = new MunicipalityTypePricingService();

        $this->assertSame(1200.0, $service->getPriceForType($municipality->id, $type->id));
    }

    public function test_it_can_create_and_update_type_specific_price(): void
    {
        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);
        $type = LibType::create(['name' => 'Van', 'carwash_fee' => 250]);

        $service = new MunicipalityTypePricingService();
        $service->setPriceForType($municipality->id, $type->id, 1000);

        $this->assertDatabaseHas('lib_municipality_type_prices', [
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => '1000.00',
        ]);

        $service->setPriceForType($municipality->id, $type->id, 1500);

        $this->assertDatabaseHas('lib_municipality_type_prices', [
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => '1500.00',
        ]);
    }

    public function test_it_rejects_non_positive_prices(): void
    {
        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);
        $type = LibType::create(['name' => 'Pickup', 'carwash_fee' => 250]);

        $service = new MunicipalityTypePricingService();

        $this->expectException(ValidationException::class);
        $service->setPriceForType($municipality->id, $type->id, 0);
    }

    public function test_unique_constraint_prevents_duplicate_municipality_type_combinations(): void
    {
        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);
        $type = LibType::create(['name' => 'Hatchback', 'carwash_fee' => 250]);

        LibMunicipalityTypePrice::create([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 900,
        ]);

        $this->expectException(QueryException::class);
        LibMunicipalityTypePrice::create([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 950,
        ]);
    }

    public function test_cascade_delete_removes_type_prices_when_municipality_is_deleted(): void
    {
        $municipality = LibMunicipality::create([
            'region' => 'Region A',
            'province' => 'Province A',
            'municipality' => 'Municipality A',
        ]);
        $type = LibType::create(['name' => 'Coupe', 'carwash_fee' => 250]);

        $row = LibMunicipalityTypePrice::create([
            'lib_municipality_id' => $municipality->id,
            'lib_type_id' => $type->id,
            'price' => 800,
        ]);

        $municipality->delete();

        $this->assertDatabaseMissing('lib_municipality_type_prices', [
            'id' => $row->id,
        ]);
    }
}
