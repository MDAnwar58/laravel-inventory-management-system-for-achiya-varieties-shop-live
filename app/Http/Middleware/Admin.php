<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (
                Auth::user()->role == 'admin'
                || Auth::user()->role == 'staff' && Auth::user()->is_active == true
                || Auth::user()->role == 'super_admin' && Auth::user()->is_active == true
                || Auth::user()->role == 'manager' && Auth::user()->is_active == true
                || Auth::user()->role == 'owner'
            )
                return $next($request);
            else
                return redirect()->route('sign.in');
        } else
            return redirect()->route('sign.in');
    }
}
