<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeApiController extends Controller
{
    /**
     * Get authenticated user info.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_admin' => $user->isAdmin(),
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'profile' => $user->profile ? [
                    'avatar' => $user->profile->avatar_url,
                    'phone' => $user->profile->phone,
                    'bio' => $user->profile->bio,
                ] : null,
            ],
        ]);
    }
}
