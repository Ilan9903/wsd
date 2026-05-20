<?php

namespace Hopla\AuthManagement\Handlers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

class TwoFactorHandler
{
    public function loginWith2FA(User $user): JsonResponse
    {
        $tempToken = Crypt::encryptString($user->id);
        Cache::put("2fa_temp_token_{$user->id}", $tempToken, now()->addMinutes(10));

        return response()->json([
            'two_factor' => true,
            'user_id' => $user->id,
            'temp_token' => $tempToken,
        ]);
    }

    public function askEnable2FA(User $user): JsonResponse
    {
        $tempToken = Crypt::encryptString($user->id);
        Cache::put("2fa_temp_token_{$user->id}", $tempToken, now()->addMinutes(10));

        $user->sendTwoFactorActivationNotification($tempToken);

        return response()->json([
            'message' => __('auth.user-2fa-ask-enable-email'),
        ], 401);
    }
}
