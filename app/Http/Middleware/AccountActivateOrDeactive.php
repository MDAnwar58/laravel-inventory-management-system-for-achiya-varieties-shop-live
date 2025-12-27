<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountActivateOrDeactive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if (
            $user->role == 'admin' && $user->is_active == false
            || $user->role == 'staff' && $user->is_active == false
            || $user->role == 'staff' && $user->is_active == false
            || $user->role == 'super_admin' && $user->is_active == false
            || $user->role == 'manager' && $user->is_active == false
        ) {
            return redirect()->back()->with([
                'status' => 'warning',
                'msg' => 'Your account is deactived.'
            ]);
        }
        return $next($request);
    }
}
