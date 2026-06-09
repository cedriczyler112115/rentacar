<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $types = ['SUV', 'Sedan', 'Hatchback', 'Van', 'Truck'];
        foreach($types as $t) \App\Models\LibType::create(['name' => $t]);

        $brands = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan', 'BMW', 'Mercedes-Benz', 'Audi', 'Hyundai', 'Kia'];
        foreach($brands as $b) \App\Models\LibBrand::create(['name' => $b]);

        $statuses = ['Available', 'Pending', 'Rented', 'Maintenance'];
        foreach($statuses as $s) \App\Models\LibAvailabilityStatus::create(['name' => $s]);

        $transmissions = ['Automatic', 'Manual'];
        foreach($transmissions as $tr) \App\Models\LibTransmission::create(['name' => $tr]);

        $fuels = ['Gasoline', 'Diesel', 'Electric'];
        foreach($fuels as $f) \App\Models\LibFuelType::create(['name' => $f]);

        $this->call([
            VehicleSeeder::class,
        ]);
    }
}
