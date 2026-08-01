<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => $request->password,
            'role'     => 'guest',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->createdResponse(
            (new UserResource($user))->additional(['token' => $token]),
            __('api.auth_register_success')
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return $this->unauthorizedResponse(__('api.auth_invalid_credentials'));
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->trashed()) {
            return $this->unauthorizedResponse(__('api.auth_account_deactivated'));
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return $this->successResponse(
            (new UserResource($user))->additional(['token' => $token]),
            __('api.auth_login_success')
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse([], __('api.auth_logout_success'));
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            __('api.auth_profile_retrieved')
        );
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'  => ['sometimes', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->update($validated);

        return $this->successResponse(
            new UserResource($request->user()->fresh()),
            __('api.auth_profile_updated')
        );
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->delete();

        $user->tokens()->delete();

        return $this->successResponse([], __('api.auth_account_deleted'), 200);
    }
}
