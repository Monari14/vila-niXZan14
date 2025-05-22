<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return User::orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        return User::create($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        return Auth::user();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        return Auth::user()->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(): void
    {
        Auth::user()->delete();
    }
}
