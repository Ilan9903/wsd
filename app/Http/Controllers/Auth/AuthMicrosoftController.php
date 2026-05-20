<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserSocialiteService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Microsoft\Provider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Tymon\JWTAuth\JWTGuard;

/** * @codeCoverageIgnore */
class AuthMicrosoftController extends Controller
{
    /**
     * @return RedirectResponse|\Illuminate\Http\RedirectResponse
     */
    public function redirectToMicrosoft(): RedirectResponse|\Illuminate\Http\RedirectResponse
    {

        $redirectUri = 'https://'.tenant()->domains->first()->domain.'/api/azure/auth/callback';

        return Socialite::buildProvider(Provider::class, [
            'client_id' => config('services.azure.client_id'),
            'client_secret' => config('services.azure.client_secret'),
            'redirect' => $redirectUri,
        ])->stateless()->redirect();
    }

    /**
     * @param  UserSocialiteService  $userService
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     *
     * @throws ConnectionException
     */
    public function handleMicrosoftCallback(UserSocialiteService $userService): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $redirectUri = 'https://'.tenant()->domains->first()->domain.'/api/azure/auth/callback';

        /** @var \Laravel\Socialite\Two\User $microsoftUser */
        $microsoftUser = Socialite::buildProvider(Provider::class, [
            'client_id' => config('services.azure.client_id'),
            'client_secret' => config('services.azure.client_secret'),
            'redirect' => $redirectUri,
        ])->stateless()->user();

        $user = User::where('email', '=', $microsoftUser->getEmail())->first();

        if (! $user) {
            return response()->json([
                'message' => __('auth.not-found'),
            ]);
        }

        $avatarPath = $userService->fetchMicrosoftUserDetails($microsoftUser->token, (string) $user->id);
        $userService->updateUserFieldsIfNotNull($user, [
            'lastname' => $microsoftUser->user['surname'] ?? null,
            'firstname' => $microsoftUser->user['givenName'] ?? null,
            'city' => $microsoftUser->user['officeLocation'] ?? null,
            'phone' => $microsoftUser->user['businessPhones'][0] ?? null,
            'image' => $avatarPath ?? null,
        ]);

        /** @var JWTGuard $guard */
        $guard = auth('api');

        $token = $guard->login($user);

        $frontUrl = config('wesend.domain');

        return redirect()->away('https://'.tenant()->id.$frontUrl.'/auth?token='.$token);

    }
}
