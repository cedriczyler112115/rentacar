<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\LibBrand;
use App\Models\LibType;
use App\Models\LibTransmission;
use App\Models\LibFuelType;
use App\Models\LibAvailabilityStatus;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PublicVehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::query()
            ->select([
                'id',
                'name',
                'lib_brand_id',
                'price_per_day',
                'color',
                'seating_capacity',
                'lib_type_id',
                'lib_availability_status_id',
                'booked_dates',
                'lib_transmission_id',
                'lib_fuel_type_id',
                'displacement',
                'year_model',
                'user_id',
            ])
            ->with([
                'images' => fn ($q) => $q
                    ->select(['id', 'vehicle_id', 'image_path', 'is_primary'])
                    ->orderByDesc('is_primary')
                    ->orderBy('id'),
                'libBrand:id,name',
                'libType:id,name',
                'libTransmission:id,name',
                'libFuelType:id,name',
                'libAvailabilityStatus:id,name',
            ]);
        $allowedStatusIds = LibAvailabilityStatus::whereIn(DB::raw('LOWER(name)'), ['available', 'pending'])->pluck('id')->all();
        if (count($allowedStatusIds) > 0) {
            $query->whereIn('lib_availability_status_id', $allowedStatusIds);
        }

        // Quick Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhereHas('libBrand', function($brandQuery) use ($request) {
                      $brandQuery->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Filters
        if ($request->filled('lib_brand_id')) {
            $query->where('lib_brand_id', $request->lib_brand_id);
        }

        if ($request->filled('lib_type_id')) {
            $query->where('lib_type_id', $request->lib_type_id);
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
            if (in_array((int) $request->availability_status, $allowedStatusIds, true)) {
                $query->where('lib_availability_status_id', $request->availability_status);
            }
        }
        
        if ($request->filled('min_price')) {
            $query->where('price_per_day', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('price_per_day', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_low_high') {
            $query->orderBy('price_per_day', 'asc');
        } elseif ($sort === 'price_high_low') {
            $query->orderBy('price_per_day', 'desc');
        } else {
            $query->latest();
        }

        $vehicles = $query->paginate(12)->withQueryString()->fragment('fleet');
        
        $brands = LibBrand::orderBy('name')->get();
        $types = LibType::orderBy('name')->get();
        $transmissions = LibTransmission::orderBy('name')->get();
        $fuels = LibFuelType::orderBy('name')->get();
        $statuses = LibAvailabilityStatus::whereIn(DB::raw('LOWER(name)'), ['available', 'pending'])->orderBy('name')->get();

        if ($request->ajax() || $request->boolean('ajax')) {
            return view('partials.public_vehicle_grid', compact('vehicles'));
        }

        $faqs = Schema::hasTable('faqs')
            ? Faq::query()->where('is_active', true)->orderBy('sort_order')->orderBy('id')->get()
            : collect();

        return view('welcome', compact('vehicles', 'brands', 'types', 'transmissions', 'fuels', 'statuses', 'faqs'));
    }
}
