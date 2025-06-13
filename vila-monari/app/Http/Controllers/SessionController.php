<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use function Laravel\Prompts\select;

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
        return Auth::user()->tokens()->where('id', $id)->delete();
    }
}
