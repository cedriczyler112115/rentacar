<?php

namespace Tests\Feature;

use App\Models\CarwashServicePayment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarwashServicePaymentsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_aaracc_user_can_create_update_and_delete_carwash_service_payment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $this->actingAs($user);

        $vehicle = Vehicle::create([
            'name' => 'Car 1',
            'license_plate' => 'ABC123',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'user_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->image('vehicle.png');

        $res = $this->post(route('admin.carwash-service-payments.store'), [
            'vehicle_id' => $vehicle->id,
            'service_date' => '2026-04-01',
            'amount_paid' => 250,
            'vehicle_proof' => $file,
        ]);
        $res->assertRedirect(route('admin.carwash-service-payments.index'));

        $payment = CarwashServicePayment::query()->firstOrFail();
        $this->assertSame($vehicle->id, $payment->vehicle_id);
        $this->assertSame('2026-04-01', $payment->service_date->format('Y-m-d'));
        $this->assertSame('250.00', (string) $payment->amount_paid);
        $this->assertNotNull($payment->vehicle_proof_path);
        Storage::disk('public')->assertExists($payment->vehicle_proof_path);

        $newFile = UploadedFile::fake()->image('vehicle2.jpg');
        $oldPath = $payment->vehicle_proof_path;

        $res2 = $this->put(route('admin.carwash-service-payments.update', $payment), [
            'vehicle_id' => $vehicle->id,
            'service_date' => '2026-04-02',
            'amount_paid' => 300.5,
            'vehicle_proof' => $newFile,
        ]);
        $res2->assertRedirect(route('admin.carwash-service-payments.index'));

        $payment->refresh();
        $this->assertSame('2026-04-02', $payment->service_date->format('Y-m-d'));
        $this->assertSame('300.50', (string) $payment->amount_paid);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($payment->vehicle_proof_path);

        $res3 = $this->delete(route('admin.carwash-service-payments.destroy', $payment));
        $res3->assertRedirect(route('admin.carwash-service-payments.index'));

        $this->assertDatabaseMissing('carwash_service_payments', ['id' => $payment->id]);
        Storage::disk('public')->assertMissing($payment->vehicle_proof_path);
    }
}
