<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $routeName = optional($request->route())->getName();
        if ($routeName && (
            str_starts_with($routeName, 'profile.') ||
            $routeName === 'logout' ||
            $routeName === 'verification.send' ||
            $routeName === 'verification.notice'
        )) {
            return $next($request);
        }

        $missingContact = !is_string($user->contact_number) || trim($user->contact_number) === '';
        $missingAddress = !is_string($user->address) || trim($user->address) === '';

        if ($missingContact || $missingAddress) {
            $request->session()->put('profile.intended', $request->fullUrl());
            if (!$request->session()->has('url.intended')) {
                $request->session()->put('url.intended', $request->fullUrl());
            }
            Log::info('profile_incomplete_redirect', [
                'user_id' => $user->id,
                'route' => $routeName,
                'intended' => $request->session()->get('url.intended'),
            ]);

            return redirect()->route('profile.edit')->with('status', 'Please complete your profile details to continue.');
        }

        return $next($request);
    }
}
