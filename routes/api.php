<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierController;
use App\Http\Controllers\Api\ExceptionController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ShipmentController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Authentication API Endpoints
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('api.login');

/*
|--------------------------------------------------------------------------
| Protected Sanctum API Endpoints
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    // Current User Profile & Revocation
    Route::get('/user', [AuthController::class, 'me'])->name('api.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    // Shipments Resource
    Route::apiResource('shipments', ShipmentController::class)->names('api.shipments');

    // Tracking Milestones & Telemetry
    Route::get('/shipments/{shipment}/tracking', [TrackingController::class, 'index'])->name('api.shipments.tracking.index');
    Route::post('/shipments/{shipment}/tracking', [TrackingController::class, 'store'])->name('api.shipments.tracking.store');

    // Master Data: Carriers
    Route::get('/carriers', [CarrierController::class, 'index'])->name('api.carriers.index');
    Route::post('/carriers', [CarrierController::class, 'store'])->name('api.carriers.store');

    // Master Data: Locations
    Route::get('/locations', [LocationController::class, 'index'])->name('api.locations.index');
    Route::post('/locations', [LocationController::class, 'store'])->name('api.locations.store');

    // Shipment Exceptions Management
    Route::get('/shipments/{shipment}/exceptions', [ExceptionController::class, 'index'])->name('api.shipments.exceptions.index');
    Route::post('/shipments/{shipment}/exceptions', [ExceptionController::class, 'store'])->name('api.shipments.exceptions.store');
    Route::patch('/exceptions/{exception}', [ExceptionController::class, 'update'])->name('api.exceptions.update');
});
