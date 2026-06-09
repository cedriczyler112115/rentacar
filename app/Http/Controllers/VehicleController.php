<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\LibType;
use App\Models\LibAvailabilityStatus;
use App\Models\LibTransmission;
use App\Models\LibFuelType;
use App\Models\LibBrand;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::with(['images', 'user', 'libType', 'libAvailabilityStatus', 'libTransmission', 'libFuelType', 'libBrand']);

        // Filtering
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('libBrand', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        if ($request->filled('lib_brand_id')) {
            $query->where('lib_brand_id', $request->lib_brand_id);
        }
        
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }
        
        if ($request->filled('type')) {
            $query->where('lib_type_id', $request->type);
        }
        
        if ($request->filled('transmission')) {
            $query->where('lib_transmission_id', $request->transmission);
        }
        
        if ($request->filled('fuel_type')) {
            $query->where('lib_fuel_type_id', $request->fuel_type);
        }
        
        if ($request->filled('seating_capacity')) {
            $query->where('seating_capacity', '>=', $request->seating_capacity);
        }

        if ($request->filled('availability_status')) {
            $query->where('lib_availability_status_id', $request->availability_status);
        }
        
        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low_high':
                $query->orderBy('price_per_day', 'asc');
                break;
            case 'price_high_low':
                $query->orderBy('price_per_day', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $vehicles = $query->paginate(12)->withQueryString();

        $rentedStatusId = LibAvailabilityStatus::query()
            ->whereRaw('LOWER(name) = ?', ['rented'])
            ->value('id');
        $rentedStatusId = $rentedStatusId ? (int) $rentedStatusId : null;

        $today = Carbon::today()->toDateString();
        $rentedStatusRow = $rentedStatusId ? LibAvailabilityStatus::query()->whereKey($rentedStatusId)->first() : null;

        $vehicles->getCollection()->transform(function (Vehicle $v) use ($today, $rentedStatusId, $rentedStatusRow) {
            $booked = $this->expandBookedDates($v->booked_dates ?? []);
            $filtered = array_values(array_filter($booked, fn ($d) => is_string($d) && $d >= $today));

            if (count($filtered) !== count($booked)) {
                $v->update(['booked_dates' => count($filtered) > 0 ? $filtered : null]);
                $v->booked_dates = count($filtered) > 0 ? $filtered : null;
            }

            if ($rentedStatusId && in_array($today, $filtered, true) && (int) $v->lib_availability_status_id !== $rentedStatusId) {
                $v->update(['lib_availability_status_id' => $rentedStatusId]);
                $v->lib_availability_status_id = $rentedStatusId;
                if ($rentedStatusRow) {
                    $v->setRelation('libAvailabilityStatus', $rentedStatusRow);
                }
            }

            return $v;
        });

        $ownerIds = $vehicles->getCollection()
            ->map(fn ($v) => (int) ($v->user_id ?? 0))
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

        $types = LibType::orderBy('name')->get();
        $transmissions = LibTransmission::orderBy('name')->get();
        $fuels = LibFuelType::orderBy('name')->get();
        $brands = LibBrand::orderBy('name')->get();
        $statuses = LibAvailabilityStatus::orderBy('name')->get();

        return view('vehicles.index', compact('vehicles', 'types', 'transmissions', 'fuels', 'brands', 'statuses', 'ownerRatings'));
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
}
