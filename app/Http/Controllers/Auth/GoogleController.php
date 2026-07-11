<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'password' => null,
                'email_verified_at' => now(), // Google users are pre-verified
            ]);

            Auth::login($user);

            $defaultRoute = $user->is_aaracc ? 'bookings.manage' : 'dashboard';

            return redirect()->intended(route($defaultRoute, absolute: false));
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Failed to securely authenticate with Google. Please try again.']);
        }
    }
}
