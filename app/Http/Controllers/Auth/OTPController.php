<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OTPController extends Controller
{
    public function show(Request $request)
    {
        if (! $request->session()->has('auth.registration_user_id')) {
            return redirect()->route('login');
        }
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $userId = $request->session()->get('auth.registration_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        
        if (!$user || $user->otp_code !== $request->otp || now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'The provided OTP is invalid or has expired.']);
        }

        // Clear OTP and finalize login
        $user->update(['otp_code' => null, 'otp_expires_at' => null]);
        
        Auth::login($user);
        $request->session()->forget('auth.registration_user_id');

        return redirect()->route($user->is_aaracc ? 'bookings.manage' : 'dashboard');
    }
}
