<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $redirect = $request->query('redirect');
        if (is_string($redirect) && $redirect !== '') {
            $safe = $this->sanitizeRedirect($redirect);
            if ($safe) {
                $request->session()->put('url.intended', $safe);
                $request->session()->put('booking.intended', $safe);
                Log::info('login_redirect_intended_set', ['intended' => $safe]);
            } else {
                Log::warning('login_redirect_rejected', ['redirect' => $redirect]);
            }
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $defaultRoute = $user && $user->is_aaracc ? 'bookings.manage' : 'dashboard';

        return redirect()->intended(route($defaultRoute, absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function sanitizeRedirect(string $redirect): ?string
    {
        $redirect = trim($redirect);
        if ($redirect === '' || strlen($redirect) > 2048) {
            return null;
        }
        if (str_starts_with($redirect, '//')) {
            return null;
        }
        if (!str_starts_with($redirect, '/')) {
            return null;
        }
        if (preg_match('/[\r\n]/', $redirect)) {
            return null;
        }

        return $redirect;
    }
}
