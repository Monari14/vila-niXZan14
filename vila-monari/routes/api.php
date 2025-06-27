<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CommentController;

Route::prefix('/v1')->group(function () {
    # Rota de login
    Route::post('/login', LoginController::class);
    # Rota de registro
    Route::post('/register', [UserController::class, 'store']);

    Route::middleware('auth:sanctum')->group(function () {
        # Rota de logout
        Route::post('/logout', LogoutController::class);

        # Rotas do usuário
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/user', [UserController::class, 'show']);
        Route::put('/user/{id}', [UserController::class, 'update']);
        Route::delete('/user/{id}', [UserController::class, 'destroy']);

        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/{id}', [PostController::class, 'show']);
        Route::put('/posts/{id}', [PostController::class, 'update']);
        Route::delete('/posts/{id}', [PostController::class, 'destroy']);

        # comentarios
        Route::get('/comments', [CommentController::class, 'index']);
        Route::post('/comments/{id}/posts', [CommentController::class, 'store']);
        Route::get('/comments/{id}', [CommentController::class, 'show']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);
        # votação (up ou down vote) em um comentário ou post

        # Rotas de sessões do usuário
        Route::get('/user/sessions', [SessionController::class, 'list']);
        Route::delete('/user/sessions/{id}', [SessionController::class, 'destroy']);
    });
});
