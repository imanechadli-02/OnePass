<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerifiedDevice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Enregistrer automatiquement le premier appareil
        VerifiedDevice::create([
            'user_id' => $user->id,
            'device_id' => $request->device_id ?? null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
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

        $user = User::where('email', $request->email)->first();
        
        // Vérifier si l'appareil/IP est déjà vérifié
        $isVerifiedDevice = VerifiedDevice::where([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
        ])->exists();

        // Si ce n'est pas un appareil vérifié, on ne renvoie pas de token
        if (!$isVerifiedDevice) {
            return response()->json([
                'message' => 'Device verification required',
                'needs_verification' => true,
                'user_id' => $user->id,
                'email' => $user->email
            ], 200);
        }

        RateLimiter::clear($key); // Reset limit after successful login

        $token = auth()->user()->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login Successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
}
