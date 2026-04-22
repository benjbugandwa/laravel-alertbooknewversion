<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->query('lang');

        if (in_array($lang, ['fr', 'en'])) {
            session(['app_lang' => $lang]);
        }

        $locale = session('app_lang', config('app.locale', 'fr'));

        App::setLocale($locale);

        return $next($request);
    }
}
