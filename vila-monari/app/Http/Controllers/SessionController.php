<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function list(){
        return Auth::user()->tokens()->select([
            'id',
            'last_used_at',
            'expires_at',
            'created_at',
        ])->latest('id')->get();
    }

    public function destroy(Request $request, $id)
    {
        Auth::user()->tokens()->where('id', $id)->delete();
        return response(status: 204);
    }
}
