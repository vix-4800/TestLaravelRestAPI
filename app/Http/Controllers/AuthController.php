<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request) {}

    public function register(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create($data);

        $apiToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => UserResource::make($user),
            'access_token' => $apiToken,
            'token_type' => 'Bearer',
        ], 201);
    }
}
