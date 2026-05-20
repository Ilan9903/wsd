<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserSocialiteService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tymon\JWTAuth\JWTGuard;

/** * @codeCoverageIgnore */
class AuthGoogleController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function redirectToGoogle(): RedirectResponse
    {
        $redirectUri = 'https://'.tenant()->domains->first()->domain.'/api/auth/google/callback';

        return Socialite::buildProvider(GoogleProvider::class, [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect' => $redirectUri,
        ])->stateless()->redirect();
    }

    /**
     * @return JsonResponse|RedirectResponse
     *
     * @throws GuzzleException
     */
    public function handleGoogleCallback(): JsonResponse|RedirectResponse
    {

        $redirectUri = 'https://'.tenant()->domains->first()->domain.'/api/auth/google/callback';
        $service = new UserSocialiteService;

        /** @var \Laravel\Socialite\Two\User $googleUser */
        $googleUser = Socialite::buildProvider(GoogleProvider::class, [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect' => $redirectUri,
        ])->stateless()->user();

        $details = $service->fetchGoogleUserDetails($googleUser->token);

        $user = User::where('email', '=', $googleUser->getEmail())->first();

        $service->updateUserFieldsIfNotNull($user, [
            'lastname' => $googleUser->user['family_name'] ?? null,
            'firstname' => $googleUser->user['given_name'] ?? null,
            'city' => $details['city'] ?? null,
            'phone' => $details['phone'] ?? null,
        ]);

        if (! $user) {
            return response()->json([
                'message' => __('auth.not-found'),
            ], 403);
        }

        /** @var JWTGuard $guard */
        $guard = auth('api');

        $token = $guard->login($user);

        $frontUrl = config('wesend.domain');

        return redirect()->away('https://'.tenant()->id.$frontUrl.'/auth?token='.$token);

    }
}
