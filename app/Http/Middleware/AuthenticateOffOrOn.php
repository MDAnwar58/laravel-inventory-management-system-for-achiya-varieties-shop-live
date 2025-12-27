<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateOffOrOn
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $setting = Setting::first();
        if ($setting->is_auth_system)
            return $next($request);
        return redirect()->route('welcome')->with([
            'status' => 'warning',
            'msg' => 'Authentication System is Off! Please contact the site owner.',
        ]);
    }
}
