<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Like;
use App\Models\Post;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Busca todos os usuários, ordenados do mais recente para o mais antigo
        $users = User::orderBy('id', 'desc')->get();

        // Coleta todos os posts desses usuários
        $posts = Post::whereIn('user_id', $users->pluck('id'))->get();

        // Coleta os likes agrupados por post_id
        $likes = Like::whereIn('post_id', $posts->pluck('id'))
            ->selectRaw('post_id, count(*) as nLikes')
            ->groupBy('post_id')
            ->get()
            ->keyBy('post_id');

        // Agrupa os likes por usuário
        $likesPorUsuario = [];

        foreach ($posts as $post) {
            $userId = $post->user_id;
            $nLikes = $likes[$post->id]->nLikes ?? 0;

            if (!isset($likesPorUsuario[$userId])) {
                $likesPorUsuario[$userId] = 0;
            }

            $likesPorUsuario[$userId] += $nLikes;
        }

        // Junta os dados por usuário
        $postagens = $users->map(function ($user) use ($likesPorUsuario) {
            return [
                'id' => $user->id,
                "@" . $user->username => [
                    'follows' => [
                        'seguidores' => $user->seguidores()->count(),
                        'seguindo' => $user->seguindo()->count(),
                    ],
                    'posts' => [
                        'posts' => $user->posts()->count(),
                        'likes' => $user->likesInMyPosts()->count(),
                    ],
                    'comments' => [
                        'comments' => $user->comments()->count(),
                        'likes' => $user->likesInMyComments()->count(),
                    ],
                ],
            ];
        });

        return response()->json($postagens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:users|max:255',
                'username' => 'required|unique:users|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
            ]);

            $user = User::create($validated);

            return response()->json([
                $user,
                'status' => true,
                'message' => 'Usuário criado com sucesso.',
                'access_token' => $user->createToken("LOGIN TOKEN")->plainTextToken,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Erro ao criar usuário.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        // Coleta os posts desse usuário
        $posts = Post::where('user_id', $user->id)->get();

        // Coleta os likes agrupados por post_id
        $likes = Like::whereIn('post_id', $posts->pluck('id'))
            ->selectRaw('post_id, count(*) as nLikes')
            ->groupBy('post_id')
            ->get()
            ->keyBy('post_id');

        // Soma os likes recebidos nos posts do usuário
        $nLikesPosts = 0;

        foreach ($posts as $post) {
            $nLikesPosts += $likes[$post->id]->nLikes ?? 0;
        }

        // Monta a estrutura de resposta
        $dadosUsuario = [
            'id' => $user->id,
            "@" . $user->username => [
                'follows' => [
                    'seguidores' => $user->seguidores()->count(),
                    'seguindo' => $user->seguindo()->count(),
                ],
                'posts' => [
                    'posts' => $user->posts()->count(),
                    'likes' => $user->likesInMyPosts()->count(),
                ],
                'comments' => [
                    'comments' => $user->comments()->count(),
                    'likes' => $user->likesInMyComments()->count(),
                ],
            ],
        ];

        return response()->json($dadosUsuario);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $nameOld = $user->name;
        $usernameOld = $user->username;
        $emailOld = $user->email;

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        $dadosUsuario = [
            'user_id' => $user->id,
            "dados" => [
                "antigos" => [
                    'name' => $nameOld,
                    'username' => $usernameOld,
                    'email' => $emailOld,
                ],
                "atualizados" => [
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                ],
            ],
            "@" . $user->username => [
                'follows' => [
                    'seguidores' => $user->seguidores()->count(),
                    'seguindo' => $user->seguindo()->count(),
                ],
                'posts' => [
                    'posts' => $user->posts()->count(),
                    'likes' => $user->likesInMyPosts()->count(),
                ],
                'comments' => [
                    'comments' => $user->comments()->count(),
                    'likes' => $user->likesInMyComments()->count(),
                ],
            ],
        ];

        return response()->json($dadosUsuario);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        Auth::user()->delete();
        return response(status: 204);
    }

}
