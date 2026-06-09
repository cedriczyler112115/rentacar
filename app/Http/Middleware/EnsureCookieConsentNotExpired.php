<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class EnsureCookieConsentNotExpired
{
    public function handle(Request $request, Closure $next): Response
    {
        $raw = $request->cookie('aar_cookie_consent');
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['expires_at'])) {
                $ts = strtotime((string) $decoded['expires_at']);
                if ($ts && $ts < time()) {
                    Cookie::queue(Cookie::forget('aar_cookie_consent'));
                }
            }
        }

        return $next($request);
    }
}

