<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $alreadyFollow = $user->likes()
            ->where('user_id', $request->user()->id)
            ->whereNull('comment_id') // Garante que não confunda com curtida em comentário
            ->exists();

        if ($alreadyLiked) {
            return response()->json(['message' => 'Você já curtiu este post.'], 400);
        }

        $post->likes()->create([
            'user_id' => $request->user()->id,
            'comment_id' => null,
        ]);

        return response()->json(['message' => 'Post curtido!']);
    } return response()->json(['message' => 'Usuário seguido com sucesso.']);
    }

    public function unfollow($id)
    {
        // Lógica para deixar de seguir um usuário
        return response()->json(['message' => 'Usuário deixado de seguir com sucesso.']);
    }
}
