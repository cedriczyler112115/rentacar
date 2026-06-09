<?php

namespace Tests\Feature;

use App\Models\ServiceFeePayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceFeePaymentsFilterAndMembersTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_fee_payments_index_supports_filter_and_pagination(): void
    {
        $user = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $this->actingAs($user);

        for ($m = 1; $m <= 12; $m++) {
            ServiceFeePayment::create([
                'user_id' => $user->id,
                'year' => 2026,
                'month' => $m,
                'amount' => 1000,
                'proof_path' => null,
            ]);
        }

        $res1 = $this->get(route('admin.service-fee-payments.index', ['year' => 2026]));
        $res1->assertOk();
        $res1->assertSee('Service Fee Payments');
        $res1->assertSee('December 2026');
        $res1->assertSee('page=2');

        $res2 = $this->get(route('admin.service-fee-payments.index', ['year' => 2026, 'page' => 2]));
        $res2->assertOk();
        $res2->assertSee('February 2026');
        $res2->assertSee('January 2026');
    }

    public function test_members_paid_unpaid_page_marks_users_correctly_for_period(): void
    {
        $admin = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $uPaid = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Test Address 2',
            'name' => 'Paid Member',
        ]);

        $uUnpaid = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000002',
            'address' => 'Test Address 3',
            'name' => 'Unpaid Member',
        ]);

        ServiceFeePayment::create([
            'user_id' => $uPaid->id,
            'year' => 2026,
            'month' => 4,
            'amount' => 500,
            'proof_path' => null,
        ]);

        $this->actingAs($admin);

        $res = $this->get(route('admin.service-fee-payments.members', ['year' => 2026, 'month' => 4]));
        $res->assertOk();
        $res->assertSee('Paid Member');
        $res->assertSee('Unpaid Member');
        $res->assertSee('Paid');
        $res->assertSee('Unpaid');
    }
}

