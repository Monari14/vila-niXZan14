<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class VotateController extends Controller
{
    public function likePost(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post não encontrado.'], 404);
        }

        $alreadyLiked = $post->likes()->where('user_id', $request->user()->id)->exists();

        if ($alreadyLiked) {
            return response()->json(['message' => 'Você já curtiu este post.'], 400);
        }

        $post->likes()->create([
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Post curtido!']);
    }

    public function dislikePost(Request $request, $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Post não encontrado.'], 404);
        }

        $like = $post->likes()->where('user_id', $request->user()->id)->first();

        if (!$like) {
            return response()->json(['message' => 'Você não curtiu este post.'], 400);
        }

        $like->delete();

        return response()->json(['message' => 'Curtida removida.']);
    }

    public function likeComment(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        $alreadyLiked = $comment->likes()->where('user_id', $request->user()->id)->exists();

        if ($alreadyLiked) {
            return response()->json(['message' => 'Você já curtiu este comentário.'], 400);
        }

        $comment->likes()->create([
            'user_id' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Comentário curtido!']);
    }

    public function dislikeComment(Request $request, $id)
    {
        $comment = Comment::find($id);
        if (!$comment) {
            return response()->json(['message' => 'Comentário não encontrado.'], 404);
        }

        $like = $comment->likes()->where('user_id', $request->user()->id)->first();

        if (!$like) {
            return response()->json(['message' => 'Você não curtiu este comentário.'], 400);
        }

        $like->delete();

        return response()->json(['message' => 'Curtida removida.']);
    }
}
