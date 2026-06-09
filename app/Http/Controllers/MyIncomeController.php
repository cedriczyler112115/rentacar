<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyIncomeController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        $vehicleId = $request->input('vehicle_id');

        $query = Rental::whereHas('vehicle', function ($q) {
            $q->where('user_id', Auth::id());
        })->whereIn('status', ['Completed', 'Owner Booking']);

        if ($year) {
            $query->whereYear('datetime_from', $year);
        }
        if ($month) {
            $query->whereMonth('datetime_from', $month);
        }
        if ($vehicleId) {
            $query->where('vehicle_id', $vehicleId);
        }

        $totalEstimated = (clone $query)->sum('estimated_price');
        $totalActual = (clone $query)->sum(DB::raw('COALESCE(actual_price, estimated_price)'));

        $reports = $query->with(['vehicle', 'user'])
                         ->orderByDesc('datetime_from')
                         ->paginate(10)
                         ->withQueryString();

        $ownedVehicles = Vehicle::where('user_id', Auth::id())->orderBy('name')->get();

        return view('my-income.index', compact('reports', 'year', 'month', 'vehicleId', 'ownedVehicles', 'totalEstimated', 'totalActual'));
    }
}
