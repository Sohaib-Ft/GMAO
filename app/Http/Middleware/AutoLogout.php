<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AutoLogout
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $timeout = 15 * 60; // 15 minutes

            if (session()->has('last_activity')) {
                if (time() - session('last_activity') > $timeout) {
                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();

                    return redirect()->route('login')
                        ->withErrors(['email' => 'Session expirée par inactivité.']);
                }
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
