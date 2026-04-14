<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyCashClosureController;
use App\Http\Controllers\InitialSetupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SpecialCashOperationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified', 'initial.setup'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/initial-setup', [InitialSetupController::class, 'create'])->name('initial-setup.create');
    Route::post('/initial-setup', [InitialSetupController::class, 'store'])->name('initial-setup.store');
});

Route::middleware(['auth', 'initial.setup'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('sales-credit-accounts', [SaleController::class, 'creditAccounts'])->name('sales.credit-accounts');
    Route::post('sales/{sale}/settle-credit', [SaleController::class, 'settleCredit'])->name('sales.settle-credit');
    Route::resource('daily-cash-closures', DailyCashClosureController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('special-cash-operations', SpecialCashOperationController::class)->only(['index', 'create', 'store']);
    Route::resource('roles', RoleController::class)->only(['index', 'create', 'store', 'edit', 'update'])->middleware('can:roles.manage');
    Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update'])->middleware('can:users.manage');
});

require __DIR__.'/auth.php';
