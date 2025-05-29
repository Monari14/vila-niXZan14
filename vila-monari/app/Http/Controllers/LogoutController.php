<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Auth::user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'User logged Out Successfully',
        ], 200);
    }
}
