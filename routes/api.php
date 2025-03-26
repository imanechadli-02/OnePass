<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DeviceVerificationController;
use App\Http\Middleware\IpRestrictionMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\IpManagementController;
use App\Http\Controllers\PasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Blacklist routes (Only Admins)
Route::middleware(['auth:sanctum', AdminMiddleware::class])->group(function () {

    Route::post('/ip/blacklist', [IpManagementController::class, 'addToBlacklist']);
    Route::delete('/ip/blacklist/{ip}', [IpManagementController::class, 'removeFromBlacklist']);
    Route::get('/blacklist', [IpManagementController::class, 'listBlacklist']);
});

// Whitelist routes (IP Restriction)
Route::middleware(['auth:sanctum',IpRestrictionMiddleware::class])->group(function () {
    Route::delete('/ip/whitelist/{ip}', [IpManagementController::class, 'removeFromWhitelist']);
    Route::get('/whitelist', [IpManagementController::class, 'listWhitelist']);
});

Route::post('/ip/whitelist', [IpManagementController::class, 'addToWhitelist']);

// Public routes
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
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource("passwords", PasswordController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
});



