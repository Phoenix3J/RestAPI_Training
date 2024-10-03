<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthJWTController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Test API
Route::get('/test-api', function () {
    return response()->json([
        'status' => 200,
        'message' => 'hai, test api nih',
    ]);
})->middleware(['auth:api', 'snap-bi']);

// Endpoint API Product
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::post('products', [ProductController::class, 'store']);
Route::put('products/{id}', [ProductController::class, 'update']);
Route::delete('products/{id}', [ProductController::class, 'destroy']);

//Auth Sanctum
Route::post('users/register', [AuthController::class, 'register']);
Route::post('users/login', [AuthController::class, 'login']);

//middleware auth:sanctum group
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('users/user', [AuthController::class, 'user']);

    Route::post('users/logout', [AuthController::class, 'logout']);
});


//JWT Auth
Route::post('jwt/register', [AuthJWTController::class, 'register']);
Route::post('jwt/login', [AuthJWTController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::get('jwt/profile', [AuthJWTController::class, 'profile']);
    Route::post('jwt/refresh', [AuthJWTController::class, 'refresh']);
    Route::post('jwt/logout', [AuthJWTController::class, 'logout']);
});
