<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        if(!Auth::attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Email or Password does no match.',
            ], 401);
        }
        return response()->json([
            'status' => true,
            'message' => 'User Logged In Successfully',
            'access_token' => Auth::user()->createToken("LOGIN TOKEN")->plainTextToken,
        ], 200);
    }
}
