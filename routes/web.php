<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentExceptionController;
use App\Http\Controllers\TrackingEventController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::match(['get', 'post'], '/locale/{locale}', [LocaleController::class, 'setLocale'])
    ->name('locale.set');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Central dashboard redirector
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Administrator Dedicated Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Logistics Operator Dedicated Routes
    Route::middleware('role:operator')->prefix('operator')->name('operator.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'operator'])->name('dashboard');
    });

    // Customer Dedicated Routes
    Route::middleware('role:customer')->prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');
    });

    // In-App Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Master Data & Shipments Resource Routes (Protected via Model Policies)
    Route::resource('carriers', CarrierController::class);
    Route::post('/locations/geocode', [LocationController::class, 'geocode'])->name('locations.geocode');
    Route::resource('locations', LocationController::class);
    Route::resource('shipments', ShipmentController::class);
    Route::post('/shipments/{shipment}/events', [TrackingEventController::class, 'store'])->name('shipments.events.store');
    Route::post('/shipments/{shipment}/exceptions', [ShipmentExceptionController::class, 'store'])->name('shipments.exceptions.store');
    Route::patch('/exceptions/{exception}/status', [ShipmentExceptionController::class, 'update'])->name('exceptions.status.update');
});
