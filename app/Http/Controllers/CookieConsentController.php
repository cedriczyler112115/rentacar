<?php

namespace App\Http\Controllers;

use App\Models\CookieConsentEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CookieConsentController extends Controller
{
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'analytics' => ['nullable', 'boolean'],
                'marketing' => ['nullable', 'boolean'],
            ]);

            $prefs = [
                'essential' => true,
                'analytics' => (bool) ($validated['analytics'] ?? false),
                'marketing' => (bool) ($validated['marketing'] ?? false),
                'version' => 1,
                'accepted_at' => now()->toIso8601String(),
                'expires_at' => now()->addDays(365)->toIso8601String(),
            ];

            if (Schema::hasTable('cookie_consent_events')) {
                CookieConsentEvent::create([
                    'user_id' => $request->user()?->id,
                    'action' => 'accepted',
                    'preferences' => $prefs,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                ]);
            }

            Cookie::queue(cookie('aar_cookie_consent', json_encode($prefs), 60 * 24 * 365, null, null, $request->isSecure(), true, false, 'lax'));
            if ($request->user()) {
                Auth::login($request->user(), true);
            }

            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'preferences' => $prefs]);
            }

            return redirect()->back();
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to save cookie preferences.',
                ], 500);
            }
            return redirect()->back()->withErrors(['cookie_consent' => 'Failed to save cookie preferences.']);
        }
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $validated = $request->validate([
                'analytics' => ['nullable', 'boolean'],
                'marketing' => ['nullable', 'boolean'],
            ]);

            $prefs = $this->currentPreferences($request);
            $prefs['analytics'] = (bool) ($validated['analytics'] ?? false);
            $prefs['marketing'] = (bool) ($validated['marketing'] ?? false);
            $prefs['essential'] = true;
            $prefs['version'] = 1;
            $prefs['updated_at'] = now()->toIso8601String();
            $prefs['expires_at'] = now()->addDays(365)->toIso8601String();

            if (Schema::hasTable('cookie_consent_events')) {
                CookieConsentEvent::create([
                    'user_id' => $request->user()?->id,
                    'action' => 'updated',
                    'preferences' => $prefs,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                ]);
            }

            Cookie::queue(cookie('aar_cookie_consent', json_encode($prefs), 60 * 24 * 365, null, null, $request->isSecure(), true, false, 'lax'));
            if ($request->user()) {
                Auth::login($request->user(), true);
            }

            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'preferences' => $prefs]);
            }

            return redirect()->back();
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to update cookie preferences.',
                ], 500);
            }
            return redirect()->back()->withErrors(['cookie_consent' => 'Failed to update cookie preferences.']);
        }
    }

    public function decline(Request $request): JsonResponse|RedirectResponse
    {
        try {
            $prefs = [
                'essential' => true,
                'analytics' => false,
                'marketing' => false,
                'version' => 1,
                'accepted_at' => now()->toIso8601String(),
                'expires_at' => now()->addDays(365)->toIso8601String(),
            ];

            if (Schema::hasTable('cookie_consent_events')) {
                CookieConsentEvent::create([
                    'user_id' => $request->user()?->id,
                    'action' => 'declined',
                    'preferences' => $prefs,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                ]);
            }

            Cookie::queue(cookie('aar_cookie_consent', json_encode($prefs), 60 * 24 * 365, null, null, $request->isSecure(), true, false, 'lax'));
            if ($request->user()) {
                Auth::login($request->user(), true);
            }

            if ($request->expectsJson()) {
                return response()->json(['ok' => true, 'preferences' => $prefs]);
            }

            return redirect()->back();
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to save cookie preferences.',
                ], 500);
            }
            return redirect()->back()->withErrors(['cookie_consent' => 'Failed to save cookie preferences.']);
        }
    }

    public function forget(Request $request): JsonResponse|RedirectResponse
    {
        try {
            if (Schema::hasTable('cookie_consent_events')) {
                CookieConsentEvent::create([
                    'user_id' => $request->user()?->id,
                    'action' => 'revoked',
                    'preferences' => null,
                    'ip' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                ]);
            }

            Cookie::queue(Cookie::forget('aar_cookie_consent'));

            if ($request->expectsJson()) {
                return response()->json(['ok' => true]);
            }

            return redirect()->back();
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to revoke cookie preferences.',
                ], 500);
            }
            return redirect()->back()->withErrors(['cookie_consent' => 'Failed to revoke cookie preferences.']);
        }
    }

    private function currentPreferences(Request $request): array
    {
        $raw = $request->cookie('aar_cookie_consent');
        if (!is_string($raw) || $raw === '') return [];
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
