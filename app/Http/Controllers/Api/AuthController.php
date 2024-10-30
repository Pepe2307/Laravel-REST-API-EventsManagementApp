<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login( Request $request ){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user){
            throw validation_exception::withMessages([
                'email' => 'The provided credentials are incorrect.'
            ]);
        }

        if (!Hash::check($request->password, $user->password)){
            throw validation_exception::withMessages([
                'email' => 'The provided credentials are incorrect.'
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);

    }

    public function logout( Request $request ){

    }
}