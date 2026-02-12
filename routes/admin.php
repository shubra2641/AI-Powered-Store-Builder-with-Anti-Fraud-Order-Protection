<?php

use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AIKeyController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\DS_PaymentController;
use App\Http\Controllers\Admin\DS_PageController;
use App\Http\Controllers\Admin\DS_LandingPageController;
use App\Http\Controllers\Admin\DS_TransactionController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DS_IntegrationController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" and "auth"/"admin-access" middleware.
|
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Language Management
Route::post('languages/bulk-delete', [LanguageController::class, 'bulkDelete'])->name('languages.bulk-delete');
Route::post('languages/{language}/default', [LanguageController::class, 'setDefault'])->name('languages.set-default');
Route::resource('languages', LanguageController::class)->except(['show', 'create', 'edit']);

// User Management
Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
Route::post('users/{user}/add-credit', [UserController::class, 'addCredit'])->name('users.add-credit');
Route::post('users/{user}/verify', [UserController::class, 'verifyEmail'])->name('users.verify');
Route::post('users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
Route::get('transactions', [DS_TransactionController::class, 'index'])->name('transactions.index');
Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);

// Notifications
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
Route::delete('notifications/destroy-all', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

// Email Templates
Route::get('emails/send', [EmailTemplateController::class, 'showSendForm'])->name('emails.send-form');
Route::post('emails/send', [EmailTemplateController::class, 'sendBulk'])->name('emails.send-bulk');
Route::resource('emails', EmailTemplateController::class)->except(['show']);

Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
Route::post('settings/test-smtp', [SettingsController::class, 'testSmtp'])->name('settings.test-smtp');

// AI API Keys
Route::post('ai-keys/{aiKey}/activate', [AIKeyController::class, 'activate'])->name('ai-keys.activate');
Route::post('ai-keys/{aiKey}/test', [AIKeyController::class, 'test'])->name('ai-keys.test');
Route::resource('ai-keys', AIKeyController::class)->except(['index', 'show', 'create', 'edit']);

// Legal Pages
Route::resource('pages', DS_PageController::class)->except(['show']);

// Plan Management
Route::post('plans/{plan}/toggle', [PlanController::class, 'toggleStatus'])->name('plans.toggle');
Route::post('plans/{plan}/default', [PlanController::class, 'setDefault'])->name('plans.set-default');
Route::resource('plans', PlanController::class)->except(['show', 'create', 'edit']);

// Payment Gateways
Route::get('payments', [DS_PaymentController::class, 'index'])->name('payments.index');
Route::post('payments', [DS_PaymentController::class, 'store'])->name('payments.store');
Route::get('payments/{gateway}/data', [DS_PaymentController::class, 'getData'])->name('payments.data');
Route::get('payments/{gateway}/edit', [DS_PaymentController::class, 'edit'])->name('payments.edit');
Route::put('payments/{gateway}', [DS_PaymentController::class, 'update'])->name('payments.update');
Route::delete('payments/{gateway}', [DS_PaymentController::class, 'destroy'])->name('payments.destroy');
Route::post('payments/{gateway}/toggle', [DS_PaymentController::class, 'toggleStatus'])->name('payments.toggle');

// Landing Page Builder
Route::post('landing-pages/upload-media', [DS_LandingPageController::class, 'uploadMedia'])->name('landing-pages.upload-media');
Route::post('landing-pages/generate', [DS_LandingPageController::class, 'generate'])->name('landing-pages.generate');
Route::post('landing-pages/{landing_page}/generate-ajax', [DS_LandingPageController::class, 'generateAjax'])->name('landing-pages.generate-ajax');
Route::get('landing-pages/{landing_page}/builder', [DS_LandingPageController::class, 'builder'])->name('landing-pages.builder');
Route::post('landing-pages/{landing_page}/save', [DS_LandingPageController::class, 'save'])->name('landing-pages.save');
Route::get('landing-pages/{landing_page}/export', [DS_LandingPageController::class, 'export'])->name('landing-pages.export');
Route::resource('landing-pages', DS_LandingPageController::class)->except(['show', 'create', 'edit']);

// Integrations
Route::get('/integrations', [DS_IntegrationController::class, 'index'])->name('integrations.index');
Route::post('/integrations/toggle', [DS_IntegrationController::class, 'toggle'])->name('integrations.toggle');
Route::post('/integrations/update', [DS_IntegrationController::class, 'update'])->name('integrations.update');
