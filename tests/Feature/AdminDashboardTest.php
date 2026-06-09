<?php

namespace Tests\Feature;

use App\Models\CarwashServicePayment;
use App\Models\Rental;
use App\Models\RentalLog;
use App\Models\ServiceFeePayment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_aaracc_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'is_aaracc' => false,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $this->actingAs($user);
        $this->get(route('admin.index'))->assertStatus(403);
        $this->get(route('admin.dashboard.data'))->assertStatus(403);
    }

    public function test_aaracc_user_can_view_dashboard_and_fetch_data(): void
    {
        $admin = User::factory()->create([
            'is_aaracc' => true,
            'email_verified_at' => now(),
            'contact_number' => '09000000000',
            'address' => 'Test Address',
        ]);

        $u2 = User::factory()->create([
            'is_aaracc' => false,
            'email_verified_at' => now(),
            'contact_number' => '09000000001',
            'address' => 'Address 2',
        ]);

        $v1 = Vehicle::create([
            'name' => 'Car A',
            'license_plate' => 'AAA111',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'user_id' => $admin->id,
        ]);

        $v2 = Vehicle::create([
            'name' => 'Car B',
            'license_plate' => 'BBB222',
            'price_per_day' => 1000,
            'seating_capacity' => 4,
            'user_id' => $u2->id,
        ]);

        $r1 = Rental::create([
            'user_id' => $u2->id,
            'vehicle_id' => $v1->id,
            'pickup_location' => 'Pickup',
            'region' => 'Region',
            'province' => 'Province',
            'municipality' => 'Municipality',
            'destination_price' => 0,
            'has_carwash' => false,
            'carwash_fee' => 0,
            'extra_hours' => 0,
            'extra_hours_fee' => 0,
            'datetime_from' => now()->addDay(),
            'datetime_to' => now()->addDays(2),
            'estimated_price' => 1000,
            'status' => 'Pending',
        ]);

        RentalLog::create([
            'rental_id' => $r1->id,
            'user_id' => $admin->id,
            'action' => 'created',
        ]);

        ServiceFeePayment::create([
            'user_id' => $admin->id,
            'year' => (int) now()->year,
            'month' => (int) now()->month,
            'amount' => 500,
            'proof_path' => null,
        ]);

        CarwashServicePayment::create([
            'user_id' => $admin->id,
            'vehicle_id' => $v1->id,
            'service_date' => now()->toDateString(),
            'amount_paid' => 250,
            'vehicle_proof_path' => null,
        ]);

        $this->actingAs($admin);
        $this->get(route('admin.index'))->assertOk()->assertSee('Admin Dashboard');

        $res = $this->getJson(route('admin.dashboard.data'));
        $res->assertOk();
        $res->assertJsonStructure([
            'meta' => ['generated_at', 'refresh_seconds'],
            'kpis' => [
                'users_total',
                'users_aaracc',
                'members_total',
                'vehicles_total',
                'vehicles_aaracc',
                'vehicles_rented',
                'vehicles_pending',
                'rentals_total',
                'bookings_rejected',
                'bookings_cancelled',
                'carwash_total_amount',
                'service_fee_this_month',
                'carwash_this_month',
                'owners_below_3',
            ],
            'rentals_status' => ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'Rejected'],
            'charts' => ['rentals_by_day', 'service_fee_by_month'],
            'top_cars' => ['range_days', 'items'],
            'vehicles_status' => ['available', 'rented', 'pending'],
            'recent',
            'system',
        ]);

        $json = $res->json();
        $this->assertSame(2, $json['kpis']['users_total']);
        $this->assertSame(1, $json['kpis']['users_aaracc']);
        $this->assertSame(2, $json['kpis']['vehicles_total']);
        $this->assertSame(1, $json['kpis']['vehicles_aaracc']);
        $this->assertSame(1, $json['rentals_status']['Pending']);
    }
}
