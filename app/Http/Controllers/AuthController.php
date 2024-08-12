<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Mail\ResetPassword;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Str;

class AuthController extends Controller
{
    /**
     * Login a user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        /** @var User $user */
        $user = User::where('email', $data['email'])->first();

        if (! Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        $user->tokens()->delete();

        return response()->json([
            'auth_token' => $user->createToken('auth_token')->plainTextToken,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $apiToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => UserResource::make($user),
            'auth_token' => $apiToken,
            'token_type' => 'Bearer',
        ], 201);
    }

    /**
     * Reset password.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated()['email'])->first();

        $newPassword = Str::random(8);
        $user->update([
            'password' => bcrypt($newPassword),
        ]);

        Mail::to($user->email)->send(new ResetPassword($user->email, $newPassword));

        return response()->json([
            'message' => 'Password reset successfully, check your email',
        ], 200);
    }
}
