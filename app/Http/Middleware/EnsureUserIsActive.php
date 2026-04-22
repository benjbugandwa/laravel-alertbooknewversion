<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->is_active === false) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Votre compte est désactivé. Contactez un administrateur.');
        }

        return $next($request);
    }
}
