<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceVerificationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Routes pour la vérification des appareils
Route::post('/device/send-verification', [DeviceVerificationController::class, 'sendVerificationCode']);
Route::post('/device/verify-code', [DeviceVerificationController::class, 'verifyCode']);
Route::post('/device/check', [DeviceVerificationController::class, 'isDeviceVerified']);


Route::get("/test" , function()
{
    return response()->json(["message" => "salhijcedi"]);
});