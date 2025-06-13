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
    Route::put('/user/{id}', [UserController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->middleware('auth:sanctum');

    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store'])->middleware('auth:sanctum');
    Route::get('/posts/{id}', [PostController::class, 'show'])->middleware('auth:sanctum');
    Route::put('/posts/{id}', [PostController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->middleware('auth:sanctum');


    # Rotas de sessões do usuário
    Route::get('/user/sessions', [SessionController::class, 'list'])->middleware('auth:sanctum');
    Route::delete('/user/sessions/{$id}', [SessionController::class, 'destroy'])->middleware('auth:sanctum');
});
