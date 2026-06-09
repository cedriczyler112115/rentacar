<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . ' Model',
            'price_per_day' => $this->faker->randomFloat(2, 20, 200),
            'seating_capacity' => $this->faker->numberBetween(2, 7),
            'lib_brand_id' => \App\Models\LibBrand::inRandomOrder()->first()->id ?? 1,
            'lib_type_id' => \App\Models\LibType::inRandomOrder()->first()->id ?? 1,
            'lib_availability_status_id' => \App\Models\LibAvailabilityStatus::inRandomOrder()->first()->id ?? 1,
            'lib_transmission_id' => \App\Models\LibTransmission::inRandomOrder()->first()->id ?? 1,
            'lib_fuel_type_id' => \App\Models\LibFuelType::inRandomOrder()->first()->id ?? 1,
        ];
    }
}
