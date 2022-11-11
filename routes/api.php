<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/* Auth Routes */
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::group(['prefix' => 'auth', 'middleware' => 'auth:sanctum'], function() {
    Route::post('logout', [AuthController::class, 'logout']);
});

/* Users API Routes */ // Add auth access when not authenticated
Route::middleware(['auth:sanctum'])->group(function() {
    Route::apiResource('users', UserController::class);
    Route::get('usersPreStoreData', [UserController::class, 'preStoreData']);
    Route::get('usersPreUpdateData/{id}', [UserController::class, 'preUpdateData']);
});