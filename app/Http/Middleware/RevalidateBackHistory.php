<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RevalidateBackHistory
{
    /**
     * L11: adăugat type hints Request și Response
     * L11: \Request::route() → $request->route()
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route()->getName() === 'invoice.pdf') {
            return $next($request);
        }

        $response = $next($request);

        return $response->header('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }
}
