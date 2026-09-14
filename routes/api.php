<?php

use App\Http\Controllers\Api\AnimalController;
use App\Http\Controllers\Api\AnimalMovementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\FeedLogController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\RationFormulaController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\WeighInController;
use Illuminate\Support\Facades\Route;

// RBAC is enforced via the `permission:<code>` middleware on write routes below,
// backed by $user->hasPermission('code') (see App\Http\Middleware\EnsurePermission).
// Read routes stay open to any authenticated user.

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // --- Auth ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:dashboard.view');

    // --- Batches ---
    Route::get('/batches', [BatchController::class, 'index']);
    Route::post('/batches', [BatchController::class, 'store'])->middleware('permission:batch.create');
    Route::get('/batches/{batch}', [BatchController::class, 'show']);
    Route::patch('/batches/{batch}', [BatchController::class, 'update'])->middleware('permission:batch.update');
    Route::get('/batches/{batch}/profitability', [BatchController::class, 'profitability']);

    // --- Animals (nested under batches for intake/listing, flat for detail) ---
    Route::get('/batches/{batch}/animals', [AnimalController::class, 'index']);
    Route::post('/animals', [AnimalController::class, 'store'])->middleware('permission:animal.create');
    Route::get('/animals/{animal}', [AnimalController::class, 'show']);
    Route::get('/animals/ready-to-sell', [AnimalController::class, 'readyToSell']);

    // --- Weigh-ins (growth tracking) ---
    Route::get('/animals/{animal}/weigh-ins', [WeighInController::class, 'index']);
    Route::post('/animals/{animal}/weigh-ins', [WeighInController::class, 'store'])->middleware('permission:weighin.create');

    // --- Animal movements (pen transfers) ---
    Route::get('/animals/{animal}/movements', [AnimalMovementController::class, 'index']);
    Route::post('/animals/{animal}/movements', [AnimalMovementController::class, 'store'])->middleware('permission:animalmovement.create');

    // --- Feed logs (per batch/pen) ---
    Route::get('/batches/{batch}/feed-logs', [FeedLogController::class, 'index']);
    Route::post('/batches/{batch}/feed-logs', [FeedLogController::class, 'store'])->middleware('permission:feedlog.create');

    // --- Health records ---
    Route::get('/animals/{animal}/health-records', [HealthRecordController::class, 'index']);
    Route::post('/animals/{animal}/health-records', [HealthRecordController::class, 'store'])->middleware('permission:healthrecord.create');

    // --- Expenses (batch overhead) ---
    Route::get('/batches/{batch}/expenses', [ExpenseController::class, 'index']);
    Route::post('/batches/{batch}/expenses', [ExpenseController::class, 'store'])->middleware('permission:expense.create');

    // --- Sales & settlement ---
    Route::post('/sales-orders', [SalesOrderController::class, 'store'])->middleware('permission:salesorder.create');
    Route::get('/sales-orders/{salesOrder}', [SalesOrderController::class, 'show']);

    // --- Payments (against a sales order) ---
    Route::get('/sales-orders/{salesOrder}/payments', [PaymentController::class, 'index']);
    Route::post('/sales-orders/{salesOrder}/payments', [PaymentController::class, 'store']);

    // --- Suppliers & warehouses ---
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::post('/suppliers', [SupplierController::class, 'store'])->middleware('permission:supplier.create');
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::post('/warehouses', [WarehouseController::class, 'store'])->middleware('permission:warehouse.create');

    // --- Ration formulas ---
    Route::get('/ration-formulas', [RationFormulaController::class, 'index']);
    Route::post('/ration-formulas', [RationFormulaController::class, 'store'])->middleware('permission:rationformula.create');
    Route::get('/ration-formulas/{rationFormula}', [RationFormulaController::class, 'show']);

    // --- Purchase orders ---
    Route::get('/purchase-orders', [PurchaseOrderController::class, 'index']);
    Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->middleware('permission:purchaseorder.create');
    Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show']);
    Route::patch('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->middleware('permission:purchaseorder.update');

    // --- Payments (against a purchase order) ---
    Route::get('/purchase-orders/{purchaseOrder}/payments', [PaymentController::class, 'indexForPurchaseOrder']);
    Route::post('/purchase-orders/{purchaseOrder}/payments', [PaymentController::class, 'storeForPurchaseOrder']);
});
