<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HandleLoginRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Mail\AdminSendResetLinkMail;
use App\Models\Admin;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AdminAuthenticationController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }

    public function handleLogin(HandleLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function forgotPassword() : View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendResetLink(Request $request) : RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'exists:admins,email'],
        ]);

        $token = \Str::random(64);

        $admin = Admin::where('email', $request->email)->first();
        $admin->remember_token = $token;
        $admin->save();

        Mail::to($request->email)->send(new AdminSendResetLinkMail($token, $request->email));

        return redirect()->back()->with('success', 'A mail has been sent to your email address please check!');
    }

    public function resetPassword($token) : View
    {
        return view('admin.auth.reset-password', compact('token'));
    }

    public function handleResetPassword(ResetPasswordRequest $request) : RedirectResponse
    {
        $admin = Admin::where(['email'=> $request->email, 'remember_token'=> $request->token])->first();

        if(!$admin) {
            return back()->with('error', 'Token is invalid');
        }

        $admin->password = bcrypt($request->password);
        $admin->remember_token = null;
        $admin->save();

        return redirect()->back()->route('admin.login')->with('success', 'Password reset successfully!');
    }
}
