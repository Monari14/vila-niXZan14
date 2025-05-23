<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

Route::post('/register', [UserController::class, 'store']);

Route::get('user', [UserController::class, 'show'])->middleware('auth:sanctum');
Route::put('user', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::delete('user', [UserController::class, 'destroy'])->middleware('auth:sanctum');

Route::apiResource('/posts', PostController::class);
