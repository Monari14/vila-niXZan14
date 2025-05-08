<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //desc: Maior pro Menor | asc: Menor pro Maior
        return Post::orderBy('id', 'desc')->get();
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
        if(! empty($request->username)) {
            $post->username = $request->username;
        }
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
        return Post::findOrFail($id);
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
    }
}
