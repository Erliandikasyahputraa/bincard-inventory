<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PastikanRoleAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasRole('Admin')) {
            abort(403, 'Akses hanya untuk Admin.');
        }

        return $next($request);
    }
}
