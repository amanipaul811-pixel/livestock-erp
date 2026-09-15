<?php

use App\Http\Controllers\Web\AnimalController;
use App\Http\Controllers\Web\AnimalMovementController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\BatchController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ExpenseController;
use App\Http\Controllers\Web\FeedItemController;
use App\Http\Controllers\Web\FeedLogController;
use App\Http\Controllers\Web\HealthRecordController;
use App\Http\Controllers\Web\PasswordResetController;
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PurchaseOrderController;
use App\Http\Controllers\Web\RationFormulaController;
use App\Http\Controllers\Web\SalesOrderController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\WarehouseController;
use App\Http\Controllers\Web\WeighInController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:login');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// RBAC is enforced via the `permission:<code>` middleware, backed by
// $user->hasPermission('code') (see App\Http\Middleware\EnsurePermission).
// Every route with a matching seeded permission is gated -- including the
// GET create/edit routes, not just the POST/PUT/DELETE writes -- so a role
// without the permission is turned away before it can fill out a form it
// isn't allowed to submit, not just when it tries to submit one. Resources
// with no seeded permission (feed items, customers) stay open to any
// authenticated user. The nav additionally hides links a user can't act on,
// but that's a UX nicety on top of this -- this is what actually blocks it.
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create')->middleware('permission:batch.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store')->middleware('permission:batch.create');
    Route::get('/batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
    Route::get('/batches/{batch}/edit', [BatchController::class, 'edit'])->name('batches.edit')->middleware('permission:batch.update');
    Route::put('/batches/{batch}', [BatchController::class, 'update'])->name('batches.update')->middleware('permission:batch.update');

    Route::get('/batches/{batch}/animals/create', [AnimalController::class, 'create'])->name('animals.create')->middleware('permission:animal.create');
    Route::post('/batches/{batch}/animals', [AnimalController::class, 'store'])->name('animals.store')->middleware('permission:animal.create');
    Route::get('/animals/{animal}', [AnimalController::class, 'show'])->name('animals.show');

    Route::post('/animals/{animal}/weigh-ins', [WeighInController::class, 'store'])->name('weigh-ins.store')->middleware('permission:weighin.create');
    Route::post('/animals/{animal}/health-records', [HealthRecordController::class, 'store'])->name('health-records.store')->middleware('permission:healthrecord.create');
    Route::post('/animals/{animal}/movements', [AnimalMovementController::class, 'store'])->name('movements.store')->middleware('permission:animalmovement.create');
    Route::post('/batches/{batch}/feed-logs', [FeedLogController::class, 'store'])->name('feed-logs.store')->middleware('permission:feedlog.create');
    Route::post('/batches/{batch}/expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('permission:expense.create');

    Route::get('/feed-items', [FeedItemController::class, 'index'])->name('feed-items.index');
    Route::post('/feed-items', [FeedItemController::class, 'store'])->name('feed-items.store');
    Route::get('/feed-items/{feedItem}/edit', [FeedItemController::class, 'edit'])->name('feed-items.edit');
    Route::put('/feed-items/{feedItem}', [FeedItemController::class, 'update'])->name('feed-items.update');
    Route::delete('/feed-items/{feedItem}', [FeedItemController::class, 'destroy'])->name('feed-items.destroy');
    Route::post('/feed-items/{feedItem}/restock', [FeedItemController::class, 'restock'])->name('feed-items.restock');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store')->middleware('permission:supplier.create');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit')->middleware('permission:supplier.create');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update')->middleware('permission:supplier.create');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy')->middleware('permission:supplier.create');

    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store')->middleware('permission:warehouse.create');
    Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit')->middleware('permission:warehouse.create');
    Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update')->middleware('permission:warehouse.create');
    Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy')->middleware('permission:warehouse.create');

    Route::get('/ration-formulas', [RationFormulaController::class, 'index'])->name('ration-formulas.index');
    Route::get('/ration-formulas/create', [RationFormulaController::class, 'create'])->name('ration-formulas.create')->middleware('permission:rationformula.create');
    Route::post('/ration-formulas', [RationFormulaController::class, 'store'])->name('ration-formulas.store')->middleware('permission:rationformula.create');
    Route::get('/ration-formulas/{rationFormula}', [RationFormulaController::class, 'show'])->name('ration-formulas.show');
    Route::get('/ration-formulas/{rationFormula}/edit', [RationFormulaController::class, 'edit'])->name('ration-formulas.edit')->middleware('permission:rationformula.create');
    Route::put('/ration-formulas/{rationFormula}', [RationFormulaController::class, 'update'])->name('ration-formulas.update')->middleware('permission:rationformula.create');
    Route::delete('/ration-formulas/{rationFormula}', [RationFormulaController::class, 'destroy'])->name('ration-formulas.destroy')->middleware('permission:rationformula.create');

    Route::get('/sales-orders/create', [SalesOrderController::class, 'create'])->name('sales-orders.create')->middleware('permission:salesorder.create');
    Route::post('/sales-orders', [SalesOrderController::class, 'store'])->name('sales-orders.store')->middleware('permission:salesorder.create');
    Route::get('/sales-orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales-orders.show');
    Route::post('/sales-orders/{salesOrder}/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create')->middleware('permission:purchaseorder.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store')->middleware('permission:purchaseorder.create');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::patch('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status')->middleware('permission:purchaseorder.update');
    Route::post('/purchase-orders/{purchaseOrder}/payments', [PaymentController::class, 'storeForPurchaseOrder'])->name('purchase-order-payments.store');

    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:user.manage');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:user.manage');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:user.manage');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:user.manage');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:user.manage');
});
