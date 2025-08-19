<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role->name ?? null;

        if (!in_array($userRole, $roles, true)) {
            abort(403); // o redirect()->route('login');
        }

        return $next($request);
    }
}
