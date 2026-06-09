<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LibAvailabilityStatus;
use App\Models\LibMunicipality;
use App\Models\LibType;
use App\Models\Rental;
use App\Models\RentalLog;
use App\Models\Vehicle;
use App\Services\BookingEmailService;
use App\Services\MunicipalityTypePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DispatchingController extends Controller
{
    public function index(Request $request): View
    {
        $types = collect();
        $vehicles = collect();
        $activeTypeId = null;
        $error = null;

        try {
            $types = LibType::query()->orderBy('created_at', 'asc')->get();
            $activeTypeId = (int) $request->query('type_id', $types->first()?->id);
            if ($activeTypeId <= 0) {
                $activeTypeId = $types->first()?->id;
            }

            $vehicles = $this->queryVehiclesForType($activeTypeId)->get();
        } catch (\Throwable $e) {
            $error = 'Failed to load dispatching data. Please try again.';
        }

        return view('admin.dispatching.index', compact('types', 'activeTypeId', 'vehicles', 'error'));
    }

    public function vehicles(Request $request)
    {
        $typeId = (int) $request->query('type_id');
        try {
            $vehicles = $this->queryVehiclesForType($typeId)->get();
            return view('admin.dispatching.partials.vehicle_grid', compact('vehicles'));
        } catch (\Throwable $e) {
            return response()->view('admin.dispatching.partials.error', [
                'message' => 'Failed to load vehicles for this tab. Please try again.',
            ], 500);
        }
    }

    public function dispatchForm(Request $request)
    {
        $vehicleId = (int) $request->query('vehicle_id');
        if ($vehicleId <= 0) {
            return response()->view('admin.dispatching.partials.error', ['message' => 'Invalid vehicle.'], 422);
        }

        try {
            $vehicle = Vehicle::with(['images', 'libAvailabilityStatus', 'libBrand', 'libType', 'libTransmission', 'libFuelType', 'user'])
                ->whereKey($vehicleId)
                ->whereHas('user', fn ($q) => $q->where('is_aaracc', true))
                ->firstOrFail();

            $municipalities = LibMunicipality::all()->groupBy(['region', 'province']);
            $typePriceMap = DB::table('lib_municipality_type_prices')
                ->where('lib_type_id', $vehicle->lib_type_id)
                ->pluck('price', 'lib_municipality_id');

            return view('admin.dispatching.partials.dispatch_form', compact('vehicle', 'municipalities', 'typePriceMap'));
        } catch (\Throwable $e) {
            return response()->view('admin.dispatching.partials.error', ['message' => 'Failed to load dispatch form.'], 500);
        }
    }

    public function dispatchStore(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|integer|min:1',
            'region' => 'required|string',
            'province' => 'required|string',
            'municipality' => 'required|string',
            'datetime_from' => 'required|date|after_or_equal:today',
            'datetime_to' => 'required|date|after:datetime_from',
            'pickup_location' => 'required|string|max:255',
            'downpayment_amount' => 'nullable|numeric|min:0',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'drivers_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'additional_message' => 'nullable|string|max:5000',
        ]);

        $vehicleId = (int) $request->vehicle_id;
        $vehicle = Vehicle::with(['libType', 'user'])
            ->whereKey($vehicleId)
            ->whereHas('user', fn ($q) => $q->where('is_aaracc', true))
            ->firstOrFail();

        $municipality = LibMunicipality::where('municipality', $request->municipality)
            ->where('province', $request->province)
            ->where('region', $request->region)
            ->firstOrFail();

        $paths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $paths[] = $file->store('rentals/attachments', 'public');
            }
        }

        $driversLicensePath = null;
        if ($request->hasFile('drivers_license')) {
            $driversLicensePath = $request->file('drivers_license')->store('rentals/drivers-licenses', 'public');
        }

        $from = \Carbon\Carbon::parse($request->datetime_from);
        $to = \Carbon\Carbon::parse($request->datetime_to);

        $requestedDates = $this->datesFromDateTimeRange($from, $to);
        $existingBooked = $this->expandBookedDates($vehicle->booked_dates ?? []);
        if (count($requestedDates) > 0 && count(array_intersect($requestedDates, $existingBooked)) > 0) {
            return response()->json([
                'message' => 'Selected dates include already booked date(s). Please choose other dates.',
            ], 422);
        }

        $totalHours = $from->diffInHours($to);
        $days = floor($totalHours / 24);
        $extra_hours = $totalHours % 24;

        if ($days == 0 && $extra_hours > 0) {
            $days = 1;
            $extra_hours = 0;
        }

        $pricing = new MunicipalityTypePricingService();
        try {
            $municipalityRate = $pricing->getPriceForType($municipality->id, (int) $vehicle->lib_type_id);
        } catch (ValidationException $e) {
            throw $e;
        }

        $destination_price = $municipalityRate * ($days > 0 ? $days : 1);
        $carwash_fee = (float) ($vehicle->libType->carwash_fee ?? 0);
        $extra_hours_fee = 0;

        if ($extra_hours > 0) {
            if ($extra_hours > 5) {
                $extra_hours_fee = $municipalityRate;
            } else {
                $extra_hours_fee = $extra_hours * 200;
            }
        }

        $estimated_price = $destination_price + $carwash_fee + $extra_hours_fee;

        $availableStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['available'])->value('id') ?? 1;
        $rentedStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['rented'])->value('id');
        if (!$rentedStatusId) {
            return response()->json([
                'message' => 'Rented availability status is not configured.',
            ], 422);
        }

        $currentStatusId = (int) $vehicle->lib_availability_status_id;
        if (!in_array($currentStatusId, [(int) $availableStatusId], true)) {
            return response()->json([
                'message' => 'This vehicle is not available for dispatch right now.',
            ], 422);
        }

        $prev = [
            'status' => null,
            'vehicle_lib_availability_status_id' => $vehicle->lib_availability_status_id,
        ];

        $rental = null;
        DB::transaction(function () use ($request, $vehicle, $paths, $driversLicensePath, $destination_price, $carwash_fee, $extra_hours, $extra_hours_fee, $estimated_price, $rentedStatusId, $requestedDates, $existingBooked, &$rental) {
            $rental = Rental::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicle->id,
                'pickup_location' => $request->pickup_location,
                'region' => $request->region,
                'province' => $request->province,
                'municipality' => $request->municipality,
                'destination_price' => $destination_price,
                'has_carwash' => true,
                'carwash_fee' => $carwash_fee,
                'extra_hours' => $extra_hours,
                'extra_hours_fee' => $extra_hours_fee,
                'datetime_from' => $request->datetime_from,
                'datetime_to' => $request->datetime_to,
                'estimated_price' => $estimated_price,
                'downpayment_amount' => $request->downpayment_amount,
                'downpayment_attachments' => count($paths) > 0 ? $paths : null,
                'drivers_license_path' => $driversLicensePath,
                'additional_message' => $request->additional_message,
                'referral' => 'AARACC Booking',
                'status' => 'Confirmed',
            ]);

            $merged = array_values(array_unique(array_merge($existingBooked, $requestedDates)));
            sort($merged);

            $vehicle->update([
                'lib_availability_status_id' => $rentedStatusId,
                'booked_dates' => count($merged) > 0 ? $merged : null,
            ]);
        });

        RentalLog::create([
            'rental_id' => $rental->id,
            'user_id' => Auth::id(),
            'action' => 'internal_dispatch_confirmed',
            'previous_values' => $prev,
            'new_values' => [
                'status' => 'Confirmed',
                'vehicle_lib_availability_status_id' => $rentedStatusId,
            ],
        ]);

        $rental->load(['user', 'vehicle.user', 'vehicle.libBrand', 'vehicle.libType', 'vehicle.libTransmission', 'vehicle.libFuelType']);
        app(BookingEmailService::class)->sendDispatchCreatedOwner($rental);

        return response()->json([
            'success' => true,
            'message' => 'Dispatch booking has been created and confirmed.',
        ]);
    }

    private function queryVehiclesForType(?int $typeId)
    {
        $lastRentalSub = Rental::query()
            ->selectRaw('vehicle_id, MAX(datetime_to) as last_rented_at')
            ->where('status', 'Completed')
            ->groupBy('vehicle_id');

        $availableStatusIds = LibAvailabilityStatus::query()
            ->where(function ($q) {
                $q->whereRaw('LOWER(name) = ?', ['available'])
                    ->orWhere('id', 1);
            })
            ->pluck('id')
            ->unique()
            ->values()
            ->all();

        $query = Vehicle::query()
            ->select('vehicles.*')
            ->addSelect('lr.last_rented_at')
            ->leftJoinSub($lastRentalSub, 'lr', function ($join) {
                $join->on('lr.vehicle_id', '=', 'vehicles.id');
            })
            ->with(['images', 'libBrand', 'libType', 'libAvailabilityStatus', 'user'])
            ->when($typeId && $typeId > 0, fn ($q) => $q->where('lib_type_id', $typeId))
            ->whereHas('user', fn ($q) => $q->where('is_aaracc', true))
            ->when(
                count($availableStatusIds) > 0,
                fn ($q) => $q->whereIn('lib_availability_status_id', $availableStatusIds),
                fn ($q) => $q->whereRaw('1=0')
            )
            ->orderByRaw('CASE WHEN lr.last_rented_at IS NULL THEN 0 ELSE 1 END ASC')
            ->orderBy('lr.last_rented_at', 'asc')
            ->orderByDesc('vehicles.id');

        return $query;
    }

    private function expandBookedDates($raw): array
    {
        $items = is_array($raw) ? $raw : [];
        $dates = [];

        foreach ($items as $item) {
            if (is_string($item)) {
                $dates[] = $item;
                continue;
            }
            if (is_array($item) && isset($item['start'], $item['end'])) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('Y-m-d', (string) $item['start'])->startOfDay();
                    $end = \Carbon\Carbon::createFromFormat('Y-m-d', (string) $item['end'])->startOfDay();
                    if ($end->lt($start)) {
                        continue;
                    }
                    $cur = $start->copy();
                    while ($cur->lte($end)) {
                        $dates[] = $cur->format('Y-m-d');
                        $cur->addDay();
                    }
                } catch (\Throwable $e) {
                }
            }
        }

        $dates = array_values(array_unique(array_filter($dates, fn ($d) => is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d))));
        sort($dates);
        return $dates;
    }

    private function datesFromDateTimeRange(\Carbon\Carbon $from, \Carbon\Carbon $to): array
    {
        $start = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();
        if ($end->lt($start)) {
            return [];
        }

        $dates = [];
        $cur = $start->copy();
        while ($cur->lte($end)) {
            $dates[] = $cur->format('Y-m-d');
            $cur->addDay();
        }

        return $dates;
    }
}
