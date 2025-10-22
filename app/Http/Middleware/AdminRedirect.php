<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRedirect
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Jika user adalah admin dan mengakses halaman non-admin, redirect ke dashboard admin
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Allow admin routes
            if ($request->is('admin/*') || $request->is('admin')) {
                return $next($request);
            }
            // Redirect admin to their dashboard
            return redirect()->route('admin.dashboard');
        }

        // Jika mengakses rute admin tanpa login atau bukan admin
        if ($request->is('admin/*')) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            if (Auth::user()->role !== 'admin') {
                return redirect('/');
            }
        }

        return $next($request);
    }
}