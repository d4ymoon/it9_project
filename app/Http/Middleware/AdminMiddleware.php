<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            return redirect('/login')->with('error', 'Unauthorized access. Please login as admin.');
        }

        return $next($request);
    }
} 