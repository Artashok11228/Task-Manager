<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            $user = Auth::user();
            $token = $user->createToken('task-manager-token')->plainTextToken;
            return response()->json([
                'token' => $token,
            ], 200);
        }
        return response()->json([
            'message' => 'Unauthorized'
        ],401);
    }
}
