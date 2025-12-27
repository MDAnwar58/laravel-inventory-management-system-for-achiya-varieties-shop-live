<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->query('email');
        $token = $request->query('token');

        if (!$email || !$token) {
            return redirect()->route('forget.password')
                ->with('alert', [
                    'status' => 'error',
                    'message' => 'Invalid password reset link.',
                ]);
        }

        return view('pages.auth.reset-password', [
            'email' => $email,
            'token' => $token,
        ]);
    }

    public function reset_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check if token exists and is valid
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return redirect()->route('forget.password')
                ->with([
                    'status' => 'error',
                    'msg' => 'Invalid or expired password reset link. Please try again.',
                ]);
        }

        // Update user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the password reset record
        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('sign.in')
            ->with([
                'status' => 'success',
                'msg' => 'Your password has been reset successfully! You can now sign in with your new password.',
            ]);
    }
}
