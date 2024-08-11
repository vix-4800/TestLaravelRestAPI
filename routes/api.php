<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class)
    ->except(['create', 'edit']);
// ->middleware('auth:sanctum');

