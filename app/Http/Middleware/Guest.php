<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Guest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd(Auth::check());
        if (Auth::check()) {
            if (auth()->user()->role === "owner" || in_array(auth()->user()->role, ['admin', 'super_admin', 'manager', 'staff']) && auth()->user()->is_active)
                return redirect()->route('admin.dashboard');
            else
                return redirect()->route('welcome');
        } else
            return $next($request);
    }
}
