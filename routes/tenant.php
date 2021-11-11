<?php

declare(strict_types=1);

use App\Http\Controllers\EventController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PINCodeController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Universal API routes - public.

Route::prefix(
    'api'
)->middleware([
    'api',
    'universal',
    InitializeTenancyByDomain::class,
])->group(function () {
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);
    // Route used to update pin code
    Route::put('/pincode/{uuid}', PINCodeController::class);
});

// Universal API routes - protected


// Universal routes - protected via sanctum
Route::prefix(
    'api'
)->middleware([
    'api',
    'universal',
    InitializeTenancyByDomain::class,
    'auth:sanctum'
])->group(function () {
    Route::apiResource('users', UserController::class)->except('store');
});

// Tenant API routes - protected via sanctum

Route::prefix(
    'api'
)->middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::apiResource('events', EventController::class);
    Route::post('users', [UserController::class, 'seed']);
});
