<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Check login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check admin role/type
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Access denied. Admin only.');
        }

        return $next($request);
    }
}
