<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
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
        $likes = Like::whereIn('comment_id', $comments->pluck('id'))
            ->selectRaw('comment_id, count(*) as nLikes')
            ->groupBy('comment_id')
            ->get()
            ->keyBy('comment_id');

        $postagens = $comments->map(function ($post) use ($likes) {
            return [
                'comment' => $post,
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
            'comment' => $comment,
            'message' => 'Comentário adicionado com sucesso!'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Busca um único post
        $comment = Comment::findOrFail($id);

        // Conta os likes desse post
        $nLikes = Like::where('comment_id', $comment->id)->count();

        // Retorna os dados em estrutura JSON
        return response()->json([
            'comment' => $comment,
            'nLikes' => $nLikes,
        ]);
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
