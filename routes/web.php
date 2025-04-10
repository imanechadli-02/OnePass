<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Auth;

// Basic authentication routes (without Laravel UI)
Route::get('/login', function() {
    return view('auth.login');
})->name('login');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', function() {
    return view('auth.register');
})->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

Route::middleware(['auth'])->group(function () {
    // Dashboard route
    Route::resource('passwords', PasswordController::class);
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Password management routes
    
    // Route for updating last used timestamp
    Route::put('/passwords/{password}/update-last-used', [PasswordController::class, 'updateLastUsed'])
        ->name('passwords.updateLastUsed');
});

// Redirect root to login or dashboard based on auth status
Route::get('/', function () {
    return auth()->check() ? redirect()->route('home') : redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
