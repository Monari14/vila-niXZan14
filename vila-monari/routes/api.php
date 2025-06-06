<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;

Route::prefix('/v1')->group(function () {
    # Rota de login
    Route::post('/login', LoginController::class);
    # Rota de registro
    Route::post('/register', [UserController::class, 'store']);
    # ROta de logout
    Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

    # Rotas do usuário
    Route::get('/user', [UserController::class, 'show'])->middleware('auth:sanctum');
    Route::put('/user{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/user{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

    # Rotas de posts --api
    Route::apiResource('/posts', PostController::class);

    # Rotas de sessões do usuário
    Route::get('/user/sessions', [SessionController::class, 'list'])->middleware('auth:sanctum');
    Route::delete('/user/sessions/{$id}', [SessionController::class, 'destroy'])->middleware('auth:sanctum');
});
