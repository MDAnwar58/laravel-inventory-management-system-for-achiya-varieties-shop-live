<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAccessAdminPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->role === "owner" || in_array(auth()->user()->role, ['admin', 'super_admin', 'manager', 'staff']) && auth()->user()->is_active) {
            return $next($request);
        } else
            return redirect()->route('welcome')->with([
                'status' => 'warning',
                'msg' => 'Your account is Deactived!'
            ]);
    }
}
