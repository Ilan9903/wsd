<?php

namespace Hopla\AuthManagement\Handlers;

use App\Models\TenantContract;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserLoginHandler
{
    public function login(User $user): JsonResponse
    {
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => __('auth.login-success'),
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'image' => $user->image,
            'token' => $token,
            'city' => $user->city,
            'country' => $user->country,
            'phone' => $user->phone,
            'viewAdminPanel' => $user->can('view_admin_panel'),
            'viewThemePanel' => $user->can('view_theme_panel'),
            'is_health' => TenantContract::first()->is_health,
        ]);
    }
}
