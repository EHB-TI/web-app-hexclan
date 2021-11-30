<?php

use App\Http\Controllers\TenantController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Central API routes -auth.

Route::middleware([
    'api',
    'auth:sanctum',
    'ability:*'
])->group(function () {
    Route::apiResource('tenants', TenantController::class);
});

Route::fallback(function () {
    return response()->json(['message' => 'This route does not exist.'], Response::HTTP_NOT_FOUND);
});
