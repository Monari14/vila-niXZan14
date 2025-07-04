<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\LikeComment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        // Ordena os posts do mais recente para o mais antigo
        $comments = Comment::orderBy('id', 'desc')->get();

        // Pega os likes agrupados por post_id
        $likes = LikeComment::whereIn('comment_id', $comments->pluck('id'))
            ->selectRaw('comment_id, count(*) as nLikes')
            ->groupBy('comment_id')
            ->get()
            ->keyBy('comment_id');

        $postagens = $comments->map(function ($post) use ($likes) {
            return [
                'post' => $post,
                'nLikes' => $likes[$post->id]->nLikes ?? 0,
            ];
        });

        return response()->json($postagens);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post não encontrado.'], 404);
        }

        $comment = Comment::create([
            'user_id' => $request->user()->id,
            'post_id' => $post->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comentário adicionado com sucesso!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Post::findOrFail($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();
        return response()->json([
            'message' => 'Comentário removido com sucesso!'
        ]);
    }
}
