<?php

declare(strict_types=1);

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PINCodeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TestGetController;
use App\Http\Controllers\TestPostController;
use App\Http\Controllers\TokenController;
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

// Universal API routes - no auth.

Route::prefix(
    'api'
)->middleware([
    'api',
    'universal',
    InitializeTenancyByDomain::class,
])->group(function () {
    Route::get('/test', TestGetController::class); // To be used for debugging purposes. 
    Route::post('/test', TestPostController::class); // To be used for debugging purposes.

    Route::post('/register', RegisterController::class);
    Route::post('/login', LoginController::class);
    Route::put('/pincode/{uuid}', PINCodeController::class); // Route used to update pin code

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
});

// Universal API routes - auth.

Route::prefix(
    'api'
)->middleware([
    'api',
    'universal',
    InitializeTenancyByDomain::class,
    //'auth:sanctum'
])->group(function () {
    Route::get('/sanctum/token/refresh', [TokenController::class, 'refresh']);

    Route::get('users', [UserController::class, 'index']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);
});

// Tenant API routes - auth.

Route::prefix(
    'api'
)->middleware([
    'api',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    //'auth:sanctum'
])->group(function () {
    Route::post('/sanctum/token/sync', [TokenController::class, 'sync']);

    Route::post('users', [UserController::class, 'seed']);
    //Route::post('users/{user}', [UserController::class, 'toggleIsActive']);

    // There routes are used to attach, update, and detach roles on the pivot table.
    Route::post('events/{event}/users', [EventUserController::class, 'store']);
    Route::put('events/{event}/users/{user}', [EventUserController::class, 'update']);
    Route::delete('events/{event}/users/{user}', [EventUserController::class, 'destroy']);

    Route::get('events', [EventController::class, 'index']);
    Route::post('events', [EventController::class, 'store']);
    Route::get('events/{event}', [EventController::class, 'show']);
    Route::put('events/{event}', [EventController::class, 'update']);
    Route::delete('events/{event}', [EventController::class, 'destroy']);

    Route::get('bankaccounts', [BankAccountController::class, 'index']);
    Route::post('bankaccounts', [BankAccountController::class, 'store']);
    Route::get('bankaccounts/{bankaccount}', [BankAccountController::class, 'show']);
    Route::put('bankaccounts/{bankaccount}', [BankAccountController::class, 'update']);
    Route::delete('bankaccounts/{bankaccount}', [BankAccountController::class, 'destroy']);
});
