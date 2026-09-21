<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Register in bootstrap/app.php (Laravel 11+):
     *
     *   $middleware->alias(['role' => \App\Http\Middleware\EnsureUserHasRole::class]);
     *
     * Or in the old $routeMiddleware array (Laravel 10 and earlier).
     * Usage: Route::middleware('role:seller')->group(...)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! $request->user() || $request->user()->role !== $role) {
            abort(403);
        }

        return $next($request);
    }
}