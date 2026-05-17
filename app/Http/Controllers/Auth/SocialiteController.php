<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'github'];

    /**
     * Redirect to the provider's authentication page.
     */
    public function redirectToProvider($provider)
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     */
    public function handleProviderCallback($provider)
    {
        abort_unless(in_array($provider, self::ALLOWED_PROVIDERS, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Authentication failed.');
        }

        if (! $socialUser->getEmail()) {
            return redirect()->route('login')->with('error', 'Your '.$provider.' account did not return an email address.');
        }

        $user = User::firstOrNew(['email' => $socialUser->getEmail()]);
        $user->fill([
            'name' => $user->exists ? $user->name : ($socialUser->getName() ?: $socialUser->getNickname() ?: $socialUser->getEmail()),
            'social_id' => $socialUser->getId(),
            'social_type' => $provider,
            'status' => $user->status ?: 'active',
            'email_verified_at' => $user->email_verified_at ?: now(),
        ]);

        if (! $user->exists) {
            $user->role = 'guest';
            $user->password = null;
        }

        $user->save();

        Auth::login($user);

        return redirect()->intended($user->redirectPath());
    }
}
