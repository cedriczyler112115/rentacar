<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\LibType;
use App\Models\LibAvailabilityStatus;
use App\Models\LibTransmission;
use App\Models\LibFuelType;
use App\Models\LibBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class MyVehicleController extends Controller
{
    public function index(Request $request)
    {
        $query = Vehicle::where('user_id', Auth::id())
                        ->with(['images', 'libType', 'libAvailabilityStatus', 'libTransmission', 'libFuelType', 'libBrand']);

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

        $types = LibType::orderBy('name')->get();
        $statuses = LibAvailabilityStatus::orderBy('name')->get();
        $transmissions = LibTransmission::orderBy('name')->get();
        $fuels = LibFuelType::orderBy('name')->get();
        $brands = LibBrand::orderBy('name')->get();

        return view('vehicles.my_cars', compact('vehicles', 'types', 'statuses', 'transmissions', 'fuels', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|regex:/^\S+$/',
            'color' => 'nullable|string|max:50',
            'lib_brand_id' => 'required|exists:lib_brands,id',
            'lib_type_id' => 'required|exists:lib_types,id',
            'price_per_day' => 'required|numeric|min:0',
            'lib_availability_status_id' => 'required|exists:lib_availability_statuses,id',
            'booked_dates' => 'nullable|string',
            'lib_transmission_id' => 'required|exists:lib_transmissions,id',
            'lib_fuel_type_id' => 'required|exists:lib_fuel_types,id',
            'year_model' => 'nullable|string|max:10',
            'seating_capacity' => 'required|integer|min:1',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:2048',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['license_plate'] = strtoupper(preg_replace('/\s+/', '', $validated['license_plate']));
        $validated['booked_dates'] = $this->parseBookedDates($request->input('booked_dates'));

        $my_car = Vehicle::create($validated);

        if ($request->hasFile('images')) {
            $isFirst = true;
            foreach ($request->file('images') as $i => $file) {
                $isPrim = $request->filled('primary_upload_index') ? ((int)$request->primary_upload_index === $i) : $isFirst;
                $path = $file->store('vehicles', 'public');
                VehicleImage::create([
                    'vehicle_id' => $my_car->id,
                    'image_path' => $path,
                    'is_primary' => $isPrim,
                ]);
                $isFirst = false;
            }
        }

        return redirect()->route('my-cars.index')->with('success', 'Vehicle added successfully.');
    }

    public function update(Request $request, Vehicle $my_car)
    {
        if ($my_car->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'license_plate' => 'required|string|max:20|regex:/^\S+$/',
            'color' => 'nullable|string|max:50',
            'lib_brand_id' => 'required|exists:lib_brands,id',
            'lib_type_id' => 'required|exists:lib_types,id',
            'price_per_day' => 'required|numeric|min:0',
            'lib_availability_status_id' => 'required|exists:lib_availability_statuses,id',
            'booked_dates' => 'nullable|string',
            'lib_transmission_id' => 'required|exists:lib_transmissions,id',
            'lib_fuel_type_id' => 'required|exists:lib_fuel_types,id',
            'year_model' => 'nullable|string|max:10',
            'seating_capacity' => 'required|integer|min:1',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:2048',
            'delete_image_ids' => 'nullable|string',
        ]);

        $validated['license_plate'] = strtoupper(preg_replace('/\s+/', '', $validated['license_plate']));
        if ($request->has('booked_dates')) {
            $validated['booked_dates'] = $this->parseBookedDates($request->input('booked_dates'));
        } else {
            unset($validated['booked_dates']);
        }
        $my_car->update($validated);

        $deleteIds = $this->parseDeleteImageIds($request->input('delete_image_ids'));
        $existingCount = $my_car->images()->count();
        $imagesToDelete = $deleteIds
            ? VehicleImage::query()->where('vehicle_id', $my_car->id)->whereIn('id', $deleteIds)->get()
            : collect();
        $deleteCount = $imagesToDelete->count();

        $newCount = $request->hasFile('images') ? count($request->file('images')) : 0;
        if (($existingCount - $deleteCount + $newCount) > 6) {
            return back()->withErrors(['images' => 'You can only have up to 6 images per vehicle.'])->withInput();
        }

        foreach ($imagesToDelete as $img) {
            Storage::disk('public')->delete($img->image_path);
            $img->delete();
        }

        if ($request->filled('primary_image_id')) {
            $primaryId = (int) $request->primary_image_id;
            $exists = VehicleImage::query()->where('vehicle_id', $my_car->id)->whereKey($primaryId)->exists();
            if ($exists) {
                VehicleImage::where('vehicle_id', $my_car->id)->update(['is_primary' => false]);
                VehicleImage::where('vehicle_id', $my_car->id)->whereKey($primaryId)->update(['is_primary' => true]);
            }
        }

        // Append new images if provided
        if ($request->hasFile('images')) {
            // Check if vehicle has any primary image currently
            $hasPrimary = $my_car->images()->where('is_primary', true)->exists() && !($request->filled('primary_upload_index'));
            
            foreach ($request->file('images') as $i => $file) {
                $isPrim = $request->filled('primary_upload_index') ? ((int)$request->primary_upload_index === $i) : !$hasPrimary;
                $path = $file->store('vehicles', 'public');
                $img = VehicleImage::create([
                    'vehicle_id' => $my_car->id,
                    'image_path' => $path,
                    'is_primary' => $isPrim,
                ]);
                if($isPrim) {
                    VehicleImage::where('vehicle_id', $my_car->id)->where('id', '!=', $img->id)->update(['is_primary' => false]);
                }
                $hasPrimary = true;
            }
        }

        $hasPrimary = $my_car->images()->where('is_primary', true)->exists();
        if (!$hasPrimary) {
            $first = $my_car->images()->orderBy('id')->first();
            if ($first) {
                $first->update(['is_primary' => true]);
            }
        }

        return redirect()->route('my-cars.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $my_car)
    {
        if ($my_car->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        foreach ($my_car->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }
        
        $my_car->delete();

        return redirect()->route('my-cars.index')->with('success', 'Vehicle deleted successfully.');
    }

    public function setPrimaryImage(VehicleImage $image)
    {
        $vehicle = $image->vehicle;
        if ($vehicle->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        VehicleImage::where('vehicle_id', $image->vehicle_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return response()->json(['success' => true]);
    }

    private function parseBookedDates($raw)
    {
        if ($raw === null || $raw === '') {
            return null;
        }

        if (is_array($raw)) {
            $data = $raw;
        } else {
            $data = json_decode((string) $raw, true);
        }

        if (!is_array($data)) {
            throw ValidationException::withMessages([
                'booked_dates' => 'Invalid booked dates format.',
            ]);
        }

        $today = Carbon::today();
        $normalized = [];

        foreach ($data as $item) {
            if (is_string($item)) {
                try {
                    $date = Carbon::createFromFormat('Y-m-d', $item);
                } catch (\Throwable $e) {
                    throw ValidationException::withMessages(['booked_dates' => 'Invalid date format.']);
                }
                if ($date->format('Y-m-d') !== $item) {
                    throw ValidationException::withMessages(['booked_dates' => 'Invalid date format.']);
                }
                if ($date->lt($today)) {
                    throw ValidationException::withMessages(['booked_dates' => 'Dates cannot be in the past.']);
                }
                $normalized[] = $item;
                continue;
            }

            if (is_array($item) && array_key_exists('start', $item) && array_key_exists('end', $item)) {
                $startStr = (string) $item['start'];
                $endStr = (string) $item['end'];
                try {
                    $start = Carbon::createFromFormat('Y-m-d', $startStr);
                    $end = Carbon::createFromFormat('Y-m-d', $endStr);
                } catch (\Throwable $e) {
                    throw ValidationException::withMessages(['booked_dates' => 'Invalid date range format.']);
                }

                if ($start->format('Y-m-d') !== $startStr || $end->format('Y-m-d') !== $endStr) {
                    throw ValidationException::withMessages(['booked_dates' => 'Invalid date range format.']);
                }
                if ($start->lt($today) || $end->lt($today)) {
                    throw ValidationException::withMessages(['booked_dates' => 'Dates cannot be in the past.']);
                }
                if ($end->lt($start)) {
                    throw ValidationException::withMessages(['booked_dates' => 'End date must be after or equal to start date.']);
                }
                $normalized[] = ['start' => $startStr, 'end' => $endStr];
                continue;
            }

            throw ValidationException::withMessages([
                'booked_dates' => 'Invalid booked dates format.',
            ]);
        }

        return count($normalized) > 0 ? $normalized : null;
    }

    private function parseDeleteImageIds($raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        $data = json_decode((string) $raw, true);
        if (!is_array($data)) {
            throw ValidationException::withMessages([
                'delete_image_ids' => 'Invalid delete_image_ids format.',
            ]);
        }

        $ids = [];
        foreach ($data as $id) {
            if (is_numeric($id)) {
                $ids[] = (int) $id;
            }
        }

        return array_values(array_unique(array_filter($ids, fn ($id) => $id > 0)));
    }
}
