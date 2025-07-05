<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class FollowController extends Controller
{
    public function follow(Request $request, $id)
    {
        $userToFollow = User::find($id);
        if (!$userToFollow) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $authUser = $request->user();

        // Impede seguir a si mesmo
        if ($authUser->id === $userToFollow->id) {
            return response()->json(['message' => 'Você não pode seguir a si mesmo.'], 400);
        }

        // Verifica se já está seguindo
        $alreadyFollowing = $authUser->seguindo()->where('followed_id', $id)->exists();

        if ($alreadyFollowing) {
            return response()->json(['message' => 'Você já segue este usuário.'], 400);
        }

        // Adiciona o relacionamento
        $authUser->seguindo()->attach($id);

        return response()->json(['message' => 'Usuário seguido com sucesso!']);
    }

    public function unfollow(Request $request, $id)
    {
        $userToUnfollow = User::find($id);
        if (!$userToUnfollow) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $authUser = $request->user();

        // Impede deixar de seguir a si mesmo
        if ($authUser->id === $userToUnfollow->id) {
            return response()->json(['message' => 'Você não pode deixar de seguir a si mesmo.'], 400);
        }

        // Verifica se já está seguindo
        $alreadyFollowing = $authUser->seguindo()->where('followed_id', $id)->exists();

        if (!$alreadyFollowing) {
            return response()->json(['message' => 'Você não segue este usuário.'], 400);
        }

        // Remove o relacionamento
        $authUser->seguindo()->detach($id);

        return response()->json(['message' => 'Você deixou de seguir este usuário.']);
    }

}
