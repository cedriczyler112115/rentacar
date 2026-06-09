<?php

namespace Tests\Feature;

use App\Models\ServiceFeePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceFeePaymentsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_aaracc_user_can_create_update_and_delete_service_fee_payment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf');

        $res = $this->post(route('admin.service-fee-payments.store'), [
            'year' => 2026,
            'month' => 4,
            'amount' => 1000.50,
            'proof' => $file,
        ]);
        $res->assertRedirect(route('admin.service-fee-payments.index'));

        $payment = ServiceFeePayment::query()->firstOrFail();
        $this->assertSame(2026, $payment->year);
        $this->assertSame(4, $payment->month);
        $this->assertSame('1000.50', (string) $payment->amount);
        $this->assertNotNull($payment->proof_path);
        Storage::disk('public')->assertExists($payment->proof_path);

        $newFile = UploadedFile::fake()->image('proof.png');
        $oldPath = $payment->proof_path;

        $res2 = $this->put(route('admin.service-fee-payments.update', $payment), [
            'year' => 2026,
            'month' => 5,
            'amount' => 1200,
            'proof' => $newFile,
        ]);
        $res2->assertRedirect(route('admin.service-fee-payments.index'));

        $payment->refresh();
        $this->assertSame(5, $payment->month);
        $this->assertSame('1200.00', (string) $payment->amount);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($payment->proof_path);

        $res3 = $this->delete(route('admin.service-fee-payments.destroy', $payment));
        $res3->assertRedirect(route('admin.service-fee-payments.index'));

        $this->assertDatabaseMissing('service_fee_payments', ['id' => $payment->id]);
        Storage::disk('public')->assertMissing($payment->proof_path);
    }
}

