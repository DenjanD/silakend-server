<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\JobUnitController;
use App\Http\Controllers\UsageCategoryController;
use App\Http\Controllers\VehicleCategoryController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\VehicleUsageController;
use App\Http\Controllers\VehicleMaintenanceController;
use App\Http\Controllers\VehicleMaintenanceDetailController;
use App\Http\Controllers\UserRoleController;

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
Route::group(['prefix' => 'auth'], function() {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::middleware(['auth:sanctum'])->group(function() {
        Route::post('logout', [AuthController::class, 'logout']);  
    });
});

/* Role API Routes */
Route::middleware(['auth:sanctum','superadmin'])->group(function() {
    Route::apiResource('roles', RoleController::class);
});

/* Job Unit API Routes */
Route::middleware(['auth:sanctum','superadmin'])->group(function() {
    Route::apiResource('jobunits', JobUnitController::class);
});

/* Usage Categories API Routes */
Route::middleware(['auth:sanctum','superadmin','validator'])->group(function() {
    Route::apiResource('usagecategories', UsageCategoryController::class);
});

/* Vehicle Categories API Routes */
Route::middleware(['auth:sanctum','superadmin','validator'])->group(function() {
    Route::apiResource('vehiclecategories', VehicleCategoryController::class);
});

/* User Roles API Routes */
Route::middleware(['auth:sanctum','superadmin'])->group(function() {
    Route::apiResource('userroles', UserRoleController::class);
});

/* Users API Routes */
Route::middleware(['auth:sanctum'])->group(function() {
    Route::apiResource('users', UserController::class);
    Route::get('usersPreStoreData', [UserController::class, 'preStoreData'])->middleware('superadmin');
    Route::get('usersPreUpdateData/{id}', [UserController::class, 'preUpdateData'])->middleware('superadmin');
});

/* Vehicles API Routes */
Route::middleware(['auth:sanctum','superadmin','validator','driver'])->group(function() {
    Route::apiResource('vehicles', VehicleController::class);
});

/* Vehicle Usages API Routes */
Route::middleware(['auth:sanctum'])->group(function() {
    Route::apiResource('vehicleusages', VehicleUsageController::class);
});

/* Vehicle Maintenances API Routes */
Route::middleware(['auth:sanctum','superadmin','validator'])->group(function() {
    Route::apiResource('vehiclemaintenances', VehicleMaintenanceController::class);
});

/* Vehicle Maintenance Details API Routes */
Route::middleware(['auth:sanctum','superadmin','validator'])->group(function() {
    Route::apiResource('vehiclemaintenancedetails', VehicleMaintenanceDetailController::class);
});