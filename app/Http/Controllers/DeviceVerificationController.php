<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VerificationCode;
use App\Models\VerifiedDevice;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceVerificationController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function sendVerificationCode(Request $request){
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        // Générer un code aléatoire de 6 chiffres
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Créer ou mettre à jour le code de vérification
        VerificationCode::updateOrCreate(
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
            ],
            [
                'code' => $code,
                'device_id' => $request->device_id ?? Str::uuid(),
                'expires_at' => now()->addMinutes(15),
            ]
        );
        
        // Envoyer le code par email
        $this->emailService->sendVerificationCode($user->email, $code);
        
        return response()->json(['message' => 'Verification code sent', 'email' => $user->email]);
    }
    
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'device_id' => 'nullable|string',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $verificationCode = VerificationCode::where([
            'user_id' => $user->id,
            'code' => $request->code,
            'ip_address' => $request->ip(),
        ])->first();
        
        if (!$verificationCode || !$verificationCode->isValid()) {
            return response()->json(['message' => 'Invalid or expired code'], 400);
        }
        
        // Enregistrer l'appareil comme vérifié
        VerifiedDevice::create([
            'user_id' => $user->id,
            'device_id' => $verificationCode->device_id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        
        // Supprimer le code de vérification utilisé
        $verificationCode->delete();
        
        // Connecter l'utilisateur
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'message' => 'Device verified',
            'token' => $token,
            'user' => $user
        ]);
    }
    
    public function isDeviceVerified(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $verifiedDevice = VerifiedDevice::where([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
        ])->first();
        
        return response()->json([
            'verified' => $verifiedDevice ? true : false
        ]);
    }
}
