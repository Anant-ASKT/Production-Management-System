<?php

namespace App\Http\Controllers\Sampling\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('sampling')->check()) {
            return redirect()->route('sampling.dashboard');
        }
        return view('sampling.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['status'] = 'active';

        if (Auth::guard('sampling')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('sampling.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid email/password or account is inactive.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('sampling')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('sampling.login');
    }
}
