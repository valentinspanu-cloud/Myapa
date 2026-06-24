<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetLocationsAll
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!isAdmin()) {
            try {
                ApiController::getLocationsAll();
            } catch (\Exception $e) {
                \Log::error('GetLocationsAll middleware error: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
