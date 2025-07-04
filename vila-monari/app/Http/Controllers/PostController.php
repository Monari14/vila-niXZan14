<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Auth;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ordena os posts do mais recente para o mais antigo
        $posts = Post::orderBy('id', 'desc')->get();

        // Pega os likes agrupados por post_id
        $likes = Like::whereIn('post_id', $posts->pluck('id'))
            ->selectRaw('post_id, count(*) as nLikes')
            ->groupBy('post_id')
            ->get()
            ->keyBy('post_id');

        // Junta os dados em uma única estrutura
        $postagens = $posts->map(function ($post) use ($likes) {
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
    public function store(Request $request)
    {
        $image_path = null;
        if($request->file('image')) {
            $image_path = $request->file('image')->store('images');
        }

        $post = new Post();
        if(! empty($request->content)) {
            $post->content = $request->content;
        }

        $post->user_id = Auth::guard('sanctum')->id();

        if(! empty($image_path)) {
            $post->image = $image_path;
        }
        $post->save();
        return $post;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Busca um único post
        $post = Post::findOrFail($id);

        // Conta os likes desse post
        $nLikes = Like::where('post_id', $post->id)->count();

        // Retorna os dados em estrutura JSON
        return response()->json([
            'post' => $post,
            'nLikes' => $nLikes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);
        $post->content = $request->content;
        $post->save();
        return $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json([
            'message' => 'Post deletado com sucesso!'
        ]);
    }
}
