<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffAndUserManagementAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!in_array(auth()->user()->role, ['owner', 'admin', 'super_admin']))
            return redirect()->route('admin.dashboard')->with([
                'status' => 'warning',
                'msg' => 'You are not authorized to access this page!'
            ]);

        return $next($request);
    }
}
