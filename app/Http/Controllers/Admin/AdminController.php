<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarwashServicePayment;
use App\Models\LibAvailabilityStatus;
use App\Models\Rental;
use App\Models\RentalLog;
use App\Models\Review;
use App\Models\ServiceFeePayment;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $modules = [
            [
                'key' => 'dashboard',
                'title' => 'Dashboard',
                'description' => 'Overview and system insights',
                'route' => route('admin.index'),
                'active' => $request->routeIs('admin.index'),
                'enabled' => true,
            ],
            [
                'key' => 'users',
                'title' => 'Users',
                'description' => 'Manage accounts and access',
                'route' => route('admin.users.index'),
                'active' => $request->routeIs('admin.users.*'),
                'enabled' => (bool) ($user?->is_aaracc),
            ],
            [
                'key' => 'dispatching',
                'title' => 'Dispatching',
                'description' => 'Dispatch available vehicles by type',
                'route' => route('admin.dispatching.index'),
                'active' => $request->routeIs('admin.dispatching.*'),
                'enabled' => (bool) ($user?->is_aaracc),
            ],
            [
                'key' => 'service_fee',
                'title' => 'Service Fee',
                'description' => 'Track service fee payments',
                'route' => route('admin.service-fee-payments.index'),
                'active' => $request->routeIs('admin.service-fee-payments.*'),
                'enabled' => (bool) ($user?->is_aaracc),
            ],
            [
                'key' => 'carwash',
                'title' => 'Carwash Service Fee',
                'description' => 'Track carwash service payments',
                'route' => route('admin.carwash-service-payments.index'),
                'active' => $request->routeIs('admin.carwash-service-payments.*'),
                'enabled' => (bool) ($user?->is_aaracc),
            ],
            [
                'key' => 'faqs',
                'title' => 'FAQs',
                'description' => 'Manage booking FAQs',
                'route' => route('admin.faqs.index'),
                'active' => $request->routeIs('admin.faqs.*'),
                'enabled' => (bool) ($user?->is_aaracc),
            ],
        ];

        $widgets = [
            ['id' => 'kpis', 'title' => 'KPIs', 'enabled' => true],
            ['id' => 'quick_actions', 'title' => 'Quick Actions', 'enabled' => true],
            ['id' => 'charts', 'title' => 'Trends', 'enabled' => true],
            ['id' => 'pending', 'title' => 'Pending Tasks', 'enabled' => true],
            ['id' => 'recent', 'title' => 'Recent Activity', 'enabled' => true],
            ['id' => 'system', 'title' => 'System Health', 'enabled' => true],
        ];

        $refreshIntervalSeconds = 30;

        return view('admin.index', compact('modules', 'widgets', 'refreshIntervalSeconds'));
    }

    public function dashboardData(Request $request)
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $rangeDays = 30;
        $start = $today->copy()->subDays($rangeDays - 1);
        $topCarsDays = 30;
        $topCarsStart = $today->copy()->subDays($topCarsDays - 1);

        $usersTotal = User::query()->count();
        $aaraccUsers = User::query()->where('is_aaracc', true)->count();
        $vehiclesTotal = Vehicle::query()->count();
        $vehiclesAaracc = Vehicle::query()->whereHas('user', fn ($q) => $q->where('is_aaracc', true))->count();

        $rentalsTotal = Rental::query()->count();
        $rentalsPending = Rental::query()->where('status', 'Pending')->count();
        $rentalsConfirmed = Rental::query()->where('status', 'Confirmed')->count();
        $rentalsCompleted = Rental::query()->where('status', 'Completed')->count();
        $rentalsCancelled = Rental::query()->where('status', 'Cancelled')->count();
        $rentalsRejected = Rental::query()->where('status', 'Rejected')->count();

        $availableStatusIds = LibAvailabilityStatus::query()
            ->whereRaw('LOWER(name) = ?', ['available'])
            ->pluck('id')
            ->all();
        $rentedStatusIds = LibAvailabilityStatus::query()
            ->whereRaw('LOWER(name) = ?', ['rented'])
            ->pluck('id')
            ->all();
        $pendingStatusIds = LibAvailabilityStatus::query()
            ->whereRaw('LOWER(name) = ?', ['pending'])
            ->pluck('id')
            ->all();

        $vehiclesAvailable = count($availableStatusIds) > 0 ? Vehicle::query()->whereIn('lib_availability_status_id', $availableStatusIds)->count() : 0;
        $vehiclesRented = count($rentedStatusIds) > 0 ? Vehicle::query()->whereIn('lib_availability_status_id', $rentedStatusIds)->count() : 0;
        $vehiclesPending = count($pendingStatusIds) > 0 ? Vehicle::query()->whereIn('lib_availability_status_id', $pendingStatusIds)->count() : 0;

        $ownersBelow3 = Review::query()
            ->selectRaw('owner_id')
            ->groupBy('owner_id')
            ->havingRaw('AVG(rating) < 3')
            ->get()
            ->count();

        $carwashTotalAmount = (float) CarwashServicePayment::query()->sum('amount_paid');
        $serviceFeeThisMonth = (float) ServiceFeePayment::query()
            ->where('year', (int) $now->year)
            ->where('month', (int) $now->month)
            ->sum('amount');

        $carwashThisMonth = (float) CarwashServicePayment::query()
            ->whereBetween('service_date', [$now->copy()->startOfMonth()->toDateString(), $now->copy()->endOfMonth()->toDateString()])
            ->sum('amount_paid');

        $rentalsByDay = Rental::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween('created_at', [$start, $now])
            ->groupBy('d')
            ->orderBy('d')
            ->pluck('c', 'd')
            ->all();

        $days = [];
        for ($i = 0; $i < $rangeDays; $i++) {
            $d = $start->copy()->addDays($i)->toDateString();
            $days[] = [
                'date' => $d,
                'count' => (int) ($rentalsByDay[$d] ?? 0),
            ];
        }

        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i);
            $months[] = [
                'year' => (int) $m->year,
                'month' => (int) $m->month,
                'label' => $m->format('M Y'),
            ];
        }

        $serviceRows = ServiceFeePayment::query()
            ->selectRaw('year, month, SUM(amount) as total')
            ->where(function ($q) use ($months) {
                $first = $months[0];
                $last = $months[count($months) - 1];
                $q->whereRaw('(year > ? OR (year = ? AND month >= ?))', [$first['year'], $first['year'], $first['month']])
                    ->whereRaw('(year < ? OR (year = ? AND month <= ?))', [$last['year'], $last['year'], $last['month']]);
            })
            ->groupBy('year', 'month')
            ->get();

        $serviceByMonthRaw = [];
        foreach ($serviceRows as $row) {
            $k = (int) $row->year . '-' . str_pad((string) ((int) $row->month), 2, '0', STR_PAD_LEFT);
            $serviceByMonthRaw[$k] = (float) $row->total;
        }

        $serviceByMonth = [];
        foreach ($months as $m) {
            $k = $m['year'] . '-' . str_pad((string) $m['month'], 2, '0', STR_PAD_LEFT);
            $serviceByMonth[] = [
                'label' => $m['label'],
                'total' => (float) ($serviceByMonthRaw[$k] ?? 0),
            ];
        }

        $topCars = Rental::query()
            ->selectRaw('vehicle_id, COUNT(*) as c')
            ->whereBetween('created_at', [$topCarsStart, $now])
            ->whereIn('status', ['Confirmed', 'Completed'])
            ->groupBy('vehicle_id')
            ->orderByDesc('c')
            ->limit(10)
            ->with(['vehicle'])
            ->get()
            ->map(function ($row) {
                return [
                    'vehicle_id' => (int) $row->vehicle_id,
                    'name' => $row->vehicle?->name ?? 'N/A',
                    'license_plate' => $row->vehicle?->license_plate ?? null,
                    'count' => (int) $row->c,
                ];
            })
            ->all();

        $recent = RentalLog::query()
            ->with(['user', 'rental.vehicle'])
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function ($log) {
                $vehicleName = $log->rental?->vehicle?->name ?? null;
                $ref = $log->rental_id ? str_pad((string) $log->rental_id, 6, '0', STR_PAD_LEFT) : null;
                return [
                    'id' => $log->id,
                    'when' => $log->created_at?->toIso8601String(),
                    'user' => $log->user?->name ?? 'System',
                    'action' => (string) $log->action,
                    'booking_ref' => $ref ? ('#' . $ref) : null,
                    'vehicle' => $vehicleName,
                ];
            })
            ->all();

        $dbOk = true;
        $dbMs = null;
        try {
            $t0 = microtime(true);
            DB::select('select 1');
            $dbMs = (int) round((microtime(true) - $t0) * 1000);
        } catch (\Throwable $e) {
            $dbOk = false;
        }

        $publicPath = storage_path('app/public');
        $storageOk = is_dir($publicPath) && is_writable($publicPath);

        return response()->json([
            'meta' => [
                'generated_at' => $now->toIso8601String(),
                'refresh_seconds' => 30,
            ],
            'kpis' => [
                'users_total' => $usersTotal,
                'users_aaracc' => $aaraccUsers,
                'members_total' => $aaraccUsers,
                'vehicles_total' => $vehiclesTotal,
                'vehicles_aaracc' => $vehiclesAaracc,
                'vehicles_rented' => $vehiclesRented,
                'vehicles_pending' => $vehiclesPending,
                'rentals_total' => $rentalsTotal,
                'service_fee_this_month' => $serviceFeeThisMonth,
                'carwash_this_month' => $carwashThisMonth,
                'carwash_total_amount' => $carwashTotalAmount,
                'bookings_rejected' => $rentalsRejected,
                'bookings_cancelled' => $rentalsCancelled,
                'owners_below_3' => $ownersBelow3,
            ],
            'rentals_status' => [
                'Pending' => $rentalsPending,
                'Confirmed' => $rentalsConfirmed,
                'Completed' => $rentalsCompleted,
                'Cancelled' => $rentalsCancelled,
                'Rejected' => $rentalsRejected,
            ],
            'charts' => [
                'rentals_by_day' => $days,
                'service_fee_by_month' => $serviceByMonth,
            ],
            'top_cars' => [
                'range_days' => $topCarsDays,
                'items' => $topCars,
            ],
            'vehicles_status' => [
                'available' => $vehiclesAvailable,
                'rented' => $vehiclesRented,
                'pending' => $vehiclesPending,
            ],
            'pending' => [
                'pending_bookings' => $rentalsPending,
            ],
            'recent' => $recent,
            'system' => [
                'server_time' => $now->toIso8601String(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'db_ok' => $dbOk,
                'db_latency_ms' => $dbMs,
                'storage_ok' => $storageOk,
            ],
        ]);
    }
}
