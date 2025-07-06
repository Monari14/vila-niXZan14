<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\VotateController;

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
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::put('/user', [UserController::class, 'update']);
        Route::delete('/user', [UserController::class, 'destroy']);

        # posts
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::get('/posts/{id}', [PostController::class, 'show']);
        Route::put('/posts/{id}', [PostController::class, 'update']);
        Route::delete('/posts/{id}', [PostController::class, 'destroy']);

        # comentarios
        Route::get('/comments', [CommentController::class, 'index']);
        Route::post('/comments/{id}/posts', [CommentController::class, 'store']);
        Route::get('/comments/{id}', [CommentController::class, 'show']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

        # like e dislike em Posts
        Route::post('/posts/{id}/like', [VotateController::class, 'likePost']);
        Route::post('/posts/{id}/dislike', [VotateController::class, 'dislikePost']);

        # like e dislike em Comentários
        Route::post('/comments/{id}/like', [VotateController::class, 'likeComment']);
        Route::post('/comments/{id}/dislike', [VotateController::class, 'dislikeComment']);

        # Rotas de sessões do usuário
        Route::get('/user/sessions', [SessionController::class, 'list']);
        Route::delete('/user/sessions/{id}', [SessionController::class, 'destroy']);

        # follow e unfollow
        Route::post('/user/{id}/follow', [FollowController::class, 'follow']);
        Route::post('/user/{id}/unfollow', [FollowController::class, 'unfollow']);
    });
});
