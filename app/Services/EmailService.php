<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService
{
    public function sendVerificationCode($email, $code)
    {
        // Dans un environnement réel, vous utiliserez Mail::send() ou Mail::to()
        // Pour simplifier, nous allons simplement simuler l'envoi d'un email
        
        // Exemple:
        Mail::to($email)->send(new \App\Mail\VerificationCode($code));
        
        // Log pour debug
        Log::info("Code de vérification envoyé à $email: $code");
        
        return true;
    }
}
