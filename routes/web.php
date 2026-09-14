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
use App\Http\Controllers\Web\PaymentController;
use App\Http\Controllers\Web\PurchaseOrderController;
use App\Http\Controllers\Web\RationFormulaController;
use App\Http\Controllers\Web\SalesOrderController;
use App\Http\Controllers\Web\SupplierController;
use App\Http\Controllers\Web\WarehouseController;
use App\Http\Controllers\Web\WeighInController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [BatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [BatchController::class, 'store'])->name('batches.store');
    Route::get('/batches/{batch}', [BatchController::class, 'show'])->name('batches.show');

    Route::get('/batches/{batch}/animals/create', [AnimalController::class, 'create'])->name('animals.create');
    Route::post('/batches/{batch}/animals', [AnimalController::class, 'store'])->name('animals.store');
    Route::get('/animals/{animal}', [AnimalController::class, 'show'])->name('animals.show');

    Route::post('/animals/{animal}/weigh-ins', [WeighInController::class, 'store'])->name('weigh-ins.store');
    Route::post('/animals/{animal}/health-records', [HealthRecordController::class, 'store'])->name('health-records.store');
    Route::post('/animals/{animal}/movements', [AnimalMovementController::class, 'store'])->name('movements.store');
    Route::post('/batches/{batch}/feed-logs', [FeedLogController::class, 'store'])->name('feed-logs.store');
    Route::post('/batches/{batch}/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    Route::get('/feed-items', [FeedItemController::class, 'index'])->name('feed-items.index');
    Route::post('/feed-items', [FeedItemController::class, 'store'])->name('feed-items.store');

    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');

    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');

    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');

    Route::get('/ration-formulas', [RationFormulaController::class, 'index'])->name('ration-formulas.index');
    Route::get('/ration-formulas/create', [RationFormulaController::class, 'create'])->name('ration-formulas.create');
    Route::post('/ration-formulas', [RationFormulaController::class, 'store'])->name('ration-formulas.store');
    Route::get('/ration-formulas/{rationFormula}', [RationFormulaController::class, 'show'])->name('ration-formulas.show');

    Route::get('/sales-orders/create', [SalesOrderController::class, 'create'])->name('sales-orders.create');
    Route::post('/sales-orders', [SalesOrderController::class, 'store'])->name('sales-orders.store');
    Route::get('/sales-orders/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales-orders.show');
    Route::post('/sales-orders/{salesOrder}/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    Route::patch('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    Route::post('/purchase-orders/{purchaseOrder}/payments', [PaymentController::class, 'storeForPurchaseOrder'])->name('purchase-order-payments.store');
});
