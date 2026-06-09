<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Rental;
use App\Models\RentalLog;
use App\Services\MunicipalityTypePricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use App\Models\LibMunicipality;
use App\Models\LibAvailabilityStatus;
use App\Models\Faq;
use App\Models\Review;
use App\Services\BookingEmailService;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Rental::where('user_id', Auth::id());

        // Get counts for each status
        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $allCount = $statusCounts->sum();

        // Main query for bookings
        $query = (clone $baseQuery)->with([
            'user', 
            'vehicle.images', 
            'vehicle.user',
            'vehicle.libBrand', 
            'vehicle.libType', 
            'vehicle.libTransmission', 
            'vehicle.libFuelType',
            'logs.user',
            'review',
        ])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        $ownerIds = $bookings->getCollection()
            ->map(fn ($b) => (int) ($b->vehicle?->user_id ?? 0))
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $ownerRatings = [];
        if (count($ownerIds) > 0) {
            $rows = Review::query()
                ->selectRaw('owner_id, AVG(rating) as avg_rating, COUNT(*) as total_reviews')
                ->whereIn('owner_id', $ownerIds)
                ->groupBy('owner_id')
                ->get();

            foreach ($rows as $r) {
                $ownerRatings[(int) $r->owner_id] = [
                    'avg' => $r->avg_rating ? round((float) $r->avg_rating, 2) : 0,
                    'count' => (int) $r->total_reviews,
                ];
            }
        }

        $myOwnerRow = Review::query()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->where('owner_id', Auth::id())
            ->first();

        $myOwnerRatingAvg = $myOwnerRow?->avg_rating ? round((float) $myOwnerRow->avg_rating, 2) : 0;
        $myOwnerRatingCount = (int) ($myOwnerRow?->total_reviews ?? 0);

        return view('dashboard', compact('bookings', 'statusCounts', 'allCount', 'ownerRatings', 'myOwnerRatingAvg', 'myOwnerRatingCount'));
    }

    public function manageOwnedBookings(Request $request)
    {
        $baseQuery = Rental::whereHas('vehicle', function ($query) {
            $query->where('user_id', Auth::id());
        });

        $ownedVehicles = Vehicle::where('user_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'year_model']);

        if ($request->filled('vehicle_id')) {
            $baseQuery->where('vehicle_id', $request->vehicle_id);
        }

        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $allCount = $statusCounts->sum();

        $query = (clone $baseQuery)->with([
            'user',
            'vehicle.images',
            'vehicle.libBrand',
            'vehicle.libType',
            'vehicle.libTransmission',
            'vehicle.libFuelType',
            'logs.user',
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('sort') && $request->sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->latest();
        }

        $bookings = $query->paginate(10)->withQueryString();
        
        return view('bookings.manage', compact('bookings', 'statusCounts', 'allCount', 'ownedVehicles'));
    }

    public function confirm(Request $request, Rental $rental)
    {
        if ($rental->vehicle->user_id !== Auth::id()) {
            abort(403);
        }
        if ($rental->status !== 'Pending') {
            abort(422);
        }
        $rentedStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['rented'])->value('id') ?? 2;
        $availableStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['available'])->value('id') ?? 1;
        $startDate = $rental->datetime_from ? Carbon::parse($rental->datetime_from)->startOfDay() : Carbon::today();
        $daysUntilStart = Carbon::today()->diffInDays($startDate, false);
        $nextVehicleStatusId = $daysUntilStart <= 2 ? (int) $rentedStatusId : (int) $availableStatusId;
        $prev = [
            'status' => $rental->status,
            'vehicle_lib_availability_status_id' => $rental->vehicle->lib_availability_status_id,
        ];

        DB::transaction(function () use ($rental, $nextVehicleStatusId) {
            $rental->update(['status' => 'Confirmed']);
            $rental->vehicle->update(['lib_availability_status_id' => $nextVehicleStatusId]);
        });

        RentalLog::create([
            'rental_id' => $rental->id,
            'user_id' => Auth::id(),
            'action' => 'confirmed',
            'previous_values' => $prev,
            'new_values' => [
                'status' => 'Confirmed',
                'vehicle_lib_availability_status_id' => $nextVehicleStatusId,
            ],
        ]);
        $rental->load(['user', 'vehicle.user', 'vehicle.libBrand', 'vehicle.libType', 'vehicle.libTransmission', 'vehicle.libFuelType']);
        app(BookingEmailService::class)->sendBookingConfirmedRenter($rental);
        return response()->json(['success' => true, 'status' => $rental->status]);
    }

    public function cancel(Request $request, Rental $rental)
    {
        if ($rental->vehicle->user_id !== Auth::id()) {
            abort(403);
        }
        if ($rental->status !== 'Pending') {
            abort(422);
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $rejectionReason = trim((string) $validated['rejection_reason']);
        $availableStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['available'])->value('id') ?? 1;
        $prev = [
            'status' => $rental->status,
            'vehicle_lib_availability_status_id' => $rental->vehicle->lib_availability_status_id,
        ];

        DB::transaction(function () use ($rental, $availableStatusId) {
            $rental->update(['status' => 'Rejected']);
            $rental->vehicle->update(['lib_availability_status_id' => $availableStatusId]);
            $this->removeVehicleBookedDatesForRental($rental);
        });

        RentalLog::create([
            'rental_id' => $rental->id,
            'user_id' => Auth::id(),
            'action' => 'rejected',
            'previous_values' => $prev,
            'new_values' => [
                'status' => 'Rejected',
                'vehicle_lib_availability_status_id' => $availableStatusId,
                'rejection_reason' => $rejectionReason,
            ],
        ]);
        $rental->load(['user', 'vehicle.user', 'vehicle.libBrand', 'vehicle.libType', 'vehicle.libTransmission', 'vehicle.libFuelType']);
        app(BookingEmailService::class)->sendBookingRejectedRenter($rental, $rejectionReason);
        return response()->json(['success' => true, 'status' => $rental->status]);
    }

    public function cancelByRenter(Request $request, Rental $rental)
    {
        if ($rental->user_id !== Auth::id()) {
            abort(403);
        }
        if ($rental->status !== 'Pending') {
            abort(422);
        }
        $availableStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['available'])->value('id') ?? 1;
        $prev = [
            'status' => $rental->status,
            'vehicle_lib_availability_status_id' => $rental->vehicle->lib_availability_status_id,
        ];

        DB::transaction(function () use ($rental, $availableStatusId) {
            $rental->update(['status' => 'Cancelled']);
            $rental->vehicle->update(['lib_availability_status_id' => $availableStatusId]);
            $this->removeVehicleBookedDatesForRental($rental);
        });

        RentalLog::create([
            'rental_id' => $rental->id,
            'user_id' => Auth::id(),
            'action' => 'cancelled_by_renter',
            'previous_values' => $prev,
            'new_values' => [
                'status' => 'Cancelled',
                'vehicle_lib_availability_status_id' => $availableStatusId,
            ],
        ]);
        $rental->load(['user', 'vehicle.user', 'vehicle.libBrand', 'vehicle.libType', 'vehicle.libTransmission', 'vehicle.libFuelType']);
        app(BookingEmailService::class)->sendBookingCancelledOwner($rental, 'Cancelled by renter');
        return response()->json(['success' => true, 'status' => $rental->status]);
    }

    public function complete(Request $request, Rental $rental)
    {
        if ($rental->vehicle->user_id !== Auth::id()) {
            abort(403);
        }
        if ($rental->status !== 'Confirmed') {
            abort(422);
        }
        $validated = $request->validate([
            'actual_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $availableStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['available'])->value('id') ?? 1;
        $prev = [
            'status' => $rental->status,
            'vehicle_lib_availability_status_id' => $rental->vehicle->lib_availability_status_id,
            'actual_price' => $rental->actual_price,
        ];

        DB::transaction(function () use ($rental, $availableStatusId, $validated) {
            $rental->update([
                'status' => 'Completed',
                'actual_price' => $validated['actual_price'] ?? $rental->estimated_price,
            ]);
            $rental->vehicle->update(['lib_availability_status_id' => $availableStatusId]);
            $this->removeVehicleBookedDatesForRental($rental);
        });

        RentalLog::create([
            'rental_id' => $rental->id,
            'user_id' => Auth::id(),
            'action' => 'completed',
            'previous_values' => $prev,
            'new_values' => [
                'status' => 'Completed',
                'vehicle_lib_availability_status_id' => $availableStatusId,
            ],
        ]);
        $rental->load(['user', 'vehicle.user', 'vehicle.libBrand', 'vehicle.libType', 'vehicle.libTransmission', 'vehicle.libFuelType']);
        app(BookingEmailService::class)->sendBookingCompletedRenter($rental);
        return response()->json(['success' => true, 'status' => $rental->status]);
    }
    public function create($enc_id)
    {
        try {
            $id = Crypt::decrypt($enc_id);
            $vehicle = Vehicle::with(['images', 'user', 'libAvailabilityStatus', 'libBrand', 'libType', 'libTransmission'])->findOrFail($id);
        } catch (DecryptException $e) {
            abort(404, 'Invalid Vehicle Booking Link.');
        }

        $municipalities = LibMunicipality::all()->groupBy(['region', 'province']);
        $typePriceMap = DB::table('lib_municipality_type_prices')
            ->where('lib_type_id', $vehicle->lib_type_id)
            ->pluck('price', 'lib_municipality_id');

        $ownerId = (int) ($vehicle->user_id ?? 0);
        $ownerRow = $ownerId > 0
            ? Review::query()
                ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
                ->where('owner_id', $ownerId)
                ->first()
            : null;

        $ownerAvgRating = $ownerRow?->avg_rating ? round((float) $ownerRow->avg_rating, 2) : 0;
        $ownerTotalReviews = (int) ($ownerRow?->total_reviews ?? 0);

        $faqs = \Illuminate\Support\Facades\Schema::hasTable('faqs')
            ? Faq::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return view('rentals.create', compact('vehicle', 'enc_id', 'municipalities', 'typePriceMap', 'ownerAvgRating', 'ownerTotalReviews', 'faqs'));
    }

    public function store(Request $request, $enc_id)
    {
        try {
            $id = Crypt::decrypt($enc_id);
            $vehicle = Vehicle::with(['libType'])->findOrFail($id);
        } catch (DecryptException $e) {
            abort(404, 'Invalid Vehicle Booking Link.');
        }

        $request->validate([
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
            'agree_terms' => 'accepted',
        ]);

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

        // Computations
        $from = Carbon::parse($request->datetime_from);
        $to = Carbon::parse($request->datetime_to);

        $requestedDates = $this->datesFromDateTimeRange($from, $to);
        $existingBooked = $this->expandBookedDates($vehicle->booked_dates ?? []);
        if (count(array_intersect($requestedDates, $existingBooked)) > 0) {
            return back()->withErrors([
                'datetime_from' => 'Selected dates include already booked date(s). Please choose other dates.',
            ])->withInput();
        }
        
        $totalHours = $from->diffInHours($to);
        $days = floor($totalHours / 24);
        $extra_hours = $totalHours % 24;

        if ($days == 0 && $extra_hours > 0) {
            $days = 1;
            $extra_hours = 0;
        }

        // Vehicle base price is excluded from the estimated_price as it's just an estimate
        // Destination surcharge is multiplied by the number of days
        $pricing = new MunicipalityTypePricingService();
        try {
            $municipalityRate = $pricing->getPriceForType($municipality->id, (int) $vehicle->lib_type_id);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
        $destination_price = $municipalityRate * ($days > 0 ? $days : 1);
        
        $carwash_fee = (float) ($vehicle->libType->carwash_fee ?? 0);
        
        $extra_hours_fee = 0;
        
        if ($extra_hours > 0) {
            if ($extra_hours > 5) {
                // More than 5 hours is computed as a full day destination rate (based on travel area)
                $extra_hours_fee = $municipalityRate;
            } else {
                // 200 PHP per hour up to 5 hours
                $extra_hours_fee = $extra_hours * 200;
            }
        }

        $estimated_price = $destination_price + $carwash_fee + $extra_hours_fee;

        $availableStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['available'])->value('id') ?? 1;
        $pendingStatusId = LibAvailabilityStatus::whereRaw('LOWER(name) = ?', ['pending'])->value('id');

        if (!$pendingStatusId) {
            return back()->withErrors(['municipality' => 'Pending availability status is not configured.'])->withInput();
        }

        $currentStatusId = (int) $vehicle->lib_availability_status_id;
        if (!in_array($currentStatusId, [(int) $availableStatusId, (int) $pendingStatusId], true)) {
            return back()->withErrors(['municipality' => 'This vehicle is not available for booking right now.'])->withInput();
        }

        $rental = DB::transaction(function () use ($request, $vehicle, $paths, $driversLicensePath, $destination_price, $carwash_fee, $extra_hours, $extra_hours_fee, $estimated_price, $pendingStatusId, $requestedDates) {
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
                'referral' => 'Online Booking',
                'status' => 'Pending',
            ]);

            $vehicleBooked = $this->expandBookedDates($vehicle->booked_dates ?? []);
            $merged = array_values(array_unique(array_merge($vehicleBooked, $requestedDates)));
            sort($merged);

            $vehicle->update([
                'lib_availability_status_id' => $pendingStatusId,
                'booked_dates' => $merged,
            ]);
            return $rental;
        });

        $rental->load(['user', 'vehicle.user', 'vehicle.libBrand', 'vehicle.libType', 'vehicle.libTransmission', 'vehicle.libFuelType']);
        app(BookingEmailService::class)->sendBookingCreatedOwner($rental);

        return redirect()->route('dashboard')->with('success', 'Your booking request has been securely submitted! We will contact you shortly.');
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
                    $start = Carbon::createFromFormat('Y-m-d', (string) $item['start'])->startOfDay();
                    $end = Carbon::createFromFormat('Y-m-d', (string) $item['end'])->startOfDay();
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

    private function datesFromDateTimeRange(Carbon $from, Carbon $to): array
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

    private function removeVehicleBookedDatesForRental(Rental $rental): void
    {
        $vehicle = $rental->vehicle;
        if (!$vehicle) {
            return;
        }

        $from = $rental->datetime_from ? Carbon::parse($rental->datetime_from) : null;
        $to = $rental->datetime_to ? Carbon::parse($rental->datetime_to) : null;
        if (!$from || !$to) {
            return;
        }

        $remove = $this->datesFromDateTimeRange($from, $to);
        if (count($remove) === 0) {
            return;
        }

        $existing = $this->expandBookedDates($vehicle->booked_dates ?? []);
        if (count($existing) === 0) {
            return;
        }

        $remaining = array_values(array_diff($existing, $remove));
        sort($remaining);
        $vehicle->update(['booked_dates' => count($remaining) > 0 ? $remaining : null]);
    }
}
