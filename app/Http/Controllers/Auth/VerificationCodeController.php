<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VerificationCodeController extends Controller
{
    public function show(Request $request): View
    {
        return view('auth.verify-code', ['email' => $request->query('email')]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();
        $code = EmailVerificationCode::where('user_id', $user->id)
            ->where('code', $data['code'])
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $code || $code->expires_at->isPast()) {
            return back()->withInput()->withErrors(['code' => 'The verification code is invalid or expired.']);
        }

        $code->update(['verified_at' => now()]);
        $user->update([
            'email_verified_at' => now(),
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->redirectPath())->with('success', 'Email verified.');
    }

    public static function send(User $user): string
    {
        $plainCode = (string) random_int(100000, 999999);

        EmailVerificationCode::create([
            'user_id' => $user->id,
            'code' => $plainCode,
            'expires_at' => now()->addMinutes(20),
        ]);

        try {
            Mail::raw("Your Mamagan Resort verification code is {$plainCode}. It expires in 20 minutes.", function ($message) use ($user) {
                $message->to($user->email)->subject('Mamagan Resort verification code');
            });
        } catch (\Throwable $exception) {
            report($exception);
        }

        return $plainCode;
    }
}
