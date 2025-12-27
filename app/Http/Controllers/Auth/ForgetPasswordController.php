<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ForgetPasswordController extends Controller
{
    public function index()
    {
        return view('pages.auth.forget-password');
    }
    public function send_mail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()
                ->with([
                    'status' => 'warning',
                    'msg' => 'User not found!',
                ]);
        }

        // Generate a random token
        $token = Str::random(60);

        // Store the token in the password_resets table
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => bcrypt($token),
                'created_at' => now()
            ]
        );

        // Generate the reset URL
        $resetUrl = URL::temporarySignedRoute(
            'password.reset',
            now()->addMinutes(60), // Link expires in 60 minutes
            [
                'token' => $token,
                'email' => $user->email
            ]
        );

        try {
            // Send the password reset email
            Mail::to($user->email)->send(new ResetPasswordMail($resetUrl, $user));

            return redirect()->back()
                ->with([
                    'status' => 'success',
                    'msg' => 'Email sent! Please check your inbox.',
                    'user_inbox_email' => $user->email,
                ])
                ->withCookie(cookie(
                    'email_sent_to_forget_password',
                    $user->email,
                    60, // 60 minutes
                    null,
                    null,
                    false,
                    false,
                    false,
                    'Lax'
                ));

        } catch (\Exception $e) {
            return redirect()->back()
                ->with([
                    'status' => 'error',
                    'msg' => 'Something went wrong! Please try again later!',
                ]);
        }
    }

}
