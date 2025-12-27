<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use Hash;
use Illuminate\Support\Facades\Auth;

class SignInController extends Controller
{
    public function index()
    {
        $alert = session()->pull('alert', []);
        return view('pages.auth.sign-in', compact('alert'));
    }
    public function req_sign_in(SignInRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();
        if (!Hash::check($validated['password'], $user->password))
            return back()->with([
                'error' => 'Incorrect Password!',
            ]);


        if ($user) {
            // Log in user directly
            $remember = false;
            $hasRemember = $request->has('remember');
            if ($hasRemember)
                $remember = (bool) $request->get('remember');
            $cradentials = [
                'email' => $validated['email'],
                'password' => $validated['password'],
            ];
            Auth::attempt($cradentials, $remember);


            // Adjust session lifetime
            if ($hasRemember) {
                config(['session.lifetime' => 20160]); // 2 weeks
            }

            if (in_array($user->role, ['owner', 'admin', 'super_admin', 'manager', 'staff']))
                return redirect()->route('admin.dashboard');
            else
                return redirect("/");
        }

        // Optional: handle case when user not found
        return redirect()->route('sign.in')
            ->with([
                'status' => 'error',
                'msg' => 'User not found!',
            ]);
    }

    public function sign_out()
    {
        Auth::logout();
        return redirect()->route('sign.in');
    }

}
