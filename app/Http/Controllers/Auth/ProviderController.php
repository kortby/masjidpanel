<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Authentication failed. Please try again.']);
        }

        $user = User::where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        $isFirstTime = false;

        if (! $user) {
            // Check if user exists with the same email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // Link account
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar() ?? $user->avatar,
                ]);
            } else {
                // Create new user
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'is_verified' => false,
                ]);
                $isFirstTime = true;
            }
        } else {
            // Update avatar if changed
            if ($socialUser->getAvatar() && $user->avatar !== $socialUser->getAvatar()) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }
        }

        Auth::login($user, true);

        if ($isFirstTime) {
            return redirect()->route('profile.edit')->with('success', 'Welcome! Please complete your profile.');
        }

        return redirect()->intended('/');
    }
}
