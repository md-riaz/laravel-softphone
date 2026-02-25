<?php

use App\Http\Controllers\AgentConsoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallAnalyticController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispositionController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\PbxConnectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('companies', CompanyController::class);
        Route::resource('pbx-connections', PbxConnectionController::class);
        Route::resource('extensions', ExtensionController::class);
        Route::resource('dispositions', DispositionController::class);
        Route::get('analytics', [CallAnalyticController::class, 'index'])->name('analytics.index');
        Route::get('reports', [CallAnalyticController::class, 'reports'])->name('reports.index');
    });

    // Agent routes
    Route::middleware('role:admin,agent')->prefix('agent')->name('agent.')->group(function () {
        Route::get('console', [AgentConsoleController::class, 'index'])->name('console');
        Route::get('call-history', [CallController::class, 'history'])->name('call-history');
    });

    // Internal API (same-origin, session auth)
    Route::prefix('internal')->name('internal.')->group(function () {
        Route::get('me', [AuthController::class, 'me'])->name('me');
        Route::get('extensions', [ExtensionController::class, 'myExtensions'])->name('extensions');
        Route::get('extensions/{extension}/sip-credentials', [ExtensionController::class, 'sipCredentials'])->name('extensions.sip-credentials');
        Route::post('extensions/{extension}/activate', [ExtensionController::class, 'activate'])->name('extensions.activate');
        Route::post('extensions/{extension}/deactivate', [ExtensionController::class, 'deactivate'])->name('extensions.deactivate');
        Route::post('calls', [CallController::class, 'store'])->name('calls.store');
        Route::post('calls/{call}/answered', [CallController::class, 'answered'])->name('calls.answered');
        Route::post('calls/{call}/ended', [CallController::class, 'ended'])->name('calls.ended');
        Route::post('calls/{call}/wrapup', [CallController::class, 'wrapup'])->name('calls.wrapup');
        Route::get('calls', [CallController::class, 'index'])->name('calls.index');
        Route::get('dispositions', [DispositionController::class, 'list'])->name('dispositions.list');
        Route::get('analytics/summary', [CallAnalyticController::class, 'summary'])->name('analytics.summary');
    });
});
