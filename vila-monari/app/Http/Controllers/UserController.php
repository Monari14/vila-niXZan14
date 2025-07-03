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
        try {
            $validated = $request->validate([
                'name' => 'required|unique:users|max:255',
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
    public function show()
    {
        $sim = Auth::user()->tokens()->all();

        return response()->json([
            'me' => $sim,
            'mama' => 'fodase',
        ]);

        // return Auth::user();
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
