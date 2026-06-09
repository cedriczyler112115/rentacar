<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingCalendarController extends Controller
{
    public function index(): View
    {
        $vehicles = Vehicle::query()
            ->where('user_id', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name', 'license_plate']);

        return view('bookings.calendar', compact('vehicles'));
    }

    public function events(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'vehicle_id' => ['nullable', 'integer'],
        ]);

        $start = Carbon::parse($validated['start'])->startOfDay();
        $end = Carbon::parse($validated['end'])->endOfDay();
        $vehicleId = (int) ($validated['vehicle_id'] ?? 0);

        $query = Rental::query()
            ->with(['vehicle:id,name,license_plate,user_id', 'user:id,name,email,address'])
            ->whereHas('vehicle', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->where(function ($q) use ($start, $end) {
                $q->where('datetime_from', '<=', $end)
                    ->where('datetime_to', '>=', $start);
            })
            ->whereIn('status', ['Pending', 'Confirmed', 'Completed', 'Owner Booking'])
            ->orderBy('datetime_from');

        if ($vehicleId > 0) {
            $query->where('vehicle_id', $vehicleId);
        }

        $items = $query->get()->map(function (Rental $r) {
            $vehicleName = $r->vehicle?->name ?? 'Vehicle';
            $plate = $r->vehicle?->license_plate ?? '';
            $ref = method_exists($r, 'bookingReference') ? $r->bookingReference() : (string) $r->id;

            $from = $r->datetime_from ? Carbon::parse($r->datetime_from) : null;
            $to = $r->datetime_to ? Carbon::parse($r->datetime_to) : null;
            $days = 0;
            if ($from && $to) {
                $totalHours = $from->diffInHours($to);
                $computedDays = (int) floor($totalHours / 24);
                $extraHours = (int) ($totalHours % 24);
                if ($computedDays === 0 && $extraHours > 0) {
                    $computedDays = 1;
                }
                $days = max(0, $computedDays);
            }

            $referral = (string) ($r->referral ?? '');
            $status = (string) ($r->status ?? '');
            $isOwnerBooking = strtolower($status) === 'owner booking' || strtolower($referral) === 'owner calendar';
            $note = $isOwnerBooking ? trim((string) ($r->additional_message ?? '')) : '';

            return [
                'id' => (int) $r->id,
                'reference' => $ref,
                'vehicle_id' => (int) ($r->vehicle_id ?? 0),
                'vehicle_name' => trim($vehicleName . ($plate ? " ($plate)" : '')),
                'status' => $status,
                'from' => $from ? $from->toIso8601String() : null,
                'to' => $to ? $to->toIso8601String() : null,
                'renter_name' => $r->user?->name,
                'renter_email' => $r->user?->email,
                'renter_address' => $r->user?->address,
                'pickup_location' => $r->pickup_location,
                'region' => $r->region,
                'province' => $r->province,
                'municipality' => $r->municipality,
                'estimated_service_fee' => (float) ($r->estimated_price ?? 0),
                'actual_service_fee' => $r->actual_price !== null ? (float) $r->actual_price : null,
                'days' => $days,
                'tag' => $isOwnerBooking ? 'owner_booking' : null,
                'referral' => $referral,
                'note' => $note !== '' ? $note : null,
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'items' => $items,
        ]);
    }

    public function vehicleEvents(Request $request, Vehicle $vehicle): JsonResponse
    {
        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $start = Carbon::parse($validated['start'])->startOfDay();
        $end = Carbon::parse($validated['end'])->endOfDay();

        $items = Rental::query()
            ->select(['id', 'vehicle_id', 'datetime_from', 'datetime_to', 'status', 'referral', 'additional_message'])
            ->where('vehicle_id', $vehicle->id)
            ->where(function ($q) use ($start, $end) {
                $q->where('datetime_from', '<=', $end)
                    ->where('datetime_to', '>=', $start);
            })
            ->whereIn('status', ['Pending', 'Confirmed', 'Completed', 'Owner Booking'])
            ->orderBy('datetime_from')
            ->get()
            ->map(function (Rental $r) use ($vehicle) {
                $ref = method_exists($r, 'bookingReference') ? $r->bookingReference() : (string) $r->id;
                $from = $r->datetime_from ? Carbon::parse($r->datetime_from) : null;
                $to = $r->datetime_to ? Carbon::parse($r->datetime_to) : null;

                $status = (string) ($r->status ?? '');
                $referral = (string) ($r->referral ?? '');
                $isOwnerBooking = strtolower($status) === 'owner booking' || strtolower($referral) === 'owner calendar';
                $note = $isOwnerBooking ? trim((string) ($r->additional_message ?? '')) : '';
                $vehicleName = (string) ($vehicle->name ?? 'Vehicle');
                $plate = (string) ($vehicle->license_plate ?? '');

                return [
                    'id' => (int) $r->id,
                    'reference' => $ref,
                    'vehicle_id' => (int) ($r->vehicle_id ?? 0),
                    'vehicle_name' => trim($vehicleName . ($plate ? " ($plate)" : '')),
                    'status' => $status,
                    'from' => $from ? $from->toIso8601String() : null,
                    'to' => $to ? $to->toIso8601String() : null,
                    'tag' => $isOwnerBooking ? 'owner_booking' : null,
                    'note' => $note !== '' ? $note : null,
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'items' => $items,
        ]);
    }

    public function storeOwnerBooking(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'datetime_from' => ['required', 'date'],
            'datetime_to' => ['required', 'date', 'after:datetime_from'],
            'estimated_price' => ['nullable', 'numeric', 'min:0'],
            'actual_price' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $vehicleId = (int) $validated['vehicle_id'];
        $vehicle = Vehicle::query()
            ->where('id', $vehicleId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $from = Carbon::parse($validated['datetime_from']);
        $to = Carbon::parse($validated['datetime_to']);

        $requestedDates = $this->datesFromDateTimeRange($from, $to);
        $existingBooked = $this->expandBookedDates($vehicle->booked_dates ?? []);
        if (count(array_intersect($requestedDates, $existingBooked)) > 0) {
            throw ValidationException::withMessages([
                'datetime_from' => 'Selected dates include already booked date(s). Please choose other dates.',
            ]);
        }

        $note = isset($validated['note']) ? trim((string) $validated['note']) : null;
        $rental = null;

        DB::transaction(function () use ($vehicle, $from, $to, $requestedDates, $existingBooked, $note, &$rental) {
            $merged = array_values(array_unique(array_merge($existingBooked, $requestedDates)));
            sort($merged);

            $rental = Rental::create([
                'user_id' => Auth::id(),
                'vehicle_id' => $vehicle->id,
                'pickup_location' => 'Owner Calendar',
                'region' => 'Owner Calendar',
                'province' => 'Owner Calendar',
                'municipality' => 'Owner Calendar',
                'destination_price' => 0,
                'has_carwash' => false,
                'carwash_fee' => 0,
                'extra_hours' => 0,
                'extra_hours_fee' => 0,
                'datetime_from' => $from,
                'datetime_to' => $to,
                'estimated_price' => $validated['estimated_price'] ?? 0,
                'actual_price' => $validated['actual_price'] ?? null,
                'downpayment_amount' => null,
                'downpayment_attachments' => null,
                'drivers_license_path' => null,
                'additional_message' => $note ?: null,
                'referral' => 'Owner Calendar',
                'status' => 'Owner Booking',
            ]);

            $vehicle->update([
                'booked_dates' => $merged,
            ]);
        });

        return response()->json([
            'ok' => true,
            'rental_id' => (int) ($rental?->id ?? 0),
        ]);
    }

    public function updateOwnerBooking(Request $request, Rental $rental): JsonResponse
    {
        if ($rental->status !== 'Owner Booking' || $rental->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer', 'min:1'],
            'datetime_from' => ['required', 'date'],
            'datetime_to' => ['required', 'date', 'after:datetime_from'],
            'estimated_price' => ['nullable', 'numeric', 'min:0'],
            'actual_price' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:5000'],
        ]);

        $vehicleId = (int) $validated['vehicle_id'];
        $vehicle = Vehicle::query()
            ->where('id', $vehicleId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $from = Carbon::parse($validated['datetime_from']);
        $to = Carbon::parse($validated['datetime_to']);

        $requestedDates = $this->datesFromDateTimeRange($from, $to);
        $oldDates = $this->datesFromDateTimeRange(Carbon::parse($rental->datetime_from), Carbon::parse($rental->datetime_to));
        
        $otherBooked = array_diff($this->expandBookedDates($rental->vehicle->booked_dates ?? []), $oldDates);
        
        if (count(array_intersect($requestedDates, $otherBooked)) > 0) {
            throw ValidationException::withMessages([
                'datetime_from' => 'Selected dates include already booked date(s). Please choose other dates.',
            ]);
        }

        DB::transaction(function () use ($rental, $vehicle, $from, $to, $requestedDates, $otherBooked, $validated) {
            $merged = array_values(array_unique(array_merge($otherBooked, $requestedDates)));
            sort($merged);

            $rental->update([
                'vehicle_id' => $vehicle->id,
                'datetime_from' => $from,
                'datetime_to' => $to,
                'estimated_price' => $validated['estimated_price'] ?? 0,
                'actual_price' => $validated['actual_price'] ?? null,
                'additional_message' => isset($validated['note']) ? trim((string) $validated['note']) : null,
            ]);

            // If switching vehicles (rare but possible), clear old vehicle dates
            if ($rental->getOriginal('vehicle_id') !== $vehicle->id) {
                $oldVehicle = Vehicle::find($rental->getOriginal('vehicle_id'));
                if ($oldVehicle) {
                    $cleanedOldBooked = array_diff($this->expandBookedDates($oldVehicle->booked_dates ?? []), $oldDates);
                    $oldVehicle->update(['booked_dates' => array_values($cleanedOldBooked)]);
                }
            }

            $vehicle->update([
                'booked_dates' => $merged,
            ]);
        });

        return response()->json(['ok' => true]);
    }

    public function destroyOwnerBooking(Request $request, Rental $rental): JsonResponse
    {
        if ($rental->status !== 'Owner Booking' || $rental->user_id !== Auth::id()) {
            abort(403);
        }

        $vehicle = $rental->vehicle;
        $oldDates = $this->datesFromDateTimeRange(Carbon::parse($rental->datetime_from), Carbon::parse($rental->datetime_to));
        $otherBooked = array_diff($this->expandBookedDates($vehicle->booked_dates ?? []), $oldDates);

        DB::transaction(function () use ($rental, $vehicle, $otherBooked) {
            $vehicle->update([
                'booked_dates' => count(array_values($otherBooked)) > 0 ? array_values($otherBooked) : null,
            ]);
            $rental->delete();
        });

        return response()->json(['ok' => true]);
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
}
