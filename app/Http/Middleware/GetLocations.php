<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ApiController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetLocations
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!isAdmin()) {
            try {
                ApiController::getLocations();
            } catch (\Exception $e) {
                \Log::error('GetLocations middleware error: ' . $e->getMessage());
            }
        }

        return $next($request);
    }
}
