<?php

namespace App\Http\Controllers;

use App\Models\LibAvailabilityStatus;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingRedirectController extends Controller
{
    public function __invoke(Request $request, Vehicle $vehicle)
    {
        $allowedStatusIds = LibAvailabilityStatus::whereIn(DB::raw('LOWER(name)'), ['available', 'pending'])->pluck('id')->all();
        if (count($allowedStatusIds) > 0 && !in_array((int) $vehicle->lib_availability_status_id, $allowedStatusIds, true)) {
            Log::warning('booking_redirect_vehicle_not_bookable', [
                'vehicle_id' => $vehicle->id,
                'status_id' => $vehicle->lib_availability_status_id,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('home')->withErrors(['booking' => 'This vehicle is not available for booking right now.']);
        }

        $enc = encrypt($vehicle->id);
        $target = route('rentals.create', ['enc_id' => $enc], absolute: false);
        $query = $request->query();
        if (is_array($query) && count($query) > 0) {
            $target .= '?' . http_build_query($query);
        }

        $request->session()->put('url.intended', $target);
        $request->session()->put('booking.intended', $target);

        Log::info('booking_redirect_initiated', [
            'vehicle_id' => $vehicle->id,
            'user_id' => Auth::id(),
            'intended' => $target,
        ]);

        if (!Auth::check()) {
            return redirect()->route('login', ['redirect' => $target]);
        }

        return redirect()->to($target);
    }
}

