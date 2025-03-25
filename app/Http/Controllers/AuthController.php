<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
class AuthController extends Controller
{
    public function Register(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            "message"=> "Register Succefuly",
           'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(Request $request){
        $validatedData = $request->validate([
            "message"=> "Login Succefuly",
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        
        if (!auth()->attempt($validatedData)) {
            return response()->json(['message' => 'Invalid login Infos'], 401);
        }

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
    
        return response()->json(['message' => 'Logged out successfully']);
    }
}