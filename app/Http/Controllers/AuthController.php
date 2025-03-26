<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
            ]);

            // Check if user is created
            if (!$user) {
                return response()->json(['message' => 'User not created'], 500);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                "message" => "Register Successfully",
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function login(Request $request)
    {
        $key = 'login-attempts:' . Str::lower($request->input('email'));

        if (RateLimiter::tooManyAttempts($key, 10)) { // Max 5 attempts
            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' . RateLimiter::availableIn($key) . ' seconds.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($validatedData)) {
            RateLimiter::hit($key, 60); // Block for 60 seconds after limit
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        RateLimiter::clear($key); // Reset limit after successful login

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }



    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
