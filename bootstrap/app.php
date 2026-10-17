<?php

use App\Http\Middleware\OnderhoudsModus;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Onderhoudsmodus: bezoekers zien een 404-pagina zolang de modus aan staat
        $middleware->web(append: [
            OnderhoudsModus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Is de database niet bereikbaar (bijvoorbeeld omdat MySQL of WampServer uit staat)?
        // Toon dan een nette melding in plaats van een technische foutpagina.
        $exceptions->render(function (QueryException $e, Request $request) {
            $melding = $e->getMessage();

            $verbindingsFout = str_contains($melding, '[2002]')   // verbinding geweigerd
                || str_contains($melding, '[2006]')               // databaseserver is weggevallen
                || str_contains($melding, '[2013]');              // verbinding verbroken

            if (! $verbindingsFout || $request->expectsJson()) {
                return null; // andere databasefouten behandelt Laravel zoals normaal
            }

            return response()->view('errors.503', [], 503);
        });
    })->create();