<?php

namespace App\Http\Middleware;

use App\Support\Onderhoud;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OnderhoudsModus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Onderhoud::aan()) {
            return $next($request);
        }

        // Ingelogde beheerders zien de website gewoon
        if ($request->user()?->is_admin) {
            return $next($request);
        }

        // Inloggen en uitloggen blijven bereikbaar, zodat een beheerder de website weer open kan zetten
        if ($request->is('login', 'uitloggen', 'webhooks/*')) {
            return $next($request);
        }

        abort(404);
    }
}