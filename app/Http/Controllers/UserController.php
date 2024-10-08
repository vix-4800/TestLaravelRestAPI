<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $service
    ) {
        //
    }

    /**
     * Returns a collection of all users.
     */
    public function index()
    {
        return Cache::remember('users', now()->addMinutes(10), function () {
            return UserResource::collection(User::all());
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $this->service->clearCache();

        return new UserResource(
            User::create($request->validated())
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $user->update($request->validated());

        $this->service->clearCache();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): Response
    {
        $user->delete();

        $this->service->clearCache();

        return response()->noContent();
    }
}
