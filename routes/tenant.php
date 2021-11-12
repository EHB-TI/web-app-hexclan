<?php

declare(strict_types=1);

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PINCodeController;
use App\Http\Controllers\TestController;
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
    Route::get('/test', TestController::class); // To be used for debugging purposes. 
    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);
    // Route used to update pin code
    Route::put('/pincode/{uuid}', PINCodeController::class);
});

// Universal routes - protected via sanctum.
Route::prefix(
    'api'
)->middleware([
    'api',
    'universal',
    InitializeTenancyByDomain::class,
    //'auth:sanctum'
])->group(function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'delete']);
});

// Tenant API routes - protected via sanctum.

Route::prefix(
    'api'
)->middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    //'auth:sanctum'
])->group(function () {
    Route::post('users', [UserController::class, 'seed']);

    Route::get('events', [EventController::class, 'index']);
    Route::post('events', [EventController::class, 'store']);
    Route::get('events/{event}', [EventController::class, 'show']);
    Route::put('events/{event}', [EventController::class, 'update']);
    Route::delete('events/{event}', [EventController::class, 'delete']);

    Route::get('bankaccounts', [BankAccountController::class, 'index']);
    Route::post('bankaccounts', [BankAccountController::class, 'store']);
    Route::get('bankaccounts/{bankaccount}', [BankAccountController::class, 'show']);
    Route::put('bankaccounts/{bankaccount}', [BankAccountController::class, 'update']);
    Route::delete('bankaccounts/{bankaccount}', [BankAccountController::class, 'delete']);
});
