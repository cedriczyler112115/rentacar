<?php

namespace App\Http\Controllers;

use App\Models\LibMunicipality;
use App\Models\LibType;
use App\Services\MunicipalityTypePricingService;
use Illuminate\Http\Request;

class LibMunicipalityController extends Controller
{
    public function index(Request $request)
    {
        $query = LibMunicipality::query();

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('municipality', 'like', '%' . $q . '%')
                    ->orWhere('province', 'like', '%' . $q . '%')
                    ->orWhere('region', 'like', '%' . $q . '%');
            });
        }

        $municipalities = $query
            ->orderBy('region')
            ->orderBy('province')
            ->orderBy('municipality')
            ->paginate(20)
            ->withQueryString();

        $regions = LibMunicipality::query()
            ->select('region')
            ->distinct()
            ->orderBy('region')
            ->pluck('region');

        $regionProvinces = LibMunicipality::query()
            ->select('region', 'province')
            ->distinct()
            ->orderBy('region')
            ->orderBy('province')
            ->get()
            ->groupBy('region')
            ->map(fn ($rows) => $rows->pluck('province')->values());

        if ($request->filled('region')) {
            $provinces = $regionProvinces->get($request->region, collect());
        } else {
            $provinces = LibMunicipality::query()
                ->select('province')
                ->distinct()
                ->orderBy('province')
                ->pluck('province');
        }

        return view('municipalities.index', compact('municipalities', 'regions', 'provinces', 'regionProvinces'));
    }

    public function create()
    {
        $regionProvinces = LibMunicipality::query()
            ->select('region', 'province')
            ->distinct()
            ->orderBy('region')
            ->orderBy('province')
            ->get()
            ->groupBy('region')
            ->map(fn ($rows) => $rows->pluck('province')->values());

        $regions = $regionProvinces->keys()->values();

        return view('municipalities.create', compact('regions', 'regionProvinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region' => ['required', 'string', 'max:255'],
            'province' => ['required', 'string', 'max:255'],
            'municipality' => ['required', 'string', 'max:255'],
        ]);

        $isValidRegion = LibMunicipality::where('region', $validated['region'])->exists();
        $isValidProvince = LibMunicipality::where('region', $validated['region'])
            ->where('province', $validated['province'])
            ->exists();

        if (!$isValidRegion || !$isValidProvince) {
            return back()
                ->withErrors(['province' => 'Please select an existing region and province.'])
                ->withInput();
        }

        $exists = LibMunicipality::where('region', $validated['region'])
            ->where('province', $validated['province'])
            ->where('municipality', $validated['municipality'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['municipality' => 'This municipality already exists for the selected region and province.'])
                ->withInput();
        }

        LibMunicipality::create($validated);

        return redirect()->route('municipalities.index')->with('success', 'Price per location created successfully.');
    }

    public function edit(LibMunicipality $libMunicipality)
    {
        $types = LibType::query()->orderBy('name')->get();
        $typePrices = $libMunicipality->types()->pluck('lib_municipality_type_prices.price', 'lib_types.id');

        return view('municipalities.edit', [
            'municipality' => $libMunicipality,
            'types' => $types,
            'typePrices' => $typePrices,
        ]);
    }

    public function update(Request $request, LibMunicipality $libMunicipality)
    {
        $validated = $request->validate([
            'type_prices' => ['nullable', 'array'],
            'type_prices.*' => ['nullable', 'numeric', 'min:0.01'],
        ]);

        $service = new MunicipalityTypePricingService();
        $typeIds = LibType::query()->pluck('id')->all();
        $typePrices = $validated['type_prices'] ?? [];

        foreach ($typeIds as $typeId) {
            if (!array_key_exists((string) $typeId, $typePrices) && !array_key_exists($typeId, $typePrices)) {
                continue;
            }
            $value = $typePrices[$typeId] ?? $typePrices[(string) $typeId] ?? null;
            if ($value === null || $value === '') {
                $service->deletePriceForType($libMunicipality->id, (int) $typeId);
                continue;
            }
            $service->setPriceForType($libMunicipality->id, (int) $typeId, $value);
        }

        return redirect()->route('municipalities.index')->with('success', 'Price updated successfully.');
    }

    public function destroy(LibMunicipality $libMunicipality)
    {
        $libMunicipality->delete();

        return redirect()->route('municipalities.index')->with('success', 'Price per location deleted successfully.');
    }
}
