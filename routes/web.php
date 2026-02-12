<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ActivationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Payments\DS_PaymentWebhookController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\DS_LandingPageController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    
    Route::get('activate/{token}', [ActivationController::class, 'activate'])->name('activate');

    Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Shared Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Email Verification Routes
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->middleware(['throttle:3,1'])->name('verification.resend');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::get('lang/{code}', [LanguageController::class, 'switch'])->name('language.switch');
    
    // Impersonation
    Route::post('stop-impersonating', [UserController::class, 'stopImpersonating'])->name('admin.stop-impersonating');
});

// Payment Webhooks (No Auth)
Route::post('/payments/webhook/{gateway}', [DS_PaymentWebhookController::class, 'handle'])->name('payments.webhook');
// Public Landing Pages
Route::match(['get', 'post'], '/lp/{slug}', [DS_LandingPageController::class, 'view'])->name('lp.view');
