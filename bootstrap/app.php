<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // -------------------------------------------------------
        // Middleware global (rulează la FIECARE request)
        // CheckForMaintenanceMode → în L11 se numește PreventRequestsDuringMaintenance
        // TrimStrings, ConvertEmptyStringsToNull → incluse automat în L11
        // TrustProxies → înlocuit cu TrustProxies actualizat (fără fideloper)
        // -------------------------------------------------------
        $middleware->append(\App\Http\Middleware\TrustProxies::class);

        // -------------------------------------------------------
        // Alias-uri pentru route middleware
        // (înlocuiesc $routeMiddleware din Kernel.php)
        // -------------------------------------------------------
        $middleware->alias([
            // Custom ale proiectului
            'get_locations'    => \App\Http\Middleware\GetLocations::class,
            'get_locationsAll' => \App\Http\Middleware\GetLocationsAll::class,
            'revalidate'       => \App\Http\Middleware\RevalidateBackHistory::class,

            // Spatie Permission — v6 a redenumit middleware-urile
            'role'             => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'       => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // -------------------------------------------------------
        // Grupul 'web' — L11 îl are deja configurat implicit cu:
        // EncryptCookies, AddQueuedCookiesToResponse, StartSession,
        // ShareErrorsFromSession, VerifyCsrfToken, SubstituteBindings
        // Nu mai trebuie adăugate manual.
        // -------------------------------------------------------

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acces interzis.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });
    })->create();
