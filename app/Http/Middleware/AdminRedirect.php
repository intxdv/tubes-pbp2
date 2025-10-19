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
        // Jika mengakses rute admin
        if ($request->is('admin/*')) {
            // Jika belum login, arahkan ke halaman login admin
            if (!Auth::check()) {
                return redirect()->route('admin.login');
            }
            
            // Jika sudah login tapi bukan admin, arahkan ke home
            if (Auth::user()->role !== 'admin') {
                return redirect('/');
            }
        }
        // Jika bukan rute admin
        else {
            // Jika user adalah admin, arahkan ke dashboard admin
            if (Auth::check() && Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}