<?php

namespace Hopla\AuthManagement\Handlers;

use App\Models\User;
use Crypt;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class PasswordRenewalHandler
{
    public function renewPassword(User $user): JsonResponse
    {
        $tempToken = Crypt::encryptString($user->id);
        Cache::put("renew_password_temp_token_{$user->id}", $tempToken, now()->addMinutes(10));

        return response()->json([
            'renew_password' => true,
            'user_id' => $user->id,
            'temp_token' => $tempToken,
        ]);
    }
}
