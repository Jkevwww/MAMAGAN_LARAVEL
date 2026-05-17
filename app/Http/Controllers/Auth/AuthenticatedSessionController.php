<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (! $user) {
            $email = strtolower($request->input('email'));
            $user = User::create([
                'name' => str($email)->before('@')->replace('.', ' ')->title(),
                'email' => $email,
                'password' => Hash::make($request->input('password')),
                'role' => 'guest',
                'status' => 'inactive',
            ]);

            VerificationCodeController::send($user);

            return redirect()->route('verification.code.show', ['email' => $user->email])
                ->with('status', 'We created a guest account and sent a verification code to your email.');
        }

        if (! $user->email_verified_at) {
            VerificationCodeController::send($user);

            return redirect()->route('verification.code.show', ['email' => $user->email])
                ->with('status', 'Please verify your email before logging in.');
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['email' => 'This account is inactive.']);
        }

        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(Auth::user()->redirectPath());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
